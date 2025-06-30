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
    $conn->query("DELETE FROM quran WHERE id=$id");
    $msg = 'আয়াত ডিলিট হয়েছে!';
}
// Fetch all
$qurans = [];
$result = $conn->query('SELECT * FROM quran ORDER BY id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $qurans[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>কুরআন এডিট/ডিলিট</title>
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
        <h2>কুরআন এডিট/ডিলিট</h2>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <table>
            <tr>
                <th>সূরা</th>
                <th>আয়াত</th>
                <th>অর্থ</th>
                <th>অ্যাকশন</th>
            </tr>
            <?php foreach ($qurans as $q): ?>
            <tr>
                <td><?php echo htmlspecialchars($q['surah']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($q['ayat'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($q['meaning'])); ?></td>
                <td class="actions">
                    <a class="edit-link" href="edit_quran.php?id=<?php echo $q['id']; ?>">এডিট</a>
                    <a href="?delete=<?php echo $q['id']; ?>" onclick="return confirm('ডিলিট করতে চান?');">ডিলিট</a>
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
