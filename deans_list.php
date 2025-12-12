<?php
session_start();
include('include/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Dean', 'Registrar'])) {
    header('Location: landing.php');
    exit();
}

$successMessage = "";
$errorMessage = "";

// Handle Add to Dean's List
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_deans_list'])) {
    $student_id = $_POST['student_id'];
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $gpa = $_POST['gpa'];
    $year_level = $_POST['year_level'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'] ?? '';
    
    $list_id = $_POST['list_id'] ?? null;
    
    try {
        if ($list_id) {
            // Update existing
            $sql = "UPDATE dean_list SET academic_year=?, semester=?, gpa=?, year_level=?, status=?, remarks=?, verified_by=?, verified_date=NOW() WHERE list_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdssiii", $academic_year, $semester, $gpa, $year_level, $status, $remarks, $_SESSION['user_id'], $list_id);
        } else {
            // Insert new
            $sql = "INSERT INTO dean_list (student_id, academic_year, semester, gpa, year_level, status, remarks, verified_by, verified_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdsssi", $student_id, $academic_year, $semester, $gpa, $year_level, $status, $remarks, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            $successMessage = $list_id ? "Dean's list entry updated successfully!" : "Student added to Dean's list successfully!";
            
            // Log audit trail
            $action = $list_id ? 'Updated Dean\'s List Entry' : 'Added to Dean\'s List';
            $audit = "INSERT INTO audit_trail (user_id, action, table_name, record_id, timestamp) VALUES (?, ?, 'dean_list', ?, NOW())";
            $audit_stmt = $conn->prepare($audit);
            $record_id = $list_id ?? $conn->insert_id;
            $audit_stmt->bind_param("isi", $_SESSION['user_id'], $action, $record_id);
            $audit_stmt->execute();
        } else {
            $errorMessage = "Error saving Dean's list entry.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_entry'])) {
    $list_id = $_POST['list_id'];
    $sql = "DELETE FROM dean_list WHERE list_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $list_id);
    
    if ($stmt->execute()) {
        $successMessage = "Entry removed successfully!";
    } else {
        $errorMessage = "Error removing entry.";
    }
}

// Get filters
$year_filter = $_GET['academic_year'] ?? '';
$semester_filter = $_GET['semester'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with proper prepared statements
$sql = "SELECT dl.*, p.firstName, p.lastName, p.student_number, p.course, u.email,
        v.email as verifier_email
        FROM dean_list dl
        INNER JOIN user u ON dl.student_id = u.user_id
        INNER JOIN profile p ON u.user_id = p.user_id
        LEFT JOIN user v ON dl.verified_by = v.user_id
        WHERE 1=1";

$params = [];
$types = "";

if ($year_filter) {
    $sql .= " AND dl.academic_year = ?";
    $params[] = $year_filter;
    $types .= "s";
}
if ($semester_filter) {
    $sql .= " AND dl.semester = ?";
    $params[] = $semester_filter;
    $types .= "s";
}
if ($status_filter) {
    $sql .= " AND dl.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($search) {
    $sql .= " AND (p.firstName LIKE ? OR p.lastName LIKE ? OR p.student_number LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$sql .= " ORDER BY dl.academic_year DESC, dl.semester DESC, dl.gpa DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$deans_list = $stmt->get_result();

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Verified' THEN 1 ELSE 0 END) as verified,
    SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
    AVG(gpa) as avg_gpa
    FROM dean_list";
$stats = $conn->query($stats_sql)->fetch_assoc();

// Get all students for dropdown
$students = $conn->query("SELECT u.user_id, p.student_number, p.firstName, p.lastName, p.year_level 
                          FROM user u 
                          INNER JOIN profile p ON u.user_id = p.user_id 
                          WHERE u.role = 'Student' 
                          ORDER BY p.lastName, p.firstName");

$user_name = $_SESSION['user_name'] ?? $_SESSION['role'];
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FEU Roosevelt - Dean's Lister Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/deans_list.css">
</head>
<body>
  <?php require_once 'include/header.php'; ?>
  
  <div class="container">
    <?php require_once 'include/sidebar.php'; ?>
    
    <main class="main-content">
      <h1 class="page-title"><i class="fas fa-star"></i> Dean's Lister Management</h1>
      
      <?php if($successMessage): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($successMessage) ?></span>
      </div>
      <?php endif; ?>
      
      <?php if($errorMessage): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= htmlspecialchars($errorMessage) ?></span>
      </div>
      <?php endif; ?>
      
      <div class="stats-grid">
        <div class="stat-card">
          <h3>Total Entries</h3>
          <div class="stat-value"><?= number_format($stats['total']) ?></div>
        </div>
        <div class="stat-card">
          <h3>Verified</h3>
          <div class="stat-value"><?= number_format($stats['verified']) ?></div>
        </div>
        <div class="stat-card">
          <h3>Pending</h3>
          <div class="stat-value"><?= number_format($stats['pending']) ?></div>
        </div>
        <div class="stat-card">
          <h3>Average GPA</h3>
          <div class="stat-value"><?= number_format($stats['avg_gpa'], 2) ?></div>
        </div>
      </div>
      
      <div class="toolbar">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="searchInput" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <select id="yearFilter">
          <option value="">All Academic Years</option>
          <option value="2024-2025" <?= $year_filter == '2024-2025' ? 'selected' : '' ?>>2024-2025</option>
          <option value="2025-2026" <?= $year_filter == '2025-2026' ? 'selected' : '' ?>>2025-2026</option>
        </select>
        <select id="semesterFilter">
          <option value="">All Semesters</option>
          <option value="1st Semester" <?= $semester_filter == '1st Semester' ? 'selected' : '' ?>>1st Semester</option>
          <option value="2nd Semester" <?= $semester_filter == '2nd Semester' ? 'selected' : '' ?>>2nd Semester</option>
        </select>
        <select id="statusFilter">
          <option value="">All Status</option>
          <option value="Verified" <?= $status_filter == 'Verified' ? 'selected' : '' ?>>Verified</option>
          <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Rejected" <?= $status_filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button class="btn btn-primary" onclick="openModal()">
          <i class="fas fa-plus"></i> Add to Dean's List
        </button>
      </div>
      
      <div class="content-card">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Academic Year</th>
                <th>Semester</th>
                <th>Year Level</th>
                <th>GPA</th>
                <th>Status</th>
                <th>Verified By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if($deans_list && $deans_list->num_rows > 0): ?>
                <?php while($entry = $deans_list->fetch_assoc()): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($entry['student_number']) ?></strong></td>
                  <td><?= htmlspecialchars($entry['lastName'] . ', ' . $entry['firstName']) ?></td>
                  <td><?= htmlspecialchars($entry['academic_year']) ?></td>
                  <td><?= htmlspecialchars($entry['semester']) ?></td>
                  <td><?= htmlspecialchars($entry['year_level']) ?></td>
                  <td><strong><?= number_format($entry['gpa'], 2) ?></strong></td>
                  <td>
                    <span class="badge badge-<?= strtolower($entry['status']) ?>">
                      <?= htmlspecialchars($entry['status']) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($entry['verifier_email'] ?? 'N/A') ?></td>
                  <td>
                    <button class="btn btn-warning btn-sm" onclick='editEntry(<?= json_encode($entry) ?>)'>
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteEntry(<?= $entry['list_id'] ?>, '<?= htmlspecialchars($entry['firstName']) ?>')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No entries found</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  
  <!-- Modal -->
  <div id="deansModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Add to Dean's List</h2>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>
      <form method="POST">
        <input type="hidden" name="list_id" id="list_id">
        
        <div class="form-group" id="studentField">
          <label>Student *</label>
          <select name="student_id" id="student_id" required>
            <option value="">Select Student</option>
            <?php if($students): while($student = $students->fetch_assoc()): ?>
            <option value="<?= $student['user_id'] ?>" data-year="<?= htmlspecialchars($student['year_level']) ?>">
              <?= htmlspecialchars($student['student_number'] . ' - ' . $student['lastName'] . ', ' . $student['firstName']) ?>
            </option>
            <?php endwhile; endif; ?>
          </select>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Academic Year *</label>
            <select name="academic_year" id="academic_year" required>
              <option value="">Select Year</option>
              <option value="2024-2025">2024-2025</option>
              <option value="2025-2026">2025-2026</option>
            </select>
          </div>
          <div class="form-group">
            <label>Semester *</label>
            <select name="semester" id="semester" required>
              <option value="">Select Semester</option>
              <option value="1st Semester">1st Semester</option>
              <option value="2nd Semester">2nd Semester</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Year Level *</label>
            <select name="year_level" id="year_level" required>
              <option value="">Select Year Level</option>
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="4th Year">4th Year</option>
            </select>
          </div>
          <div class="form-group">
            <label>GPA *</label>
            <input type="number" name="gpa" id="gpa" step="0.01" min="1.00" max="4.00" required>
          </div>
        </div>
        
        <div class="form-group">
          <label>Status *</label>
          <select name="status" id="status" required>
            <option value="Pending">Pending</option>
            <option value="Verified">Verified</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Remarks</label>
          <textarea name="remarks" id="remarks" rows="3"></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
          <button type="submit" name="add_deans_list" class="btn btn-success">
            <i class="fas fa-save"></i> Save Entry
          </button>
          <button type="button" class="btn btn-danger" onclick="closeModal()">
            <i class="fas fa-times"></i> Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <script>
    document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 500));
    document.getElementById('yearFilter').addEventListener('change', applyFilters);
    document.getElementById('semesterFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }
    
    function applyFilters() {
      const search = document.getElementById('searchInput').value;
      const year = document.getElementById('yearFilter').value;
      const semester = document.getElementById('semesterFilter').value;
      const status = document.getElementById('statusFilter').value;
      window.location.href = `deans_list.php?search=${encodeURIComponent(search)}&academic_year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}&status=${encodeURIComponent(status)}`;
    }
    
    function openModal() {
      document.getElementById('modalTitle').textContent = 'Add to Dean\'s List';
      document.querySelector('form').reset();
      document.getElementById('list_id').value = '';
      document.getElementById('studentField').style.display = 'block';
      document.getElementById('deansModal').classList.add('active');
    }
    
    function closeModal() {
      document.getElementById('deansModal').classList.remove('active');
    }
    
    function editEntry(entry) {
      document.getElementById('modalTitle').textContent = 'Edit Dean\'s List Entry';
      document.getElementById('list_id').value = entry.list_id;
      document.getElementById('studentField').style.display = 'none';
      document.getElementById('academic_year').value = entry.academic_year;
      document.getElementById('semester').value = entry.semester;
      document.getElementById('year_level').value = entry.year_level;
      document.getElementById('gpa').value = entry.gpa;
      document.getElementById('status').value = entry.status;
      document.getElementById('remarks').value = entry.remarks || '';
      document.getElementById('deansModal').classList.add('active');
    }
    
    function deleteEntry(listId, name) {
      if(confirm(`Remove ${name} from Dean's List?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="list_id" value="${listId}">
          <input type="hidden" name="delete_entry" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }
    
    // Auto-fill year level when student is selected
    const studentSelect = document.getElementById('student_id');
    if (studentSelect) {
      studentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const yearLevel = selectedOption.getAttribute('data-year');
        if(yearLevel) {
          document.getElementById('year_level').value = yearLevel;
        }
      });
    }
    
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-20px)';
          setTimeout(() => alert.remove(), 300);
        }, 5000);
      });
    });
    
    // Close modal when clicking outside
    document.getElementById('deansModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });
  </script>
</body>
</html>