// Apply theme before CSS is parsed to prevent initial flash.
(function() {
    var savedTheme = 'system';
    try {
        savedTheme = localStorage.getItem('theme') || 'system';
    } catch (e) {
        savedTheme = 'system';
    }

    var resolvedTheme = savedTheme;
    if (savedTheme === 'system') {
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        resolvedTheme = prefersDark ? 'dark' : 'light';
    }

    document.documentElement.setAttribute('data-theme', resolvedTheme);
    document.documentElement.style.colorScheme = resolvedTheme;
    document.documentElement.classList.add('theme-preload');
})();
