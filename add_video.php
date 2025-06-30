<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include('../includes/config.php');
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $youtube_link = $_POST['youtube_link'] ?? '';
    $video_file = $_FILES['video_file'] ?? null;
    $upload_path = '';
    if ($title && ($youtube_link || ($video_file && $video_file['error'] === 0))) {
        if ($video_file && $video_file['error'] === 0) {
            $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
            if (in_array($video_file['type'], $allowed_types)) {
                $target_dir = '../uploads/videos/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_ext = pathinfo($video_file['name'], PATHINFO_EXTENSION);
                $file_name = uniqid('video_', true) . '.' . $file_ext;
                $upload_path = $target_dir . $file_name;
                if (move_uploaded_file($video_file['tmp_name'], $upload_path)) {
                    $db_path = 'uploads/videos/' . $file_name;
                    $stmt = $conn->prepare('INSERT INTO videos (title, youtube_link, video_file) VALUES (?, ?, ?)');
                    $stmt->bind_param('sss', $title, $youtube_link, $db_path);
                    if ($stmt->execute()) {
                        $msg = 'ভিডিও সফলভাবে আপলোড হয়েছে!';
                    } else {
                        $msg = 'ডাটাবেজে সমস্যা হয়েছে!';
                    }
                    $stmt->close();
                } else {
                    $msg = 'ফাইল আপলোড করতে সমস্যা হয়েছে!';
                }
            } else {
                $msg = 'শুধুমাত্র mp4, webm, ogg ভিডিও অনুমোদিত!';
            }
        } else {
            // Only YouTube link
            $stmt = $conn->prepare('INSERT INTO videos (title, youtube_link) VALUES (?, ?)');
            $stmt->bind_param('ss', $title, $youtube_link);
            if ($stmt->execute()) {
                $msg = 'ভিডিও সফলভাবে যোগ হয়েছে!';
            } else {
                $msg = 'যোগ করতে সমস্যা হয়েছে!';
            }
            $stmt->close();
        }
    } else {
        $msg = 'সব ঘর পূরণ করুন বা ভিডিও ফাইল দিন!';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ভিডিও যোগ করুন</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .form-box { max-width: 500px; margin: 60px auto; background: #fff; padding: 32px 28px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; }
        h2 { color: #1a4d2e; text-align: center; }
        .msg { color: green; text-align: center; margin-bottom: 10px; }
        .err { color: red; text-align: center; margin-bottom: 10px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #007b5e; color: #fff; border: none; border-radius: 4px; font-size: 17px; margin-top: 18px; }
        button:hover { background: #005f46; }
        .back { display: block; text-align: center; margin-top: 18px; color: #1a4d2e; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>ভিডিও যোগ করুন</h2>
        <?php if ($msg): ?>
            <div class="<?php echo strpos($msg, 'সফলভাবে') !== false ? 'msg' : 'err'; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="title">ভিডিও শিরোনাম</label>
            <input type="text" id="title" name="title" required>
            <label for="youtube_link">ইউটিউব লিংক (ঐচ্ছিক)</label>
            <input type="text" id="youtube_link" name="youtube_link">
            <label for="video_file">ভিডিও ফাইল (mp4, webm, ogg, ঐচ্ছিক)</label>
            <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm,video/ogg">
            <small style="color:#888;">ইউটিউব লিংক অথবা ভিডিও ফাইল যেকোনো একটি দিন।</small>
            <button type="submit">যোগ করুন</button>
        </form>
        <a class="back" href="dashboard.php">← ড্যাশবোর্ডে ফিরে যান</a>
    </div>
</body>
</html>