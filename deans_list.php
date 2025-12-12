<?php
session_start();
include('include/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin','Dean','Registrar'])) {
    header('Location: landing.php');
    exit();
}

$successMessage = '';
$errorMessage   = '';

/* ===============================
   FILTER DEFAULTS
================================ */
$search           = $_GET['search'] ?? '';
$year_filter      = $_GET['academic_year'] ?? '';
$semester_filter  = $_GET['semester'] ?? '';
$status_filter    = $_GET['status'] ?? '';

/* ===============================
   ADD / UPDATE DEAN'S LIST
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_deans_list'])) {

    $student_id    = (int) ($_POST['student_id'] ?? 0); // user.user_id
    $academic_year = $_POST['academic_year'] ?? '';
    $semester      = $_POST['semester'] ?? '';
    $gpa           = (float) ($_POST['gpa'] ?? 0);
    $year_level    = $_POST['year_level'] ?? '';
    $status        = $_POST['status'] ?? 'Pending';
    $remarks       = $_POST['remarks'] ?? '';
    $list_id       = $_POST['list_id'] ?? null;

    if (!$student_id || !$academic_year || !$semester) {
        $errorMessage = 'Missing required fields.';
    } else {

        // Get stud_id from profile
        $p = $conn->prepare("SELECT stud_id FROM profile WHERE user_id=?");
        $p->bind_param("i", $student_id);
        $p->execute();
        $profile = $p->get_result()->fetch_assoc();

        if (!$profile) {
            $errorMessage = 'Student profile not found.';
        } else {

            $stud_id = (int) $profile['stud_id'];

            /* ===============================
               DUPLICATE PREVENTION (INSERT ONLY)
            ================================ */
            if (!$list_id) {
                $dup = $conn->prepare("
                    SELECT list_id 
                    FROM dean_list 
                    WHERE stud_id = ?
                      AND academic_year = ?
                      AND semester = ?
                ");
                $dup->bind_param("iss", $stud_id, $academic_year, $semester);
                $dup->execute();

                if ($dup->get_result()->num_rows > 0) {
                    $errorMessage = "This student is already on the Dean's List for $semester, $academic_year.";
                }
            }

            if (!$errorMessage) {

                if ($list_id) {
                    /* ===== UPDATE ===== */
                    $stmt = $conn->prepare("
                        UPDATE dean_list SET
                            academic_year=?,
                            semester=?,
                            gpa=?,
                            year_level=?,
                            status=?,
                            remarks=?,
                            verified_by=?,
                            verified_date=NOW()
                        WHERE list_id=?
                    ");
                    $stmt->bind_param(
                        "ssdsssii",
                        $academic_year,
                        $semester,
                        $gpa,
                        $year_level,
                        $status,
                        $remarks,
                        $_SESSION['user_id'],
                        $list_id
                    );
                } else {
                    /* ===== INSERT ===== */
                    $stmt = $conn->prepare("
                        INSERT INTO dean_list (
                            student_id,
                            stud_id,
                            academic_year,
                            semester,
                            gpa,
                            year_level,
                            status,
                            remarks,
                            verified_by,
                            verified_date
                        ) VALUES (?,?,?,?,?,?,?,?,?,NOW())
                    ");
                    $stmt->bind_param(
                        "iissdsssi",
                        $student_id,
                        $stud_id,
                        $academic_year,
                        $semester,
                        $gpa,
                        $year_level,
                        $status,
                        $remarks,
                        $_SESSION['user_id']
                    );
                }

                if ($stmt->execute()) {
                    $successMessage = $list_id
                        ? "Dean's List entry updated successfully."
                        : "Student added to Dean's List successfully.";
                } else {
                    $errorMessage = "Failed to save entry.";
                }
            }
        }
    }
}

/* ===============================
   DELETE
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_entry'])) {
    $list_id = (int) $_POST['list_id'];
    $stmt = $conn->prepare("DELETE FROM dean_list WHERE list_id=?");
    $stmt->bind_param("i", $list_id);
    $stmt->execute();
    $successMessage = "Entry removed successfully.";
}

/* ===============================
   FETCH DEAN'S LIST
================================ */
$sql = "
SELECT 
    dl.*,
    p.firstName,
    p.lastName,
    p.student_number,
    p.course,
    v.email AS verifier_email
FROM dean_list dl
INNER JOIN profile p ON dl.stud_id = p.stud_id
LEFT JOIN user v ON dl.verified_by = v.user_id
WHERE 1=1
";

$params = [];
$types  = "";

if ($year_filter) {
    $sql .= " AND dl.academic_year=?";
    $params[] = $year_filter;
    $types .= "s";
}
if ($semester_filter) {
    $sql .= " AND dl.semester=?";
    $params[] = $semester_filter;
    $types .= "s";
}
if ($status_filter) {
    $sql .= " AND dl.status=?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($search) {
    $sql .= " AND (p.firstName LIKE ? OR p.lastName LIKE ? OR p.student_number LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

$sql .= " ORDER BY dl.academic_year DESC, dl.semester DESC, dl.gpa DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$deans_list = $stmt->get_result();

/* ===============================
   STATS
================================ */
$stats = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(status='Verified') AS verified,
        SUM(status='Pending') AS pending,
        AVG(gpa) AS avg_gpa
    FROM dean_list
")->fetch_assoc();

/* ===============================
   STUDENTS FOR MODAL
================================ */
$students = $conn->query("
    SELECT 
        u.user_id,
        p.student_number,
        p.firstName,
        p.lastName,
        p.year_level
    FROM user u
    INNER JOIN profile p ON u.user_id = p.user_id
    WHERE u.role='Student'
    ORDER BY p.lastName, p.firstName
");

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