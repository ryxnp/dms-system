<?php
include('db.php');

if (!isset($_GET['student_id'])) {
    exit('<div class="empty-state">Invalid request</div>');
}

$user_id = intval($_GET['student_id']);

/* 
  STEP 1: Get student_number from profile
*/
$stmt = $conn->prepare("
    SELECT student_number 
    FROM profile 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    exit('<div class="empty-state">Student not found</div>');
}

$student_number = $res->fetch_assoc()['student_number'];

/* 
  STEP 2: Fetch documents using student_number
*/
$stmt = $conn->prepare("
    SELECT 
        doc_id,
        file_name,
        doc_type,
        doc_desc,
        status,
        uploaded_at
    FROM document
    WHERE stud_id = ?
    ORDER BY uploaded_at DESC
");
$stmt->bind_param("s", $student_number);
$stmt->execute();
$docs = $stmt->get_result();

if ($docs->num_rows === 0) {
    echo '
    <div class="empty-state">
        <i class="fas fa-folder-open"></i>
        <p>No documents uploaded</p>
    </div>';
    exit;
}
?>

<div class="content-card">
<div class="table-responsive">
<table>
<thead>
<tr>
    <th>Image</th>
    <th>Type</th>
    <th>Status</th>
    <th>Uploaded</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

<?php while ($d = $docs->fetch_assoc()): 
  $ext = strtolower(pathinfo($d['file_name'], PATHINFO_EXTENSION));
  $fileUrl = "uploads/" . $d['file_name'];
  $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
?>
<tr>
  <td>
    <?php if ($isImage): ?>
      <img 
        src="<?= $fileUrl ?>" 
        class="doc-thumb"
        onclick="openImageViewer('<?= $fileUrl ?>')"
      >
    <?php else: ?>
      <i class="fas fa-file-alt file-icon"></i>
    <?php endif; ?>
  </td>

  <td><?= htmlspecialchars($d['doc_type']) ?></td>

  <td>
    <span class="badge badge-<?= strtolower($d['status']) ?>">
      <?= htmlspecialchars($d['status']) ?>
    </span>
  </td>

  <td><?= date('M d, Y', strtotime($d['uploaded_at'])) ?></td>

  <td>
    <button class="btn btn-warning btn-sm"
      onclick='openDocumentForm(<?= json_encode($d) ?>)'>
      <i class="fas fa-edit"></i>
    </button>

    <button class="btn btn-danger btn-sm"
      onclick="deleteDocument(<?= $d['doc_id'] ?>)">
      <i class="fas fa-trash"></i>
    </button>
  </td>
</tr>
<?php endwhile; ?>


</tbody>
</table>
</div>
</div>
