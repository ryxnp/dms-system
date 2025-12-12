<?php
session_start();
require_once 'db.php';

/* ===========================
   SECURITY
=========================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    exit('Unauthorized');
}

/* ===========================
   CONFIG
=========================== */
$uploadDir  = __DIR__ . '/../uploads/';
$filePath  = 'uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ===========================
   DELETE DOCUMENT
=========================== */
if (isset($_POST['delete_document'], $_POST['doc_id'])) {

    $doc_id = (int)$_POST['doc_id'];

    $stmt = $conn->prepare("SELECT file_name FROM document WHERE doc_id=?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();

    if ($doc && $doc['file_name']) {
        $file = $uploadDir . $doc['file_name'];
        if (file_exists($file)) unlink($file);
    }

    $del = $conn->prepare("DELETE FROM document WHERE doc_id=?");
    $del->bind_param("i", $doc_id);
    $del->execute();
    exit;
}

/* ===========================
   INPUTS
=========================== */
$doc_id   = $_POST['doc_id'] ?? null;
$stud_id  = $_POST['stud_id'] ?? null;
$doc_name = trim($_POST['doc_name'] ?? '');
$doc_type = trim($_POST['doc_type'] ?? '');
$doc_desc = trim($_POST['doc_desc'] ?? '');
$status   = $_POST['status'] ?? 'Pending';

if (!$stud_id || !$doc_name || !$doc_type) {
    http_response_code(422);
    exit('Missing required fields');
}

/* ===========================
   FILE UPLOAD
=========================== */
$newFileName = null;
$fileSize = null;

if (!empty($_FILES['file']['name'])) {

    $allowed = ['pdf','jpg','jpeg','png','doc','docx','xls','xlsx'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        http_response_code(422);
        exit('Invalid file type');
    }

    $rand = random_int(100000, 999999);
    $newFileName = "{$stud_id}_{$rand}.{$ext}";
    $target = $uploadDir . $newFileName;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        http_response_code(500);
        exit('File upload failed');
    }

    $fileSize = $_FILES['file']['size'];
}

/* ===========================
   CREATE DOCUMENT
=========================== */

if (!$doc_id) {

    if (!$newFileName) {
        http_response_code(422);
        exit('File is required');
    }

    $sql = "
        INSERT INTO document (
            student_id,
            stud_id,
            doc_name,
            file_name,
            file_path,
            file_size,
            doc_type,
            doc_desc,
            status,
            created_by,
            upload_date
        ) VALUES (?,?,?,?,?,?,?,?,?,?, NOW())
    ";

    $createdBy = $_SESSION['user_name'];

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssss",
        $_POST['student_id'], // 👈 INTEGER (user_id)
        $stud_id,             // 👈 STRING (student number)
        $doc_name,
        $newFileName,
        $filePath,
        $fileSize,
        $doc_type,
        $doc_desc,
        $status,
        $createdBy
    );

    $stmt->execute();
    exit;
}


/* ===========================
   UPDATE
=========================== */
if ($newFileName) {
    $old = $conn->prepare("SELECT file_name FROM document WHERE doc_id=?");
    $old->bind_param("i", $doc_id);
    $old->execute();
    $oldFile = $old->get_result()->fetch_assoc();

    if ($oldFile && $oldFile['file_name']) {
        $oldPath = $uploadDir . $oldFile['file_name'];
        if (file_exists($oldPath)) unlink($oldPath);
    }
}

$sql = "
    UPDATE document SET
        doc_name=?,
        doc_type=?,
        doc_desc=?,
        status=?,
        file_name=COALESCE(?, file_name),
        file_size=COALESCE(?, file_size),
        file_path=?,
        updated_at=NOW(),
        reviewed_by=?
    WHERE doc_id=?
";

$reviewedBy = $_SESSION['user_id'];

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssisii",
    $doc_name,
    $doc_type,
    $doc_desc,
    $status,
    $newFileName,
    $fileSize,
    $filePath,
    $reviewedBy,
    $doc_id
);
$stmt->execute();
exit;
