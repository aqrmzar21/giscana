/**
 * ajax-router.js — High-Performance Instant PJAX Router & Prefetch Engine untuk Giscana
 * 
 * Features:
 * 1. In-Memory Response Caching (Instant <10ms rendering for previously visited pages)
 * 2. Smart Link Hover Prefetching (Prefetches page on hover/pointerenter for 0ms perceived lag)
 * 3. AbortController for cancelling obsolete pending requests
 * 4. Automatic Scroll-Reveal & Alpine Re-initialization
 */

const PJAX = (() => {
    let progressTimer = null;
    let currentUrl = window.location.href;
    let navToken = 0;
    let abortController = null;

    // Cache Map: url -> { html, title, pageTitle, meta, timestamp }
    const pageCache = new Map();
    const CACHE_TTL_MS = 3 * 60 * 1000; // 3 menit
    const prefetchQueue = new Set();

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
        bar.style.transition = 'width 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        bar.style.width = '75%';

        clearTimeout(progressTimer);
        progressTimer = setTimeout(() => {
            bar.style.width = '90%';
        }, 500);
    }

    function finishProgress() {
        clearTimeout(progressTimer);
        const bar = getProgressBar();
        if (!bar) return;
        bar.style.transition = 'width 0.15s ease';
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            bar.style.width = '0';
        }, 200);
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
                el.classList.remove('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900/50', 'dark:text-indigo-300');
                if (el.tagName === 'A') {
                    el.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/40', 'font-semibold');
                    el.classList.add('text-gray-700', 'dark:text-gray-200', 'hover:bg-gray-100');
                } else {
                    el.classList.add('text-gray-700', 'dark:text-gray-200', 'hover:bg-gray-100');
                }
            });

            document.querySelectorAll('aside nav [x-show] a').forEach(el => {
                el.classList.remove('bg-indigo-50', 'text-indigo-700', 'dark:bg-indigo-900/40', 'dark:text-indigo-200', 'font-semibold');
                el.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50');
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
                    el.classList.add('bg-indigo-50', 'text-indigo-700', 'dark:bg-indigo-900/40', 'dark:text-indigo-200', 'font-semibold');
                    el.classList.remove('text-gray-600', 'text-gray-700', 'hover:bg-gray-50', 'hover:bg-gray-100');
                } else {
                    el.classList.add('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900/50', 'dark:text-indigo-300');
                    el.classList.remove('text-gray-700', 'text-gray-600', 'hover:bg-gray-100', 'hover:bg-gray-50');
                }

                const dropdown = el.closest('[x-data]');
                if (dropdown) {
                    if (dropdown._x_dataStack) {
                        try { dropdown._x_dataStack[0].open = true; } catch (_) { }
                    }
                    const parentBtn = dropdown.querySelector('button');
                    if (parentBtn) {
                        parentBtn.classList.add('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900/50', 'dark:text-indigo-300');
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

    function parseHtmlResponse(html, url) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        if (doc.title.toLowerCase().includes('login') || doc.querySelector('form[action*="login"]')) {
            return { redirectLogin: true };
        }

        const metaEl = doc.getElementById('pjax-meta');
        let meta = {};
        if (metaEl) {
            try { meta = JSON.parse(metaEl.textContent.trim()); } catch (_) { }
        }

        const targetWrapper = doc.getElementById('pjax-content-wrapper') || doc.getElementById('page-content');
        const newContent = targetWrapper ? targetWrapper.innerHTML : html;
        const pageTitle = meta.pageTitle || doc.querySelector('h1, h2')?.textContent?.trim() || '';

        return {
            content: newContent,
            title: meta.title || doc.title,
            pageTitle: pageTitle,
            meta: meta
        };
    }

    function renderContent(parsed, url, token) {
        const pageContent = getPageContent();
        if (!pageContent) {
            window.location.href = url;
            return;
        }

        if (token !== navToken) return; // sudah digantikan navigasi lebih baru

        // Animasi transisi konten yang super halus tanpa penundaan berlebih
        pageContent.style.opacity = '0.4';
        pageContent.style.transition = 'opacity 0.08s ease-out';

        requestAnimationFrame(() => {
            if (token !== navToken) return;

            pageContent.innerHTML = parsed.content;
            pageContent.style.opacity = '1';

            executeScripts(pageContent);

            if (window.Alpine) {
                try { window.Alpine.initTree(pageContent); } catch (_) { }
            }

            if (parsed.title) {
                document.title = parsed.title;
            }

            const pageTitleEl = getPageTitle();
            if (pageTitleEl && parsed.pageTitle) {
                pageTitleEl.textContent = parsed.pageTitle;
            }

            window.scrollTo({ top: 0, behavior: 'instant' });
            updateSidebarActiveState(url);

            // Re-trigger scroll reveal observers
            if (window.initScrollReveal) {
                try { window.initScrollReveal(); } catch (_) {}
            }

            document.dispatchEvent(new CustomEvent('pjax:complete', { detail: { url, meta: parsed.meta } }));
        });
    }

    async function fetchPage(url, signal = null) {
        const response = await fetch(url, {
            headers: {
                'X-PJAX': 'true',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            credentials: 'same-origin',
            cache: 'default',
            signal: signal,
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const finalUrl = response.url || url;
        const html = await response.text();
        const parsed = parseHtmlResponse(html, finalUrl);

        if (parsed.redirectLogin) {
            return { redirectLogin: true, url: finalUrl };
        }

        const entry = {
            parsed,
            finalUrl,
            html,
            timestamp: Date.now()
        };

        pageCache.set(finalUrl, entry);
        if (url !== finalUrl) pageCache.set(url, entry);

        return entry;
    }

    // Smart prefetch link on hover
    function prefetch(url) {
        if (!url || prefetchQueue.has(url) || pageCache.has(url)) return;
        const cached = pageCache.get(url);
        if (cached && (Date.now() - cached.timestamp < CACHE_TTL_MS)) return;

        prefetchQueue.add(url);
        
        // Fetch low-priority in background
        fetchPage(url).catch(() => {
            prefetchQueue.delete(url);
        });
    }

    async function navigate(url, pushState = true) {
        if (url === window.location.href && pushState) {
            return;
        }

        if (abortController) abortController.abort();
        abortController = new AbortController();
        const token = ++navToken;

        // Clean stale cache entries
        const now = Date.now();
        pageCache.forEach((val, key) => {
            if (now - val.timestamp > CACHE_TTL_MS) pageCache.delete(key);
        });

        // 1. Check in-memory cache first (INSTANT RENDER <10ms)
        const cached = pageCache.get(url);
        if (cached && (now - cached.timestamp < CACHE_TTL_MS)) {
            finishProgress();
            if (pushState) {
                window.history.pushState({ pjax: true, url: cached.finalUrl }, '', cached.finalUrl);
            } else if (cached.finalUrl !== window.location.href) {
                window.history.replaceState({ pjax: true, url: cached.finalUrl }, '', cached.finalUrl);
            }
            currentUrl = cached.finalUrl;
            renderContent(cached.parsed, cached.finalUrl, token);

            // Background revalidate to ensure page freshness
            fetchPage(url).then(freshEntry => {
                if (token === navToken && freshEntry && !freshEntry.redirectLogin) {
                    // Update metadata if changed
                    updateSidebarActiveState(freshEntry.finalUrl);
                }
            }).catch(() => {});
            return;
        }

        // 2. Cache miss -> Fetch from network with fast progress indicator
        startProgress();

        try {
            const entry = await fetchPage(url, abortController.signal);

            if (entry.redirectLogin) {
                window.location.href = entry.url;
                return;
            }

            if (token !== navToken) return;

            finishProgress();

            if (pushState) {
                window.history.pushState({ pjax: true, url: entry.finalUrl }, '', entry.finalUrl);
            } else if (entry.finalUrl !== window.location.href) {
                window.history.replaceState({ pjax: true, url: entry.finalUrl }, '', entry.finalUrl);
            }
            currentUrl = entry.finalUrl;

            renderContent(entry.parsed, entry.finalUrl, token);

        } catch (err) {
            if (err.name === 'AbortError') return;
            console.warn('[PJAX] Navigasi gagal, fallback ke full reload:', err.message);
            failProgress();
            window.location.href = url;
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
        
        if (path.includes('/print') || path.includes('/logout') || path.endsWith('.pdf')) return false;
        if (path.includes('/api/')) return false;

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

    // Prefetch on pointerenter / mouseover for internal links
    function handlePointerEnter(event) {
        const anchor = event.target.closest('a');
        if (shouldIntercept(anchor)) {
            prefetch(anchor.href);
        }
    }

    function handlePopState(event) {
        const url = window.location.href;
        if (event.state?.pjax) {
            navigate(url, false);
        } else if (event.state === null && url.split('#')[0] === currentUrl.split('#')[0]) {
            return;
        } else {
            window.location.reload();
        }
    }

    function init() {
        window.history.replaceState({ pjax: true, url: window.location.href }, '', window.location.href);
        document.addEventListener('click', handleClick, { capture: false });
        
        // Listen to hover/touch for instant preloading
        document.addEventListener('pointerenter', handlePointerEnter, { capture: true, passive: true });
        document.addEventListener('touchstart', handlePointerEnter, { capture: true, passive: true });

        window.addEventListener('popstate', handlePopState);
        console.info('[PJAX] High-speed Ajax router & prefetch active ✓');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return { navigate, prefetch, clearCache: () => pageCache.clear() };
})();

window.PJAX = PJAX;
