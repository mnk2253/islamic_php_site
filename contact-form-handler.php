<?php
// Contact form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    if ($name && $email && $message) {
        include('includes/config.php');
        $stmt = $conn->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        if ($stmt->execute()) {
            header('Location: pages/contact.php?success=1');
            exit();
        } else {
            header('Location: pages/contact.php?error=1');
            exit();
        }
        $stmt->close();
    } else {
        header('Location: pages/contact.php?error=1');
        exit();
    }
} else {
    header('Location: pages/contact.php');
    exit();
}
?>