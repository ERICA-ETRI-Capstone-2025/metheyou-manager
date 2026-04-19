<?php
// Simple layout without the admin header
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metheyou Manager - Login</title>
  <script>
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
  </script>
    <!-- Boxicons for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <!-- Pretendard Font -->
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <!-- Login Styles -->
    <link rel="stylesheet" href="/public/css/theme-toggle.css?t=<?php echo filemtime(__DIR__ . '/../../../../public/css/theme-toggle.css'); ?>">
    <link rel="stylesheet" href="/public/css/login.css?t=<?php echo filemtime(__DIR__ . '/../../../../public/css/login.css'); ?>">
</head>
<body>
<!-- Theme Toggle -->
<div class="theme-toggle-wrapper">
    <button class="theme-toggle-btn" data-theme-value="system" title="System Theme">
        <i class="bx bx-desktop"></i>
    </button>
    <button class="theme-toggle-btn" data-theme-value="light" title="Light Theme">
        <i class="bx bx-sun"></i>
    </button>
    <button class="theme-toggle-btn" data-theme-value="dark" title="Dark Theme">
        <i class="bx bx-moon"></i>
    </button>
</div>

<section class="login-section">
  <div class="login-container">
    <div class="login-card">
      <!-- Header -->
      <div class="login-header">
        <div class="login-icon">
          <i class="bx bx-lock"></i>
        </div>
        <h1 class="login-title">믿어유 매니저</h1>
        <p class="login-subtitle">관리 페이지 로그인</p>
      </div>

      <!-- Error Message -->
      <?php if(isset($error)): ?>
      <div class="login-error">
        <i class="bx bx-error-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" action="/login" class="login-form">
        <!-- Username Field -->
        <div class="form-group">
          <label class="form-label">
            <i class="bx bx-user"></i>
            <span>Username</span>
          </label>
          <input type="text" name="username" placeholder="사용자 이름을 입력하세요" required autofocus class="form-input">
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label class="form-label">
            <i class="bx bx-key"></i>
            <span>Password</span>
          </label>
          <input type="password" name="password" placeholder="비밀번호를 입력하세요" required class="form-input">
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="login-button">
          <i class="bx bx-log-in"></i>
          <span>로그인</span>
        </button>
      </form>
    </div>

    <!-- Footer -->
    <div class="login-footer">
      <span>ETRICA 2026</span>
    </div>
  </div>
</section>

<script>
  requestAnimationFrame(() => {
    document.documentElement.classList.remove('theme-preload');
  });

    const themeButtons = document.querySelectorAll('.theme-toggle-btn');
    const htmlElement = document.documentElement;
    
    // Check saved theme or use system default
    const savedTheme = localStorage.getItem('theme') || 'system';
    setTheme(savedTheme);

    themeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.getAttribute('data-theme-value');
            setTheme(theme);
        });
    });

    function setTheme(theme) {
        localStorage.setItem('theme', theme);
        
        // Update active button state
        themeButtons.forEach(btn => {
            if (btn.getAttribute('data-theme-value') === theme) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Apply theme to HTML
        if (theme === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            htmlElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
        } else {
            htmlElement.setAttribute('data-theme', theme);
        }
    }

    // Listen to system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (localStorage.getItem('theme') === 'system') {
            htmlElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
        }
    });

    // Handle system theme set if not applied yet due to script loaded late
    if (savedTheme === 'system') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        htmlElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
    }
</script>
</body>
</html>
