<?php 
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 

// 메뉴 데이터 정의
$menuGroups = [
    '데이터베이스' => [
        [
            'path'         => '/analysis',
            'icon'         => 'bx-bar-chart-alt-2',
            'title'        => '분석 DB',
            'activePrefix' => ['/', '/analysis']
        ],
        [
            'path'         => '/vectors',
            'icon'         => 'bx-cube',
            'title'        => '벡터 DB',
            'activePrefix' => ['/vectors']
        ],
        [
            'path'         => '/search',
            'icon'         => 'bx-search',
            'title'        => '레코드 검색',
            'activePrefix' => ['/search']
        ],
    ],
    '계정' => [
        [
            'path'         => '/accounts',
            'icon'         => 'bx-user-circle',
            'title'        => '계정 관리',
            'activePrefix' => ['/accounts']
        ],
    ],
    '분석' => [
        [
            'path'         => '/insights',
            'icon'         => 'bx-bar-chart-square',
            'title'        => '인사이트',
            'activePrefix' => ['/insights']
        ],
        [
            'path'         => '/refer',
            'icon'         => 'bx-link',
            'title'        => '리퍼럴',
            'activePrefix' => ['/refer']
        ],
    ],
];

// 활성화 여부 확인 함수
$isActive = function($activePrefixPatterns, $currentPath) {
    foreach ($activePrefixPatterns as $prefix) {
        if ($prefix === '/' && $currentPath === '/') return true;
        if ($prefix !== '/' && strpos($currentPath, $prefix) === 0) return true;
    }
    return false;
};
?>
<aside class="layout-sidebar">    
    <div class="menu">
        <div class="logo">
            <span>Metheyou<br/>Manager</span>
        </div>

        <?php foreach ($menuGroups as $groupName => $items): ?>
            <p class="menu-label"><?= $groupName ?></p>
            <ul class="menu-list">
                <?php foreach ($items as $item): ?>
                    <li>
                        <a href="<?= $item['path'] ?>" class="menu-link <?= $isActive($item['activePrefix'], $currentPath) ? 'active' : '' ?>">
                            <i class="bx <?= $item['icon'] ?> mr-1"></i>
                            <?= $item['title'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
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
