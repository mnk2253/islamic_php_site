<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include('../includes/config.php');
$msg = '';
// Edit form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $q = $conn->query("SELECT * FROM quran WHERE id=$id");
    $row = $q ? $q->fetch_assoc() : null;
    if (!$row) { echo 'ডেটা পাওয়া যায়নি!'; exit; }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $surah = $_POST['surah'] ?? '';
        $ayat = $_POST['ayat'] ?? '';
        $meaning = $_POST['meaning'] ?? '';
        if ($surah && $ayat && $meaning) {
            $stmt = $conn->prepare('UPDATE quran SET surah=?, ayat=?, meaning=? WHERE id=?');
            $stmt->bind_param('sssi', $surah, $ayat, $meaning, $id);
            if ($stmt->execute()) {
                $msg = 'আপডেট সফল!';
                header('Location: manage_quran.php');
                exit;
            } else {
                $msg = 'আপডেট সমস্যা হয়েছে!';
            }
            $stmt->close();
        } else {
            $msg = 'সব ঘর পূরণ করুন!';
        }
    }
} else { echo 'ID পাওয়া যায়নি!'; exit; }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>কুরআন এডিট</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .box { max-width: 500px; margin: 60px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; }
        h2 { color: #1a4d2e; text-align: center; }
        .msg { color: green; text-align: center; margin-bottom: 10px; }
        .err { color: red; text-align: center; margin-bottom: 10px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; min-height: 80px; }
        button { width: 100%; padding: 12px; background: #007b5e; color: #fff; border: none; border-radius: 4px; font-size: 17px; margin-top: 18px; }
        button:hover { background: #005f46; }
        .back { display: block; text-align: center; margin-top: 18px; color: #1a4d2e; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <h2>কুরআন এডিট</h2>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <form method="post">
            <label for="surah">সূরা</label>
            <input type="text" id="surah" name="surah" value="<?php echo htmlspecialchars($row['surah']); ?>" required>
            <label for="ayat">আয়াত</label>
            <textarea id="ayat" name="ayat" required><?php echo htmlspecialchars($row['ayat']); ?></textarea>
            <label for="meaning">অর্থ</label>
            <textarea id="meaning" name="meaning" required><?php echo htmlspecialchars($row['meaning']); ?></textarea>
            <button type="submit">আপডেট করুন</button>
        </form>
        <a class="back" href="manage_quran.php">← কুরআন ম্যানেজে ফিরে যান</a>
    </div>
</body>
</html>
