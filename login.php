<?php
session_start();
include('../includes/config.php');
// যদি ইতিমধ্যে লগইন করা থাকে, ড্যাশবোর্ডে পাঠান
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT password FROM admin WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'ভুল ইউজারনেম অথবা পাসওয়ার্ড!';
        }
    } else {
        $error = 'ভুল ইউজারনেম অথবা পাসওয়ার্ড!';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>অ্যাডমিন লগইন</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .login-box { max-width: 350px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h2 { text-align: center; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #007b5e; color: #fff; border: none; border-radius: 4px; font-size: 16px; }
        button:hover { background: #005f46; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>অ্যাডমিন লগইন</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="username">ইউজারনেম</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">পাসওয়ার্ড</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">লগইন</button>
            <!-- Removed demo credentials and password reset/phone info as per request -->
        </form>
    </div>
</body>
</html>