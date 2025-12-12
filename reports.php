<?php
session_start();
include('include/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: landing.php');
    exit();
}

/* ===========================
   QUERIES
=========================== */

// Documents by status
$docStatus = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM document
    GROUP BY status
");

// Documents uploaded per month
$docTimeline = $conn->query("
    SELECT DATE_FORMAT(upload_date,'%Y-%m') AS month, COUNT(*) AS total
    FROM document
    GROUP BY month
    ORDER BY month
");

// Students per year level
$studentsByYear = $conn->query("
    SELECT year_level, COUNT(*) AS total
    FROM profile
    GROUP BY year_level
");

// Users per role
$usersByRole = $conn->query("
    SELECT role, COUNT(*) AS total
    FROM user
    GROUP BY role
");

/* ===========================
   FETCH ONCE (IMPORTANT)
=========================== */
$docStatusData      = $docStatus->fetch_all(MYSQLI_ASSOC);
$docTimelineData    = $docTimeline->fetch_all(MYSQLI_ASSOC);
$studentsByYearData = $studentsByYear->fetch_all(MYSQLI_ASSOC);
$usersByRoleData    = $usersByRole->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/reports.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php require_once 'include/header.php'; ?>

<div class="container">
<?php require_once 'include/sidebar.php'; ?>

<main class="main-content">

<h1 class="page-title">
  <i class="fas fa-chart-bar"></i> Reports & Analytics
</h1>

<div class="chart-grid">

  <!-- DOCUMENT STATUS -->
  <div class="chart-card">
    <h3>Documents by Status</h3>
    <canvas id="docStatusChart"></canvas>
  </div>

  <!-- DOCUMENT TIMELINE -->
  <div class="chart-card">
    <h3>Documents Uploaded Over Time</h3>
    <canvas id="docTimelineChart"></canvas>
  </div>

  <!-- STUDENTS BY YEAR -->
  <div class="chart-card">
    <h3>Students by Year Level</h3>
    <canvas id="studentsByYearChart"></canvas>
  </div>

  <!-- USERS BY ROLE -->
  <div class="chart-card">
    <h3>Users by Role</h3>
    <canvas id="usersByRoleChart"></canvas>
  </div>

</div>

</main>
</div>

<script>
/* ===========================
   DOCUMENT STATUS
=========================== */
new Chart(document.getElementById('docStatusChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($docStatusData,'status')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($docStatusData,'total')) ?>,
      backgroundColor: ['#facc15','#22c55e','#ef4444']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: { legend: { position: 'bottom' } }
  }
});

/* ===========================
   DOCUMENT TIMELINE
=========================== */
new Chart(document.getElementById('docTimelineChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($docTimelineData,'month')) ?>,
    datasets: [{
      label: 'Uploads',
      data: <?= json_encode(array_column($docTimelineData,'total')) ?>,
      borderWidth: 2,
      tension: 0.3,
      fill: false
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false
  }
});

/* ===========================
   STUDENTS BY YEAR
=========================== */
new Chart(document.getElementById('studentsByYearChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($studentsByYearData,'year_level')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($studentsByYearData,'total')) ?>,
      backgroundColor: '#3b82f6'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false
  }
});

/* ===========================
   USERS BY ROLE
=========================== */
new Chart(document.getElementById('usersByRoleChart'), {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($usersByRoleData,'role')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($usersByRoleData,'total')) ?>,
      backgroundColor: ['#22c55e','#f97316','#6366f1','#ef4444','#0ea5e9']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } }
  }
});
</script>

</body>
</html>
