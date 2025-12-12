<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">

    <div class="nav-section">
        <div class="nav-section-title">Main Menu</div>

        <a href="admin_dashboard.php"
           class="nav-item <?= $current_page === 'admin_dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <a href="students.php"
           class="nav-item <?= $current_page === 'students.php' ? 'active' : '' ?>">
            <i class="fas fa-user-graduate"></i>
            <span>Students</span>
        </a>

        <a href="documents.php"
           class="nav-item <?= $current_page === 'documents.php' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Documents</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Academic</div>

        <a href="deans_list.php"
           class="nav-item <?= $current_page === 'deans_list.php' ? 'active' : '' ?>">
            <i class="fas fa-star"></i>
            <span>Dean's Lister</span>
        </a>

        <a href="scholarships.php"
           class="nav-item <?= $current_page === 'scholarships.php' ? 'active' : '' ?>">
            <i class="fas fa-award"></i>
            <span>Scholarships</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">System</div>

        <a href="users.php"
           class="nav-item <?= $current_page === 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>User Management</span>
        </a>

        <a href="reports.php"
           class="nav-item <?= $current_page === 'reports.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
        </a>

        <a href="settings.php"
           class="nav-item <?= $current_page === 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>

</aside>
