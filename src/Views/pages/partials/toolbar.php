<div class="toolbar">
    <div class="toolbar-left" style="display: flex; align-items: center;">
        <button type="button" class="toolbar-button" onclick="toggleSidebar()" style="margin-right: 1rem; border: none; background: transparent; padding: 0.5rem; cursor: pointer; z-index: 998; position: relative;">
            <i class="bx bx-menu" style="font-size: 1.5rem; color: var(--text-primary);"></i>
        </button>                    
    </div>
    <div class="toolbar-right">
        <div style="position: relative;">
            <button class="theme-toggle" onclick="toggleThemeMenu()">
                <i class="bx bx-palette"></i>
                <span style="font-size: 0.875rem;">테마</span>
            </button>
            <div id="themeToggleMenu" class="theme-toggle-menu" style="display: none;">
                <button onclick="setTheme('system')" data-theme="system">
                    <i class="bx bx-desktop"></i>
                    <span>System</span>
                </button>
                <button onclick="setTheme('light')" data-theme="light">
                    <i class="bx bx-sun"></i>
                    <span>Light</span>
                </button>
                <button onclick="setTheme('dark')" data-theme="dark">
                    <i class="bx bx-moon"></i>
                    <span>Dark</span>
                </button>
            </div>
        </div>
        <a href="/logout" class="toolbar-button">
            <i class="bx bx-log-out"></i>
            <span style="font-size: 0.875rem;">로그아웃</span>
        </a>
    </div>
</div>
