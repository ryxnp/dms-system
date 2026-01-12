<?php
session_start();
include('include/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: landing.php');
    exit();
}

$search = $_GET['search'] ?? '';
$year   = $_GET['year'] ?? '';

$sql = "
SELECT 
    u.user_id,
    p.stud_id,
    p.student_number,
    CONCAT(p.lastName, ', ', p.firstName) AS name,
    p.course,
    p.year_level,
    COUNT(d.doc_id) AS total_docs
FROM user u
INNER JOIN profile p ON u.user_id = p.user_id
LEFT JOIN document d ON d.stud_id = p.stud_id
WHERE u.role = 'Student'
";

$params = [];
$types  = "";

if ($search) {
    $sql .= " AND (p.firstName LIKE ? OR p.lastName LIKE ? OR p.student_number LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if ($year) {
    $sql .= " AND p.year_level = ?";
    $params[] = $year;
    $types   .= "s";
}

$sql .= "
GROUP BY 
    u.user_id,
    p.stud_id,
    p.student_number,
    p.firstName,
    p.lastName,
    p.course,
    p.year_level
ORDER BY p.lastName
";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FEU Roosevelt - Documents</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/documents.css">
</head>
<body>

<?php require_once 'include/header.php'; ?>

<div class="container">
<?php require_once 'include/sidebar.php'; ?>

<main class="main-content">

<h1 class="page-title">
  <i class="fas fa-folder"></i> Student Documents
</h1>

<div class="toolbar">
  <div class="search-box">
    <i class="fas fa-search"></i>
    <input id="searchInput" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>">
  </div>

  <select id="yearFilter">
    <option value="">All Year Levels</option>
    <option <?= $year=='1st Year'?'selected':'' ?>>1st Year</option>
    <option <?= $year=='2nd Year'?'selected':'' ?>>2nd Year</option>
    <option <?= $year=='3rd Year'?'selected':'' ?>>3rd Year</option>
    <option <?= $year=='4th Year'?'selected':'' ?>>4th Year</option>
  </select>
</div>

<div class="content-card">
<div class="table-responsive">
<table>
<thead>
<tr>
  <th>Student No</th>
  <th>Name</th>
  <th>Course</th>
  <th>Year</th>
  <th>Total Docs</th>
  <th>Actions</th>
</tr>
</thead>
<tbody>

<?php while($s = $students->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($s['student_number']) ?></td>
  <td><?= htmlspecialchars($s['name']) ?></td>
  <td><?= htmlspecialchars($s['course']) ?></td>
  <td><?= htmlspecialchars($s['year_level']) ?></td>
  <td><?= (int)$s['total_docs'] ?></td>
  <td>
    <button class="btn btn-primary btn-sm"
      onclick="openDocumentsModal(
        <?= (int)$s['user_id'] ?>,
        '<?= htmlspecialchars($s['stud_id'], ENT_QUOTES) ?>',
        '<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>'
      )">
      <i class="fas fa-folder-open"></i> View Documents
    </button>
  </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

</main>
</div>

<!-- ===================== DOCUMENTS MODAL ===================== -->
<div id="documentsModal" class="modal">
<div class="modal-content large">
<div class="modal-header">
  <h2 id="studentTitle"></h2>
  <button class="close-btn" onclick="closeDocumentsModal()">&times;</button>
</div>

<div id="documentsContainer"></div>

<button class="btn btn-success" onclick="openDocumentForm()">
  <i class="fas fa-plus"></i> Add Document
</button>
<button class="btn btn-secondary" onclick="printDocuments()">
  <i class="fas fa-print"></i> Print
</button>

</div>
</div>

<!-- ===================== ADD / EDIT DOCUMENT MODAL ===================== -->
<div id="documentFormModal" class="modal">
<div class="modal-content">
<div class="modal-header">
  <h2 id="formTitle">Add Document</h2>
  <button class="close-btn" onclick="closeFormModal()">&times;</button>
</div>

<form id="documentForm" enctype="multipart/form-data">
<input type="hidden" name="doc_id" id="doc_id">
<input type="hidden" name="stud_id" id="stud_id">
<input type="hidden" name="student_id" id="student_id">

<div class="form-group">
  <label>Document Name</label>
  <input name="doc_name" required>
</div>

<div class="form-group">
  <label>Document Type</label>
  <select name="doc_type" required>
    <option value="">Select Document Type</option>
    <option>Grades</option>
    <option>ID</option>
    <option>Certificate</option>
    <option>Enrollment Form</option>
    <option>Others</option>
  </select>
</div>

<div class="form-group">
  <label>Description</label>
  <textarea name="doc_desc"></textarea>
</div>

