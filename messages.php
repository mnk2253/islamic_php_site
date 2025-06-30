<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include('../includes/config.php');
$messages = [];
$result = $conn->query('SELECT * FROM messages ORDER BY id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>মেসেজসমূহ</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .msg-box { max-width: 800px; margin: 60px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; }
        h2 { color: #1a4d2e; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #e0f2f1; }
        tr:nth-child(even) { background: #f9f9f9; }
        .back { display: block; text-align: center; margin-top: 18px; color: #1a4d2e; text-decoration: underline; }
        .no-msg { text-align: center; color: #c62828; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="msg-box">
        <h2>ইউজার মেসেজসমূহ</h2>
        <?php if (count($messages) > 0): ?>
        <table>
            <tr>
                <th>নাম</th>
                <th>ইমেইল</th>
                <th>মেসেজ</th>
            </tr>
            <?php foreach ($messages as $msg): ?>
            <tr>
                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <div class="no-msg">কোনো মেসেজ পাওয়া যায়নি।</div>
        <?php endif; ?>
        <a class="back" href="dashboard.php">← ড্যাশবোর্ডে ফিরে যান</a>
    </div>
</body>
</html>