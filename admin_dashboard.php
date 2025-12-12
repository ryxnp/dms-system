<?php
session_start();
include('include/db.php');

// Restrict access to Admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header('Location: landing.php');
    exit();
}

$successMessage = "";
$errorMessage = "";

// Fetch statistics with error handling
$stats = [];
$stats['total_students'] = 0;
$stats['total_documents'] = 0;
$stats['pending_documents'] = 0;
$stats['approved_documents'] = 0;
$stats['deans_list_verified'] = 0;
$stats['scholarship_approved'] = 0;
$stats['pending_verifications'] = 0;

// Get stats safely
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM user WHERE role='Student'");
    if ($result) $stats['total_students'] = $result->fetch_assoc()['count'];

    $result = $conn->query("SELECT COUNT(*) as count FROM document");
    if ($result) $stats['total_documents'] = $result->fetch_assoc()['count'];

    $result = $conn->query("SELECT COUNT(*) as count FROM document WHERE status='Pending'");
    if ($result) $stats['pending_documents'] = $result->fetch_assoc()['count'];

    $result = $conn->query("SELECT COUNT(*) as count FROM document WHERE status='Approved'");
    if ($result) $stats['approved_documents'] = $result->fetch_assoc()['count'];

    // Check if tables exist before querying
    $tables_check = $conn->query("SHOW TABLES LIKE 'dean_list'");
    if ($tables_check && $tables_check->num_rows > 0) {
        $result = $conn->query("SELECT COUNT(*) as count FROM dean_list WHERE status='Verified'");
        if ($result) $stats['deans_list_verified'] = $result->fetch_assoc()['count'];
        
        $result = $conn->query("SELECT COUNT(*) as count FROM dean_list WHERE status='Pending'");
        if ($result) $stats['pending_verifications'] = $result->fetch_assoc()['count'];
    }

    $tables_check = $conn->query("SHOW TABLES LIKE 'scholarship_application'");
    if ($tables_check && $tables_check->num_rows > 0) {
        $result = $conn->query("SELECT COUNT(*) as count FROM scholarship_application WHERE status='Approved'");
        if ($result) $stats['scholarship_approved'] = $result->fetch_assoc()['count'];
    }

    // Recent documents
    $recent_docs = $conn->query("SELECT d.*, p.name as student_name 
                                 FROM document d 
                                 LEFT JOIN profile p ON d.stud_id = p.stud_id 
                                 ORDER BY d.upload_date DESC 
                                 LIMIT 5");
} catch (Exception $e) {
    $errorMessage = "Error loading dashboard data: " . $e->getMessage();
}

// Get user info
$user_name = $_SESSION['user_name'] ?? 'Admin';
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FEU Roosevelt - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
</head>
<body>
    <?php require_once 'include/header.php'; ?>
    
    <!-- Layout -->
    <div class="layout-wrapper">
        <?php require_once 'include/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Dashboard Overview</h1>
            </div>
            
            <!-- Alerts -->
            <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($successMessage) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['total_students']) ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['total_documents']) ?></div>
                    <div class="stat-label">Total Documents</div>
                </div>
                
                <div class="stat-card yellow">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['pending_documents']) ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
                
                <div class="stat-card purple">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['approved_documents']) ?></div>
                    <div class="stat-label">Approved Documents</div>
                </div>
                
                <div class="stat-card red">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['deans_list_verified']) ?></div>
                    <div class="stat-label">Dean's List Verified</div>
                </div>
                
                <div class="stat-card indigo">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats['scholarship_approved']) ?></div>
                    <div class="stat-label">Scholarships Approved</div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="deans_list.php" class="quick-action-card">
                            <div class="quick-action-icon" style="background: #f0fff4; color: #48bb78;">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="quick-action-content">
                                <h3>Manage Dean's List</h3>
                                <p><?= $stats['pending_verifications'] ?> pending verifications</p>
                            </div>
                        </a>
                        
                        <a href="scholarships.php" class="quick-action-card">
                            <div class="quick-action-icon" style="background: #fffff0; color: #ecc94b;">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="quick-action-content">
                                <h3>Manage Scholarships</h3>
                                <p><?= $stats['scholarship_approved'] ?> approved scholarships</p>
                            </div>
                        </a>
                        
                        <a href="users.php" class="quick-action-card">
                            <div class="quick-action-icon" style="background: #ebf8ff; color: #4299e1;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="quick-action-content">
                                <h3>User Management</h3>
                                <p><?= $stats['total_students'] ?> registered students</p>
                            </div>
                        </a>
                        
                        <a href="reports.php" class="quick-action-card">
                            <div class="quick-action-icon" style="background: #faf5ff; color: #9f7aea;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="quick-action-content">
                                <h3>View Reports</h3>
                                <p>Analytics & insights</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Documents -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-file-alt"></i>
                    Recent Documents
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Upload Date</th>
                                    <th>Status</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($recent_docs) && $recent_docs && $recent_docs->num_rows > 0): ?>
                                    <?php while($doc = $recent_docs->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?= htmlspecialchars($doc['doc_id']) ?></strong></td>
                                        <td><?= htmlspecialchars($doc['student_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                                        <td><?= date('M d, Y', strtotime($doc['upload_date'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $doc['status'] == 'Approved' ? 'success' : 
                                                ($doc['status'] == 'Pending' ? 'warning' : 'danger') 
                                            ?>">
                                                <?= htmlspecialchars($doc['status']) ?>
                                            </span>
                                        </td>
                                        <!-- <td>
                                            <a href="document-detail.php?doc_id=<?= $doc['doc_id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </a>
                                        </td> -->
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>No recent documents</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 1.25rem; text-align: right; border-top: 1px solid var(--border-color);">
                        <a href="documents.php" class="btn btn-outline-primary btn-sm">
                            View All Documents <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>