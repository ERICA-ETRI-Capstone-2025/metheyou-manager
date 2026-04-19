<?php $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
<aside class="layout-sidebar">    
    <div class="menu">
        <div class="logo">
            <span>Metheyou<br/>
            Manager</span>
            <button class="mobile-close-btn" onclick="closeSidebarMobile()" style="background: transparent; border: none; cursor: pointer; display: none;">
                <i class="bx bx-chevron-left" style="font-size: 1.5rem; color: var(--text-primary);"></i>
            </button>
        </div>
        <p class="menu-label">데이터베이스</p>
        <ul class="menu-list">
            <li>
                <a href="/analysis" class="menu-link <?= ($currentPath === '/' || strpos($currentPath, '/analysis') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-bar-chart-alt-2 mr-1"></i>
                    분석 DB
                </a>
            </li>
            <li>
                <a href="/vectors" class="menu-link <?= (strpos($currentPath, '/vectors') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-cube mr-1"></i>
                    벡터 DB
                </a>
            </li>
            <li>
                <a href="/vectors" class="menu-link <?= (strpos($currentPath, '/vectors') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-search mr-1"></i>
                    레코드 검색
                </a>
            </li>
        </ul>

        <p class="menu-label">계정</p>
        <ul class="menu-list">
            <li>
                <a href="/accounts" class="menu-link <?= (strpos($currentPath, '/accounts') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-user-circle mr-1"></i>
                    계정 관리
                </a>
            </li>
        </ul>

        <p class="menu-label">분석</p>
        <ul class="menu-list">
            <li>
                <a href="/analysis/history" class="menu-link <?= (strpos($currentPath, '/analysis/history') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-bar-chart-square mr-1"></i>
                    인사이트
                </a>
            </li>
            <li>
                <a href="/analysis/compare" class="menu-link <?= (strpos($currentPath, '/analysis/compare') === 0) ? 'active' : '' ?>">
                    <i class="bx bx-link mr-1"></i>
                    리퍼럴
                </a>    
            </li>
        </ul>
    </div>

    <!-- User Profile Footer -->
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <div class="sidebar-user-profile">
        <div class="profile-info">
            <i class="bx bx-user-circle user-avatar"></i>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <?php if(isset($_SESSION['user_role'])): ?>
                    <span class="user-role"><?= htmlspecialchars($_SESSION['user_role']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/logout" class="logout-btn" title="로그아웃">
            <i class="bx bx-log-out"></i>
        </a>
    </div>
    <?php endif; ?>
</aside>
