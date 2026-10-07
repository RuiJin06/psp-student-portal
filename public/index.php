<?php
require __DIR__ . '/../app/bootstrap.php';

$page = $_GET['page'] ?? (user() ? 'profile' : 'login');

if ($page === 'logout') {
    session_unset(); session_destroy(); header('Location: ?page=login'); exit;
}

if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $studentNo = trim($_POST['student_no'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT id, student_no, full_name, password_hash, profile_image FROM students WHERE student_no = ? LIMIT 1');
    $stmt->execute([$studentNo]); $student = $stmt->fetch();
    if ($student && password_verify($password, $student['password_hash'])) {
        session_regenerate_id(true);
        unset($student['password_hash']); $_SESSION['user'] = $student;
        header('Location: ?page=profile'); exit;
    }
    flash('error', 'Invalid student number or password.'); header('Location: ?page=login'); exit;
}

if ($page === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login(); verify_csrf();
    $file = $_FILES['profile_image'] ?? null;
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Please choose an image to upload.');
    } elseif ($file['size'] > 2 * 1024 * 1024) {
        flash('error', 'The image must be 2 MB or smaller.');
    } else {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
            flash('error', 'Only valid JPG, JPEG, and PNG image files are allowed.');
        } else {
            $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
            $directory = __DIR__ . '/uploads';
            if (!is_dir($directory)) mkdir($directory, 0755, true);
            if (move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
                $old = user()['profile_image'] ?? null;
                db()->prepare('UPDATE students SET profile_image = ? WHERE id = ?')->execute([$name, user()['id']]);
                $_SESSION['user']['profile_image'] = $name;
                if ($old && preg_match('/^[a-f0-9]{32}\.(jpg|png)$/', $old)) @unlink($directory . '/' . $old);
                flash('success', 'Profile picture updated successfully.');
            } else { flash('error', 'The upload could not be saved.'); }
        }
    }
    header('Location: ?page=profile'); exit;
}

if ($page !== 'login') require_login();
$message = flash('success'); $error = flash('error'); $currentUser = user();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PSP Student Portal</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<main class="card">
<?php if ($page === 'login'): ?>
  <h1>PSP Student Portal</h1><p class="subtitle">Sign in to manage your student profile.</p>
  <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" action="?page=login"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Student number<input name="student_no" required autocomplete="username" placeholder="10DIT24F1184"></label><label>Password<input name="password" type="password" required autocomplete="current-password"></label><button>Sign in</button></form><p class="hint">Demo account: 10DIT24F1184 / password</p>
<?php else: ?>
  <nav><strong>PSP Student Portal</strong><a href="?page=logout">Log out</a></nav>
  <section class="profile"><div class="avatar"><?php if ($currentUser['profile_image']): ?><img src="uploads/<?= e($currentUser['profile_image']) ?>" alt="Profile picture"><?php else: ?><span><?= e(strtoupper(substr($currentUser['full_name'], 0, 1))) ?></span><?php endif; ?></div><div><h1><?= e($currentUser['full_name']) ?></h1><p><?= e($currentUser['student_no']) ?></p></div></section>
  <?php if ($message): ?><p class="alert success"><?= e($message) ?></p><?php endif; ?><?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
  <h2>Update profile picture</h2><p>Choose a JPG, JPEG, or PNG image no larger than 2 MB.</p><form method="post" action="?page=upload" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Profile picture<input type="file" name="profile_image" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required></label><button>Upload picture</button></form>
<?php endif; ?></main></body></html>
