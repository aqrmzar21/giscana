/**
 * ajax-router.js — PJAX-like partial page loader untuk Giscana
 *
 * Cara kerja:
 * 1. Intercept klik pada <a> yang mengarah ke origin yang sama
 * 2. Kirim fetch request dengan header X-PJAX: true
 * 3. Server return HTML partial (hanya section content)
 * 4. Replace innerHTML dari #page-content tanpa full page reload
 * 5. Update URL via pushState, update title & page-title
 * 6. Handle browser back/forward (popstate)
 */

const PJAX = (() => {
    // ─── State ──────────────────────────────────────────────────────────────
    let isLoading = false;
    let progressTimer = null;
    let currentUrl = window.location.href;

    // ─── Elemen ─────────────────────────────────────────────────────────────
    const getProgressBar = () => document.getElementById('pjax-progress');
    const getPageContent = () => document.getElementById('page-content');
    const getPageTitle = () => document.getElementById('page-title');

    // ─── Progress Bar ────────────────────────────────────────────────────────
    function startProgress() {
        const bar = getProgressBar();
        if (!bar) return;
        bar.style.opacity = '1';
        bar.style.width = '0';
        bar.style.transition = 'none';
        // Paksa reflow agar transition bisa berjalan
        bar.getBoundingClientRect();
        bar.style.transition = 'width 0.8s ease';
        bar.style.width = '70%';

        // Fallback: lanjutkan ke 90% jika request lambat
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
            // Reset warna
            setTimeout(() => { bar.style.background = ''; }, 300);
        }, 400);
    }

    // ─── Sidebar Active State ────────────────────────────────────────────────
    function updateSidebarActiveState(url) {
        const pathname = new URL(url).pathname;

        // Hapus semua active state
        document.querySelectorAll('aside nav a, aside nav button').forEach(el => {
            el.classList.remove('bg-indigo-100', 'text-indigo-700');
            if (el.tagName === 'A') {
                el.classList.remove('bg-indigo-50');
                el.classList.add('text-gray-700', 'hover:bg-gray-100');
            } else {
                el.classList.add('text-gray-700', 'hover:bg-gray-100');
            }
        });

        // Hapus active dari sub-links
        document.querySelectorAll('aside nav [x-show] a').forEach(el => {
            el.classList.remove('bg-indigo-50', 'text-indigo-700');
            el.classList.add('text-gray-600', 'hover:bg-gray-50');
        });

        // Set active pada link yang cocok
        document.querySelectorAll('aside nav a').forEach(el => {
            if (!el.href) return;
            const elPath = new URL(el.href).pathname;

            // Peta admin dan dashboard khusus menggunakan exact match
            const isDashboardOrMap = elPath === '/dashboard' || elPath === '/dashboard/map';
            
            let isMatch = false;
            if (isDashboardOrMap) {
                isMatch = pathname === elPath;
            } else {
                isMatch = pathname === elPath || (elPath !== '/' && pathname.startsWith(elPath + '/'));
            }

            if (isMatch) {
                // Jika ini adalah sub-link, stylingnya berbeda
                if (el.closest('[x-show]')) {
                    el.classList.add('bg-indigo-50', 'text-indigo-700');
                    el.classList.remove('text-gray-600', 'text-gray-700', 'hover:bg-gray-50', 'hover:bg-gray-100');
                } else {
                    el.classList.add('bg-indigo-100', 'text-indigo-700');
                    el.classList.remove('text-gray-700', 'text-gray-600', 'hover:bg-gray-100', 'hover:bg-gray-50');
                }

                // Expand parent dropdown jika ada, dan beri styling active pada parent button
                const dropdown = el.closest('[x-data]');
                if (dropdown) {
                    if (dropdown._x_dataStack) {
                        try { dropdown._x_dataStack[0].open = true; } catch (_) {}
                    }
                    const parentBtn = dropdown.querySelector('button');
                    if (parentBtn) {
                        parentBtn.classList.add('bg-indigo-100', 'text-indigo-700');
                        parentBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                    }
                }
            }
        });
    }

    // ─── Inject & Execute scripts ─────────────────────────────────────────────
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

    // ─── Render Response ─────────────────────────────────────────────────────
    function renderContent(html, url) {
        const pageContent = getPageContent();
        if (!pageContent) {
            // Fallback: full reload
            window.location.href = url;
            return;
        }

        // Parse HTML response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Ambil metadata dari script tag
        const metaEl = doc.getElementById('pjax-meta');
        let meta = {};
        if (metaEl) {
            try { meta = JSON.parse(metaEl.textContent.trim()); } catch (_) {}
        }

        // Ambil content wrapper
        const wrapper = doc.getElementById('pjax-content-wrapper');
        const newContent = wrapper ? wrapper.innerHTML : html;

        // Fade out → replace → fade in
        pageContent.style.opacity = '0.4';
        pageContent.style.transition = 'opacity 0.15s ease';

        setTimeout(() => {
            pageContent.innerHTML = newContent;
            pageContent.style.opacity = '1';

            // Execute scripts di dalam konten baru
            executeScripts(pageContent);

            // Re-init Alpine.js untuk konten baru
            if (window.Alpine) {
                try { window.Alpine.initTree(pageContent); } catch (_) {}
            }

            // Update title
            if (meta.title) {
                document.title = meta.title;
            }

            // Update page-title di top navbar
            const pageTitleEl = getPageTitle();
            if (pageTitleEl && meta.pageTitle) {
                pageTitleEl.textContent = meta.pageTitle;
            }

            // Scroll ke atas
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Update sidebar
            updateSidebarActiveState(url);

            // Dispatch event untuk integrasi lain
            document.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url, meta } }));

        }, 150);
    }

    // ─── Core Fetch ──────────────────────────────────────────────────────────
    async function navigate(url, pushState = true) {
        if (isLoading || url === currentUrl) return;

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

            // Cek apakah server melakukan redirect
            const finalUrl = response.url || url;
            const html = await response.text();

            // Jika response adalah full page (misal: redirect ke login), full reload
            if (html.includes('<!DOCTYPE html>') && !html.includes('pjax-content-wrapper')) {
                window.location.href = finalUrl;
                return;
            }

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

    // ─── Click Interceptor ───────────────────────────────────────────────────
    function shouldIntercept(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        if (!anchor.href) return false;

        // Hanya intercept link ke origin yang sama
        try {
            const linkUrl = new URL(anchor.href);
            if (linkUrl.origin !== window.location.origin) return false;
        } catch (_) {
            return false;
        }

        // Jangan intercept kalau ada atribut khusus
        if (anchor.hasAttribute('data-no-pjax')) return false;
        if (anchor.target === '_blank') return false;
        if (anchor.hasAttribute('download')) return false;

        // Jangan intercept anchor links (#something)
        if (anchor.getAttribute('href')?.startsWith('#')) return false;

        // Jangan intercept link ke file selain halaman HTML
        const noIntercept = ['.pdf', '.jpg', '.png', '.zip', '.xlsx', '.csv', '.doc'];
        const path = new URL(anchor.href).pathname.toLowerCase();
        if (noIntercept.some(ext => path.endsWith(ext))) return false;

        // Jangan intercept API & data endpoints
        if (path.includes('/api/')) return false;
        if (path === '/map/data') return false;
        if (path === '/map/search') return false;

        // Halaman peta pakai Leaflet — perlu full init
        // Biarkan full reload saat masuk/keluar dari halaman peta
        const currentPath = window.location.pathname.toLowerCase();
        const isTargetMap = path.startsWith('/map');
        const isCurrentMap = currentPath.startsWith('/map');
        if (isTargetMap !== isCurrentMap) return false;

        return true;
    }

    function handleClick(event) {
        // Cari anchor element terdekat
        const anchor = event.target.closest('a');
        if (!shouldIntercept(anchor)) return;

        // Jangan intercept kalau ada modifier keys
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        navigate(anchor.href);
    }

    // ─── Back/Forward Browser ────────────────────────────────────────────────
    function handlePopState(event) {
        const url = window.location.href;
        if (event.state?.pjax) {
            navigate(url, false);
        } else {
            // Halaman awal (state null) → full reload
            window.location.reload();
        }
    }

    // ─── Init ────────────────────────────────────────────────────────────────
    function init() {
        // Tandai halaman awal agar popstate bisa handle back ke halaman pertama
        window.history.replaceState({ pjax: true, url: window.location.href }, '', window.location.href);

        // Delegasi event click ke document
        document.addEventListener('click', handleClick, { capture: false });

        // Handle browser back/forward
        window.addEventListener('popstate', handlePopState);

        console.info('[PJAX] Ajax router aktif ✓');
    }

    // Jalankan saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose API publik
    return { navigate };
})();

// Export agar bisa dipakai dari luar jika perlu
window.PJAX = PJAX;
