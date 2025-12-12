<?php
session_start();
require_once 'include/db.php';

/* ===========================
   AUTH
=========================== */
if (!isset($_SESSION['user_id'])) {
    header('Location: landing.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

$success = '';
$error   = '';

/* ===========================
   FETCH USER
=========================== */
$stmt = $conn->prepare("
    SELECT user_id, email, user_name, role, status, last_login
    FROM user
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ===========================
   FETCH PROFILE (IF STUDENT)
=========================== */
$profile = null;
if ($role === 'Student') {
    $stmt = $conn->prepare("
        SELECT *
        FROM profile
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
}

/* ===========================
   UPDATE PROFILE
=========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $user_name = trim($_POST['user_name']);

    if (!$user_name) {
        $error = "Username cannot be empty.";
    } else {

        // update user
        $stmt = $conn->prepare("
            UPDATE user SET user_name = ?
            WHERE user_id = ?
        ");
        $stmt->bind_param("si", $user_name, $user_id);
        $stmt->execute();

        // update student profile
        if ($role === 'Student') {
            $stmt = $conn->prepare("
                UPDATE profile SET
                    firstName = ?,
                    lastName = ?,
                    middleName = ?,
                    course = ?,
                    year_level = ?,
                    contactNumber = ?,
                    address = ?
                WHERE user_id = ?
            ");
            $stmt->bind_param(
                "sssssssi",
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['middleName'],
                $_POST['course'],
                $_POST['year_level'],
                $_POST['contactNumber'],
                $_POST['address'],
                $user_id
            );
            $stmt->execute();
        }

        $success = "Profile updated successfully.";
    }
}

/* ===========================
   CHANGE PASSWORD
=========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {

    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!$current || !$new || !$confirm) {
        $error = "All password fields are required.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {

        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $hash = $stmt->get_result()->fetch_assoc()['password'];

        if (!password_verify($current, $hash)) {
            $error = "Current password is incorrect.";
        } else {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET password=? WHERE user_id=?");
            $stmt->bind_param("si", $newHash, $user_id);
            $stmt->execute();
            $success = "Password changed successfully.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Account Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/settings.css">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
<?php include 'include/sidebar.php'; ?>

<main class="main-content">
<h1 class="page-title"><i class="fas fa-user-cog"></i> Account Settings</h1>

<?php if ($success): ?>
<div class="settings-alert success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="settings-alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- ===========================
     BASIC ACCOUNT INFO
=========================== -->
<div class="settings-card">
<h2><i class="fas fa-id-badge"></i> Account Information</h2>

<form method="POST" class="settings-form">
    <div class="settings-group">
        <label>Email</label>
        <input value="<?= htmlspecialchars($user['email']) ?>" disabled>
    </div>

    <div class="settings-group">
        <label>Username</label>
        <input name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required>
    </div>

    <div class="settings-group">
        <label>Role</label>
        <input value="<?= $user['role'] ?>" disabled>
    </div>

    <div class="settings-group">
        <label>Status</label>
        <input value="<?= $user['status'] ?>" disabled>
    </div>

    <input type="hidden" name="update_profile" value="1">

    <div class="settings-actions">
        <button class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
    </div>
</form>
</div>

<!-- ===========================
     STUDENT PROFILE
=========================== -->
<?php if ($role === 'Student' && $profile): ?>
<div class="settings-card">
<h2><i class="fas fa-user-graduate"></i> Student Profile</h2>

<form method="POST" class="settings-form full">
    <div class="settings-form">
        <div class="settings-group">
            <label>First Name</label>
            <input name="firstName" value="<?= $profile['firstName'] ?>">
        </div>
        <div class="settings-group">
            <label>Last Name</label>
            <input name="lastName" value="<?= $profile['lastName'] ?>">
        </div>
        <div class="settings-group">
            <label>Middle Name</label>
            <input name="middleName" value="<?= $profile['middleName'] ?>">
        </div>
        <div class="settings-group">
            <label>Course</label>
            <input name="course" value="<?= $profile['course'] ?>">
        </div>
        <div class="settings-group">
            <label>Year Level</label>
            <select name="year_level">
                <option <?= $profile['year_level']=='1st Year'?'selected':'' ?>>1st Year</option>
                <option <?= $profile['year_level']=='2nd Year'?'selected':'' ?>>2nd Year</option>
                <option <?= $profile['year_level']=='3rd Year'?'selected':'' ?>>3rd Year</option>
                <option <?= $profile['year_level']=='4th Year'?'selected':'' ?>>4th Year</option>
            </select>
        </div>
        <div class="settings-group">
            <label>Contact Number</label>
            <input name="contactNumber" value="<?= $profile['contactNumber'] ?>">
        </div>
        <div class="settings-group">
            <label>Address</label>
            <textarea name="address"><?= $profile['address'] ?></textarea>
        </div>
    </div>

    <input type="hidden" name="update_profile" value="1">

    <div class="settings-actions">
        <button class="btn-save"><i class="fas fa-save"></i> Update Profile</button>
    </div>
</form>
</div>
<?php endif; ?>

<!-- ===========================
     CHANGE PASSWORD
=========================== -->
<div class="settings-card">
<h2><i class="fas fa-lock"></i> Change Password</h2>

<form method="POST" class="settings-form">
    <div class="settings-group">
        <label>Current Password</label>
        <input type="password" name="current_password">
    </div>
    <div class="settings-group">
        <label>New Password</label>
        <input type="password" name="new_password">
    </div>
    <div class="settings-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password">
    </div>

    <input type="hidden" name="change_password" value="1">

    <div class="settings-actions">
        <button class="btn-save"><i class="fas fa-key"></i> Change Password</button>
    </div>
</form>
</div>

</main>
</div>
</body>
</html>
