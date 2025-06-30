<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ড্যাশবোর্ড</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f8fb; margin: 0; padding: 0; }
        .dashboard-box { max-width: 600px; margin: 60px auto; background: #fff; padding: 40px 30px; border-radius: 10px; box-shadow: 0 2px 12px #cfd8dc; text-align: center; }
        h1 { color: #1a4d2e; margin-bottom: 18px; }
        .links { margin: 30px 0; }
        .links a { display: inline-block; margin: 0 12px; padding: 10px 22px; background: #007b5e; color: #fff; border-radius: 5px; text-decoration: none; font-size: 17px; transition: background 0.2s; }
        .links a:hover { background: #005f46; }
        .logout { margin-top: 30px; }
        .logout a { color: #c62828; text-decoration: none; font-weight: bold; }
        .logout a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="dashboard-box">
        <h1>অ্যাডমিন ড্যাশবোর্ড</h1>
        <div class="links">
            <a href="add_quran.php">কুরআন যোগ করুন</a>
            <a href="add_hadith.php">হাদীস যোগ করুন</a>
            <a href="add_video.php">ভিডিও যোগ করুন</a>
            <a href="messages.php">মেসেজ দেখুন</a>
        </div>
        <div style="margin: 40px 0 0 0;">
            <a href="manage_quran.php" style="margin:0 10px; color:#1a4d2e; text-decoration:underline;">কুরআন এডিট/ডিলিট</a>
            <a href="manage_hadith.php" style="margin:0 10px; color:#1a4d2e; text-decoration:underline;">হাদীস এডিট/ডিলিট</a>
            <a href="manage_videos.php" style="margin:0 10px; color:#1a4d2e; text-decoration:underline;">ভিডিও এডিট/ডিলিট</a>
        </div>
        <div class="logout">
            <a href="logout.php">লগআউট</a>
        </div>
    </div>
</body>
</html>