<?php
session_start();
require_once 'include/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: landing.php');
    exit();
}

$successMessage = '';
$errorMessage   = '';

/* ===========================
   CREATE USER
=========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {

    $stud_id   = trim($_POST['stud_id'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $user_name = trim($_POST['user_name'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? '';

    if (!$email || !$user_name || !$password || !$role) {
        $errorMessage = 'All required fields must be filled.';
    } elseif ($role === 'Student' && !$stud_id) {
        $errorMessage = 'Student ID is required for Student accounts.';
    } else {

        // Check duplicate email or stud_id
        $check = $conn->prepare("
            SELECT user_id 
            FROM user 
            WHERE email = ? OR (stud_id = ? AND ? = 'Student')
        ");
        $check->bind_param("sss", $email, $stud_id, $role);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMessage = 'Email or Student ID already exists.';
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("
                INSERT INTO user (stud_id, email, user_name, password, role)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $stud_id, $email, $user_name, $hashed, $role);
            $stmt->execute();

            $user_id = $conn->insert_id;

            // Auto-create profile if Student
            if ($role === 'Student') {

    $firstName     = trim($_POST['firstName'] ?? '');
    $middleName    = trim($_POST['middleName'] ?? '');
    $lastName      = trim($_POST['lastName'] ?? '');
    $course        = trim($_POST['course'] ?? '');
    $year_level    = $_POST['year_level'] ?? null;
    $contactNumber = trim($_POST['contactNumber'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    if (!$stud_id || !$firstName || !$lastName) {
        $errorMessage = 'Student ID, First Name, and Last Name are required.';
        return;
    }

    $profile = $conn->prepare("
        INSERT INTO profile (
            user_id,
            stud_id,
            student_number,
            firstName,
            middleName,
            lastName,
            course,
            year_level,
            contactNumber,
            address
        ) VALUES (?,?,?,?,?,?,?,?,?,?)
    ");

    $profile->bind_param(
        "isssssssss",
        $user_id,
        $stud_id,
        $stud_id,
        $firstName,
        $middleName,
        $lastName,
        $course,
        $year_level,
        $contactNumber,
        $address
    );

    $profile->execute();
}


            $successMessage = 'User created successfully.';
        }
    }
}

/* ===========================
   FETCH USERS
=========================== */
$users = $conn->query("
    SELECT 
        u.user_id,
        u.stud_id,
        u.email,
        u.user_name,
        u.role,
        u.status,
        p.firstName,
        p.lastName,
        p.course,
        p.year_level
    FROM user u
    LEFT JOIN profile p ON u.user_id = p.user_id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/deans_list.css">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
<?php include 'include/sidebar.php'; ?>

<main class="main-content">

<h1 class="page-title">
  <i class="fas fa-users"></i> User Management
</h1>

<?php if ($successMessage): ?>
<div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage): ?>
<div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<button class="btn btn-primary" onclick="openUserModal()">
  <i class="fas fa-plus"></i> Create User
</button>

<div class="content-card">
<div class="table-responsive">
<table>
<thead>
<tr>
  <th>Student ID</th>
  <th>Name</th>
  <th>Email</th>
  <th>Course</th>
  <th>Year</th>
  <th>Role</th>
</tr>
</thead>
<tbody>

<?php while ($u = $users->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($u['stud_id'] ?? '-') ?></td>
  <td>
    <?= htmlspecialchars(
        trim(($u['lastName'] ?? '') . ', ' . ($u['firstName'] ?? '')) ?: '-'
    ) ?>
  </td>
  <td><?= htmlspecialchars($u['email']) ?></td>
  <td><?= htmlspecialchars($u['course'] ?? '-') ?></td>
  <td><?= htmlspecialchars($u['year_level'] ?? '-') ?></td>
  <td>
    <span class="badge badge-<?= strtolower($u['role']) ?>">
      <?= htmlspecialchars($u['role']) ?>
    </span>
  </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

</main>
</div>

<!-- CREATE USER MODAL -->
<div id="userModal" class="modal">
<div class="modal-content">

<div class="modal-header">
  <h2>Create User</h2>
  <button class="close-btn" onclick="closeUserModal()">&times;</button>
</div>

<form method="POST">
<input type="hidden" name="create_user" value="1">

<div class="form-group">
  <label>Role *</label>
  <select name="role" id="roleSelect" required onchange="toggleStudentFields()">
    <option value="">Select Role</option>
    <option value="Student">Student</option>
    <option value="Admin">Admin</option>
    <option value="Dean">Dean</option>
    <option value="Registrar">Registrar</option>
    <option value="Guidance">Guidance</option>
  </select>
</div>

<!-- STUDENT FIELDS -->
<div id="studentFields" style="display:none;">

  <div class="form-group">
    <label>Student ID *</label>
    <input name="stud_id">
  </div>

  <div class="form-group">
    <label>First Name *</label>
    <input name="firstName">
  </div>

  <div class="form-group">
    <label>Middle Name</label>
    <input name="middleName">
  </div>

  <div class="form-group">
    <label>Last Name *</label>
    <input name="lastName">
  </div>

  <div class="form-group">
    <label>Course</label>
    <input name="course">
  </div>

  <div class="form-group">
    <label>Year Level</label>
    <select name="year_level">
      <option value="">Select</option>
      <option>1st Year</option>
      <option>2nd Year</option>
      <option>3rd Year</option>
      <option>4th Year</option>
    </select>
  </div>

  <div class="form-group">
    <label>Contact Number</label>
    <input name="contactNumber">
  </div>

  <div class="form-group">
    <label>Address</label>
    <textarea name="address"></textarea>
  </div>

</div>

<hr>

<div class="form-group">
  <label>Email *</label>
  <input type="email" name="email" required>
</div>

<div class="form-group">
  <label>Username *</label>
  <input name="user_name" required>
</div>

<div class="form-group">
  <label>Password *</label>
  <input type="password" name="password" required>
</div>

<div class="form-actions">
  <button class="btn btn-success">Create User</button>
  <button type="button" class="btn btn-danger" onclick="closeUserModal()">Cancel</button>
</div>

</form>

</div>
</div>

<script>
function openUserModal(){
  document.getElementById('userModal').classList.add('active');
}
function closeUserModal(){
  document.getElementById('userModal').classList.remove('active');
}

function toggleStudentFields() {
  const role = document.getElementById('roleSelect').value;
  document.getElementById('studentFields').style.display =
    role === 'Student' ? 'block' : 'none';
}
</script>

</body>
</html>
