<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_name = $_SESSION['user_name'] ?? $_SESSION['role'] ?? 'User';
$user_initial = strtoupper(substr($user_name, 0, 1));
?>

<div class="header">
    <h1>
        <i class="fas fa-graduation-cap"></i>
        FEU Roosevelt DMS
    </h1>

    <div class="user-info">
        <div class="user-avatar"><?= htmlspecialchars($user_initial) ?></div>
        <span><?= htmlspecialchars($user_name) ?></span>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
