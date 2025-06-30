<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include('../includes/config.php');
$msg = '';
// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM hadith WHERE id=$id");
    $msg = 'হাদীস ডিলিট হয়েছে!';
}
// Fetch all
$hadiths = [];
$result = $conn->query('SELECT * FROM hadith ORDER BY id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hadiths[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>হাদীস এডিট/ডিলিট</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .box { max-width: 900px; margin: 40px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; }
        h2 { color: #1a4d2e; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #e0f2f1; }
        tr:nth-child(even) { background: #f9f9f9; }
        .actions a { margin-right: 10px; color: #c62828; text-decoration: underline; }
        .msg { color: green; text-align: center; margin-bottom: 10px; }
        .edit-link { color: #007b5e; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <h2>হাদীস এডিট/ডিলিট</h2>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <table>
            <tr>
                <th>বর্ণনাকারী</th>
                <th>হাদীস</th>
                <th>উৎস</th>
                <th>অ্যাকশন</th>
            </tr>
            <?php foreach ($hadiths as $h): ?>
            <tr>
                <td><?php echo htmlspecialchars($h['narrator']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($h['hadith_text'])); ?></td>
                <td><?php echo htmlspecialchars($h['source']); ?></td>
                <td class="actions">
                    <a class="edit-link" href="edit_hadith.php?id=<?php echo $h['id']; ?>">এডিট</a>
                    <a href="?delete=<?php echo $h['id']; ?>" onclick="return confirm('ডিলিট করতে চান?');">ডিলিট</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div style="text-align:center; margin-top:20px;">
            <a href="dashboard.php">← ড্যাশবোর্ডে ফিরে যান</a>
        </div>
    </div>
</body>
</html>
