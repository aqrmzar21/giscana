/**
 * ajax-router.js — Robust PJAX-like partial page loader untuk Giscana
 *
 * Mencegah loop reload & mengestrak #page-content / #pjax-content-wrapper secara cerdas.
 */

const PJAX = (() => {
    let isLoading = false;
    let progressTimer = null;
    let currentUrl = window.location.href;

    const getProgressBar = () => document.getElementById('pjax-progress');
    const getPageContent = () => document.getElementById('page-content');
    const getPageTitle = () => document.getElementById('page-title');

    function startProgress() {
        const bar = getProgressBar();
        if (!bar) return;
        bar.style.opacity = '1';
        bar.style.width = '0';
        bar.style.transition = 'none';
        bar.getBoundingClientRect();
        bar.style.transition = 'width 0.8s ease';
        bar.style.width = '70%';

        clearTimeout(progressTimer);
        progressTimer = setTimeout(() => {
            bar.style.width = '90%';
        }, 1000);
    }

    function finishProgress() {
        clearTimeout(progressTimer);
        const bar = getProgressBar();
        if (!bar) return;
        bar.style.transition = 'width 0.2s ease';
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            bar.style.width = '0';
        }, 250);
    }

    function failProgress() {
        clearTimeout(progressTimer);
        const bar = getProgressBar();
        if (!bar) return;
        bar.style.background = '#ef4444';
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            bar.style.width = '0';
            setTimeout(() => { bar.style.background = ''; }, 300);
        }, 400);
    }

    function updateSidebarActiveState(url) {
        try {
            const pathname = new URL(url).pathname;

            document.querySelectorAll('aside nav a, aside nav button').forEach(el => {
                el.classList.remove('bg-indigo-100', 'text-indigo-700');
                if (el.tagName === 'A') {
                    el.classList.remove('bg-indigo-50');
                    el.classList.add('text-gray-700', 'hover:bg-gray-100');
                } else {
                    el.classList.add('text-gray-700', 'hover:bg-gray-100');
                }
            });

            document.querySelectorAll('aside nav [x-show] a').forEach(el => {
                el.classList.remove('bg-indigo-50', 'text-indigo-700');
                el.classList.add('text-gray-600', 'hover:bg-gray-50');
            });

            let bestMatch = null;
            let bestMatchLen = -1;

            document.querySelectorAll('aside nav a').forEach(el => {
                if (!el.href) return;
                const elPath = new URL(el.href).pathname;
                const isDashboardOrMap = elPath === '/dashboard' || elPath === '/dashboard/map';

                let isMatch = false;
                if (isDashboardOrMap) {
                    isMatch = pathname === elPath;
                } else {
                    isMatch = pathname === elPath || (elPath !== '/' && pathname.startsWith(elPath));
                }

                if (isMatch && elPath.length > bestMatchLen) {
                    bestMatch = el;
                    bestMatchLen = elPath.length;
                }
            });

            if (bestMatch) {
                const el = bestMatch;
                if (el.closest('[x-show]')) {
                    el.classList.add('bg-indigo-50', 'text-indigo-700');
                    el.classList.remove('text-gray-600', 'text-gray-700', 'hover:bg-gray-50', 'hover:bg-gray-100');
                } else {
                    el.classList.add('bg-indigo-100', 'text-indigo-700');
                    el.classList.remove('text-gray-700', 'text-gray-600', 'hover:bg-gray-100', 'hover:bg-gray-50');
                }

                const dropdown = el.closest('[x-data]');
                if (dropdown) {
                    if (dropdown._x_dataStack) {
                        try { dropdown._x_dataStack[0].open = true; } catch (_) { }
                    }
                    const parentBtn = dropdown.querySelector('button');
                    if (parentBtn) {
                        parentBtn.classList.add('bg-indigo-100', 'text-indigo-700');
                        parentBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                    }
                }
            }
        } catch (e) {
            console.warn('[PJAX] Update sidebar error:', e);
        }
    }

    function executeScripts(container) {
        const scripts = container.querySelectorAll('script:not([id="pjax-meta"])');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            if (!oldScript.src) {
                newScript.textContent = oldScript.textContent;
            }
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    function renderContent(html, url) {
        const pageContent = getPageContent();
        if (!pageContent) {
            window.location.href = url;
            return;
        }

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Jika response mengarah ke login (session timeout), reload penuh
        if (doc.title.toLowerCase().includes('login') || doc.querySelector('form[action*="login"]')) {
            window.location.href = url;
            return;
        }

        // Ambil metadata dari script tag
        const metaEl = doc.getElementById('pjax-meta');
        let meta = {};
        if (metaEl) {
            try { meta = JSON.parse(metaEl.textContent.trim()); } catch (_) { }
        }

        // Ambil konten baru secara fleksibel dari #pjax-content-wrapper atau #page-content
        const targetWrapper = doc.getElementById('pjax-content-wrapper') || doc.getElementById('page-content');
        const newContent = targetWrapper ? targetWrapper.innerHTML : html;

        pageContent.style.opacity = '0.3';
        pageContent.style.transition = 'opacity 0.12s ease';

        setTimeout(() => {
            pageContent.innerHTML = newContent;
            pageContent.style.opacity = '1';

            executeScripts(pageContent);

            if (window.Alpine) {
                try { window.Alpine.initTree(pageContent); } catch (_) { }
            }

            if (meta.title) {
                document.title = meta.title;
            }

            const pageTitleEl = getPageTitle();
            if (pageTitleEl && meta.pageTitle) {
                pageTitleEl.textContent = meta.pageTitle;
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
            updateSidebarActiveState(url);

            document.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url, meta } }));
        }, 120);
    }

    async function navigate(url, pushState = true) {
        if (isLoading) return;

        // Skip jika URL persis sama dengan lokasi saat ini (mencegah loop)
        if (url === window.location.href && pushState) {
            return;
        }

        isLoading = true;
        startProgress();

        try {
            const response = await fetch(url, {
                headers: {
                    'X-PJAX': 'true',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const finalUrl = response.url || url;
            const html = await response.text();

            finishProgress();

            if (pushState) {
                window.history.pushState({ pjax: true, url: finalUrl }, '', finalUrl);
                currentUrl = finalUrl;
            } else {
                currentUrl = finalUrl;
            }

            renderContent(html, finalUrl);

        } catch (err) {
            console.warn('[PJAX] Navigasi gagal, fallback ke full reload:', err.message);
            failProgress();
            window.location.href = url;
        } finally {
            isLoading = false;
        }
    }

    function shouldIntercept(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        if (!anchor.href) return false;

        try {
            const linkUrl = new URL(anchor.href);
            if (linkUrl.origin !== window.location.origin) return false;
        } catch (_) {
            return false;
        }

        if (anchor.hasAttribute('data-no-pjax')) return false;
        if (anchor.target === '_blank') return false;
        if (anchor.hasAttribute('download')) return false;
        if (anchor.getAttribute('href')?.startsWith('#')) return false;

        const path = new URL(anchor.href).pathname.toLowerCase();
        
        // Skip cetak PDF, print, logout
        if (path.includes('/print') || path.includes('/logout') || path.endsWith('.pdf')) return false;
        if (path.includes('/api/')) return false;

        // Skip map routes jika bertukar dari/ke halaman peta
        const currentPath = window.location.pathname.toLowerCase();
        const isTargetMap = path.startsWith('/map') || path.startsWith('/dashboard/map');
        const isCurrentMap = currentPath.startsWith('/map') || currentPath.startsWith('/dashboard/map');
        if (isTargetMap !== isCurrentMap) return false;

        return true;
    }

    function handleClick(event) {
        if (event.defaultPrevented) return;

        const anchor = event.target.closest('a');
        if (!shouldIntercept(anchor)) return;

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        navigate(anchor.href);
    }

    function handlePopState(event) {
        const url = window.location.href;
        if (event.state?.pjax) {
            navigate(url, false);
        } else {
            window.location.reload();
        }
    }

    function init() {
        window.history.replaceState({ pjax: true, url: window.location.href }, '', window.location.href);
        document.addEventListener('click', handleClick, { capture: false });
        window.addEventListener('popstate', handlePopState);
        console.info('[PJAX] Ajax router aktif ✓');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return { navigate };
})();

window.PJAX = PJAX;
