requestAnimationFrame(() => {
    document.documentElement.classList.remove('theme-preload');
});

// Theme switching function
function setTheme(theme) {
    localStorage.setItem('theme', theme);
    if (theme === 'system') {
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
    } else {
        document.documentElement.setAttribute('data-theme', theme);
    }
    updateThemeMenu();
    
    // if dropdown exists, close it
    const menu = document.getElementById('themeToggleMenu');
    if (menu) menu.style.display = 'none';
}

// Listen for system theme changes
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const currentSetting = localStorage.getItem('theme') || 'system';
        if (currentSetting === 'system') {
            document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
        }
    });
}

// Update theme toggle menu display
function updateThemeMenu() {
    const savedTheme = localStorage.getItem('theme') || 'system';
    const menuButtons = document.querySelectorAll('.theme-toggle-menu button');
    
    menuButtons.forEach(btn => {
        if (btn.dataset.theme === savedTheme) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Toggle menu visibility
function toggleThemeMenu() {
    const menu = document.getElementById('themeToggleMenu');
    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('themeToggleMenu');
    const toggleBtn = document.querySelector('.theme-toggle');
    
    if (menu && toggleBtn && !toggleBtn.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = 'none';
    }
});

// Initialize responsive sidebar state from localStorage
document.addEventListener('DOMContentLoaded', () => {
    const sidebarState = localStorage.getItem('sidebarState');
    const sidebar = document.querySelector('.layout-sidebar');
    
    if (sidebarState === 'collapsed' && window.innerWidth > 768) {
        if (sidebar) sidebar.classList.add('is-collapsed');
    }
    
    // Adjust sidebar classes when window resizes
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            const backdrop = document.querySelector('.sidebar-backdrop');
            sidebar.classList.remove('is-mobile-open');
            if (backdrop) {
                backdrop.classList.remove('is-active');
                backdrop.style.display = 'none';
            }
        }
    });
});

// Toggle sidebar visibility depending on screen width
function toggleSidebar() {
    const sidebar = document.querySelector('.layout-sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    
    if (window.innerWidth <= 768) {
        // Mobile behavior (off-canvas & backdrop)
        sidebar.classList.toggle('is-mobile-open');
        const isOpen = sidebar.classList.contains('is-mobile-open');
        
        if (isOpen) {
            backdrop.style.display = 'block';
            // small delay to allow display:block to apply before opacity transition
            setTimeout(() => backdrop.classList.add('is-active'), 10);
        } else {
            backdrop.classList.remove('is-active');
            setTimeout(() => backdrop.style.display = 'none', 300);
        }
    } else {
        // Desktop behavior (push content & save state)
        sidebar.classList.toggle('is-collapsed');
        const isCollapsed = sidebar.classList.contains('is-collapsed');
        localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'open');
    }
}

function closeSidebarMobile() {
    if (window.innerWidth <= 768) {
        toggleSidebar(); // Uses the same toggle logic
    }
}
