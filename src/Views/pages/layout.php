<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/public/icon.png" type="image/png" size="512x512">
    <title>Metheyou Manager</title>
    <script src="/public/js/theme-init.js?t=<?php echo filemtime(__DIR__ . '/../../../public/js/theme-init.js'); ?>"></script>
    <!-- Bulma CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
    <!-- Boxicons for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <!-- Pretendard Font -->
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <!-- Global Styles -->
    <link rel="stylesheet" href="/public/css/styles.css?t=<?php echo filemtime(__DIR__ . '/../../../public/css/styles.css'); ?>">
    <link rel="stylesheet" href="/public/css/analysis.css?t=<?php echo filemtime(__DIR__ . '/../../../public/css/analysis.css'); ?>">
    <link rel="stylesheet" href="/public/css/sidebar.css?t=<?php echo filemtime(__DIR__ . '/../../../public/css/sidebar.css'); ?>">
</head>
<body>
    <script src="/public/js/layout.js?t=<?php echo filemtime(__DIR__ . '/../../../public/js/layout.js'); ?>"></script>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) : ?>
    <div class="layout-container">
        <!-- Sidebar Backdrop for responsive mobile design -->
        <div class="sidebar-backdrop" onclick="closeSidebarMobile()"></div>
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="layout-content">
            <!-- Header Toolbar -->
            <?php include __DIR__ . '/partials/toolbar.php'; ?>

            <div class="layout-body">
                <!-- Main Content -->
                <main class="layout-main">
                    <?php echo $content ?? ''; ?>
                </main>
            </div>
        </div>
    </div>
    <?php else: ?>
        <?php echo $content ?? ''; ?>
    <?php endif; ?>
</body>
</html>