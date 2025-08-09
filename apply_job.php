<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'job_seeker') {
    header("Location: login.php");
    exit;
}

$job_id = $_GET['job_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header("Location: job_search.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_seeker_id = $_SESSION['user_id'];
    $resume_path = '';

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $resume_path = $upload_dir . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    $stmt = $pdo->prepare("INSERT INTO applications (job_id, job_seeker_id, resume_path) VALUES (?, ?, ?)");
    $stmt->execute([$job_id, $job_seeker_id, $resume_path]);
    header("Location: job_seeker_profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - Job Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; color: #333; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2a5298; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { width: 100%; padding: 10px; background: #2a5298; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1e3c72; }
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
            <a href="#" onclick="redirect('job_seeker_profile.php')">Profile</a>
            <a href="#" onclick="redirect('job_search.php')">Search Jobs</a>
            <a href="#" onclick="redirect('messaging.php')">Messages</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        </div>
        <h2>Apply for <?php echo htmlspecialchars($job['title']); ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="resume">Upload Resume</label>
                <input type="file" id="resume" name="resume" accept=".pdf" required>
            </div>
            <button type="submit" class="btn">Apply</button>
        </form>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
