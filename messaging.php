<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT m.*, u.email FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? ORDER BY m.sent_at DESC");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $job_id = $_POST['job_id'] ?? null;
    $message = $_POST['message'];
    
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, job_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $job_id, $message]);
    header("Location: messaging.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Job Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 800px; margin: 50px auto; padding: 20px; background: #fff; color: #333; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2a5298; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { width: 100%; padding: 10px; background: #2a5298; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1e3c72; }
        .message { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .message p { margin: 5px 0; }
        .nav { margin-bottom: 20px; }
        .nav a { color: #2a5298; text-decoration: none; margin-right: 10px; }
        .nav a:hover { color: #1e3c72; }
        @media (max-width: 768px) { .container { margin: 20px; padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="#" onclick="redirect('index.php')">Home</a>
            <a href="#" onclick="redirect('<?php echo $_SESSION['user_type'] === 'employer' ? 'employer_profile.php' : 'job_seeker_profile.php'; ?>')">Profile</a>
            <a href="#" onclick="redirect('job_search.php')">Search Jobs</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        </div>
        <h2>Messages</h2>
        <form method="POST">
            <div class="form-group">
                <label for="receiver_id">Recipient (User ID)</label>
                <input type="number" id="receiver_id" name="receiver_id" required>
            </div>
            <div class="form-group">
                <label for="job_id">Job ID (Optional)</label>
                <input type="number" id="job_id" name="job_id">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="btn">Send</button>
        </form>
        <h3>Inbox</h3>
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <p><strong>From:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                <p><strong>Message:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                <p><strong>Sent:</strong> <?php echo $message['sent_at']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
