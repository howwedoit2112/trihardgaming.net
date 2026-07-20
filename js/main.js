/**
 * TriHard Gaming Tech - Main JavaScript
 * Handles navigation, link functionality, and interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initLinkHandling();
    initSmoothScroll();
    initArticleInteractions();
    initMobileMenu();
    initCategoryFilter();
    console.log('TriHard Gaming Tech loaded successfully');
});

/**
 * Highlight current page in nav
 */
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-links a');
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';

    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage ||
            (currentPage === '' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });
}

/**
 * Ensure all internal links navigate properly
 */
function initLinkHandling() {
    const allLinks = document.querySelectorAll('a[href]');

    allLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Let external/anchor/mailto links work natively
            if (href.startsWith('http') || href.startsWith('#') || href.startsWith('mailto:')) {
                return;
            }

            // Prevent default, then navigate
            e.preventDefault();

            // Resolve relative paths correctly
            let targetUrl = href;
            if (!href.startsWith('/')) {
                // Strip filename from current path to get directory
                const currentPath = window.location.pathname;
                const slashIdx = currentPath.lastIndexOf('/');
                const currentDir = slashIdx >= 0 ? currentPath.substring(0, slashIdx + 1) : '';
                targetUrl = currentDir + href;
            }

            // Navigate
            window.location.assign(targetUrl);
        });
    });
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.documentElement.style.scrollBehavior = 'smooth';
}

/**
 * Reading time estimates + code copy buttons
 */
function initArticleInteractions() {
    const articles = document.querySelectorAll('.article-body');

    articles.forEach(article => {
        const text = article.textContent;
        const wordCount = text.split(/\s+/).length;
        const readTime = Math.max(1, Math.ceil(wordCount / 200));

        const meta = article.closest('article')?.querySelector('.article-meta');
        if (meta) {
            const readTimeEl = document.createElement('span');
            readTimeEl.className = 'read-time';
            readTimeEl.textContent = `${readTime} min read`;
            meta.appendChild(readTimeEl);
        }
    });

    // Copy button for code blocks
    const codeBlocks = document.querySelectorAll('pre, code');
    codeBlocks.forEach(block => {
        const button = document.createElement('button');
        button.textContent = 'Copy';
        button.className = 'copy-button';
        button.style.cssText = `
            position: absolute; top: 0.5rem; right: 0.5rem;
            background: #21262d; border: 1px solid #30363d;
            color: #8b949e; padding: 0.25rem 0.5rem;
            border-radius: 4px; cursor: pointer; font-size: 0.8rem;
            opacity: 0; transition: opacity 0.2s;
        `;
        block.style.position = 'relative';
        block.addEventListener('mouseenter', () => button.style.opacity = '1');
        block.addEventListener('mouseleave', () => button.style.opacity = '0');
        button.addEventListener('click', () => {
            navigator.clipboard.writeText(block.textContent);
            button.textContent = 'Copied!';
            setTimeout(() => button.textContent = 'Copy', 2000);
        });
        block.appendChild(button);
    });
}

/**
 * Mobile hamburger menu
 */
function initMobileMenu() {
    const nav = document.querySelector('.site-nav');
    if (!nav) return;

    const hamburger = document.createElement('button');
    hamburger.className = 'hamburger';
    hamburger.innerHTML = '\u2630';
    hamburger.setAttribute('aria-label', 'Toggle navigation');
    hamburger.style.cssText = `
        display: none; background: none; border: none;
        color: #8b949e; font-size: 1.5rem; cursor: pointer;
        margin-right: 1rem;
    `;

    const navLinks = document.querySelector('.nav-links');
    if (!navLinks) return;

    hamburger.addEventListener('click', () => {
        const isFlex = navLinks.style.display === 'flex';
        navLinks.style.display = isFlex ? 'none' : 'flex';
    });

    const mediaQuery = window.matchMedia('(max-width: 768px)');
    function handleMobileView(e) {
        if (e.matches) {
            hamburger.style.display = 'block';
            navLinks.style.flexDirection = 'column';
            navLinks.style.gap = '0.5rem';
            navLinks.style.padding = '1rem 0';
        } else {
            hamburger.style.display = 'none';
            navLinks.style.display = 'flex';
            navLinks.style.flexDirection = 'row';
        }
    }
    mediaQuery.addEventListener('change', handleMobileView);
    handleMobileView(mediaQuery);
    nav.insertBefore(hamburger, navLinks);
}

/**
 * Category filter for articles page
 */
function initCategoryFilter() {
    const sections = document.querySelectorAll('.articles-list');
    if (sections.length <= 1) return;

    const filterBar = document.createElement('div');
    filterBar.className = 'category-filter';
    filterBar.style.cssText = `
        display: flex; gap: 0.5rem; margin: 1rem 0 2rem; flex-wrap: wrap;
    `;

    const allBtn = document.createElement('button');
    allBtn.textContent = 'All';
    allBtn.className = 'filter-btn active';
    allBtn.style.cssText = `
        padding: 0.5rem 1rem; border: 1px solid #30363d; background: #161b22;
        color: #c9d1d9; border-radius: 6px; cursor: pointer;
    `;
    filterBar.appendChild(allBtn);

    sections.forEach(section => {
        const cat = section.getAttribute('data-category');
        const btn = document.createElement('button');
        btn.textContent = cat.charAt(0).toUpperCase() + cat.slice(1);
        btn.className = 'filter-btn';
        btn.style.cssText = allBtn.style.cssText;
        btn.addEventListener('click', () => {
            sections.forEach(s => s.style.display = 'none');
            const target = document.querySelector(`.articles-list[data-category="${cat}"]`);
            if (target) target.style.display = 'block';
            filterBar.querySelectorAll('.filter-btn').forEach(b => b.style.background = '#161b22');
            btn.style.background = '#1f6feb';
        });
        filterBar.appendChild(btn);
    });

    allBtn.addEventListener('click', () => {
        sections.forEach(s => s.style.display = 'block');
        filterBar.querySelectorAll('.filter-btn').forEach(b => b.style.background = '#161b22');
        allBtn.style.background = '#1f6feb';
    });

    const wrapper = document.querySelector('.content-wrapper');
    if (wrapper) {
        wrapper.insertBefore(filterBar, wrapper.querySelector('section'));
    }
}
