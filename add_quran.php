<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include('../includes/config.php');
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surah = $_POST['surah'] ?? '';
    $ayat = $_POST['ayat'] ?? '';
    $meaning = $_POST['meaning'] ?? '';
    if ($surah && $ayat && $meaning) {
        $stmt = $conn->prepare('INSERT INTO quran (surah, ayat, meaning) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $surah, $ayat, $meaning);
        if ($stmt->execute()) {
            $msg = 'আয়াত সফলভাবে যোগ হয়েছে!';
        } else {
            $msg = 'যোগ করতে সমস্যা হয়েছে!';
        }
        $stmt->close();
    } else {
        $msg = 'সব ঘর পূরণ করুন!';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>কুরআন আয়াত যোগ করুন</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .form-box { max-width: 500px; margin: 60px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; }
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
    <div class="form-box">
        <h2>কুরআন আয়াত যোগ করুন</h2>
        <?php if ($msg): ?>
            <div class="<?php echo strpos($msg, 'সফলভাবে') !== false ? 'msg' : 'err'; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="surah">সূরা</label>
            <input type="text" id="surah" name="surah" required>
            <label for="ayat">আয়াত</label>
            <textarea id="ayat" name="ayat" required></textarea>
            <label for="meaning">অর্থ</label>
            <textarea id="meaning" name="meaning" required></textarea>
            <button type="submit">যোগ করুন</button>
        </form>
        <a class="back" href="dashboard.php">← ড্যাশবোর্ডে ফিরে যান</a>
    </div>
</body>
</html>