<div class="form-group">
  <label>Status</label>
  <select name="status">
    <option>Pending</option>
    <option>Approved</option>
    <option>Declined</option>
  </select>
</div>

<div class="form-group">
  <label>Upload File</label>
  <input 
  type="file" 
  name="file"
  accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
>
</div>

<div class="form-actions">
  <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
  <button type="button" class="btn btn-danger" onclick="closeFormModal()">Cancel</button>
</div>
</form>
</div>
</div>

<!-- IMAGE VIEWER -->
<div id="imageViewer" class="image-viewer" onclick="closeImageViewer()">
  <span class="close-viewer">&times;</span>
  <img id="viewerImage">
</div>

<script>
let currentUserId = null;
let currentStudId = null;

/* =========================== STUDENT DOCUMENTS =========================== */
function openDocumentsModal(userId, studId, name) {
  currentUserId = userId;
  currentStudId = studId;

  document.getElementById('student_id').value = userId;
  document.getElementById('stud_id').value = studId;

  document.getElementById('studentTitle').innerText = name;
  document.getElementById('documentsModal').classList.add('active');
  loadDocuments();
}

function closeDocumentsModal() {
  document.getElementById('documentsModal').classList.remove('active');
}

function loadDocuments() {
  fetch(`include/fetch_student_documents.php?student_id=${currentUserId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('documentsContainer').innerHTML = html;
    });
}

/* =========================== ADD / EDIT =========================== */
function openDocumentForm(doc = null) {
  document.getElementById('documentForm').reset();
  document.getElementById('doc_id').value = '';
  document.getElementById('stud_id').value = currentStudId;
  document.getElementById('student_id').value = currentUserId;

  document.getElementById('formTitle').innerText =
    doc ? 'Edit Document' : 'Add Document';

  if (doc) {
    document.getElementById('doc_id').value = doc.doc_id;
    document.querySelector('[name="doc_name"]').value = doc.doc_name;
    document.querySelector('[name="doc_type"]').value = doc.doc_type;
    document.querySelector('[name="doc_desc"]').value = doc.doc_desc || '';
    document.querySelector('[name="status"]').value = doc.status;
  }

  document.getElementById('documentFormModal').classList.add('active');
}

function closeFormModal() {
  document.getElementById('documentFormModal').classList.remove('active');
}

document.getElementById('documentForm').addEventListener('submit', e => {
  e.preventDefault();
  fetch('include/process_document.php', {
    method: 'POST',
    body: new FormData(e.target)
  }).then(() => {
    closeFormModal();
    loadDocuments();
  });
});

/* =========================== DELETE =========================== */
function deleteDocument(id) {
  if (!confirm('Delete this document?')) return;
  fetch('include/process_document.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `delete_document=1&doc_id=${id}`
  }).then(() => loadDocuments());
}

/* =========================== FILTERING =========================== */
function applyFilters() {
  const s = document.getElementById('searchInput').value;
  const y = document.getElementById('yearFilter').value;
  location = `documents.php?search=${encodeURIComponent(s)}&year=${encodeURIComponent(y)}`;
}

document.getElementById('searchInput')
  .addEventListener('input', () => setTimeout(applyFilters, 400));
document.getElementById('yearFilter')
  .addEventListener('change', applyFilters);


function openImageViewer(src) {
  const viewer = document.getElementById('imageViewer');
  const img = document.getElementById('viewerImage');
  img.src = src;
  viewer.classList.add('active');
}

function closeImageViewer() {
  document.getElementById('imageViewer').classList.remove('active');
}

function printDocuments() {
  const container = document.getElementById('documentsContainer').cloneNode(true);
  const title = document.getElementById('studentTitle').innerText;

  // Convert relative image paths to absolute
  container.querySelectorAll('img').forEach(img => {
    img.src = new URL(img.getAttribute('src'), window.location.href).href;
  });

  const printWindow = window.open('', '', 'width=900,height=650');

  printWindow.document.write(`
    <html>
    <head>
      <title>Student Documents</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          padding: 20px;
        }
        h2 {
          margin-bottom: 15px;
        }
        table {
          width: 100%;
          border-collapse: collapse;
        }
        th, td {
          border: 1px solid #ccc;
          padding: 8px;
          text-align: left;
        }
        th {
          background: #f3f3f3;
        }
        img {
          max-width: 80px;
          height: auto;
        }
        button, a.btn {
          display: none !important;
        }
      </style>
    </head>
    <body>
      <h2>${title} – Documents</h2>
      ${container.innerHTML}
    </body>
    </html>
  `);

  printWindow.document.close();

  // ⏳ Wait for images before printing
  printWindow.onload = () => {
    setTimeout(() => {
      printWindow.focus();
      printWindow.print();
    }, 300);
  };
}

</script>

</body>
</html>
