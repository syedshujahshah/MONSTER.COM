<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'job_seeker') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM job_seeker_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $resume_path = '';

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $resume_path = $upload_dir . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    if ($profile) {
        $stmt = $pdo->prepare("UPDATE job_seeker_profiles SET full_name = ?, skills = ?, experience = ?, resume_path = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $skills, $experience, $resume_path ?: $profile['resume_path'], $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO job_seeker_profiles (user_id, full_name, skills, experience, resume_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $full_name, $skills, $experience, $resume_path]);
    }
    header("Location: job_seeker_profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Profile - Job Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; color: #333; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2a5298; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
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
            <a href="#" onclick="redirect('job_search.php')">Search Jobs</a>
            <a href="#" onclick="redirect('messaging.php')">Messages</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        </div>
        <h2>Job Seeker Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="skills">Skills</label>
                <textarea id="skills" name="skills" required><?php echo htmlspecialchars($profile['skills'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="experience">Experience</label>
                <textarea id="experience" name="experience" required><?php echo htmlspecialchars($profile['experience'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="resume">Upload Resume</label>
                <input type="file" id="resume" name="resume" accept=".pdf">
            </div>
            <button type="submit" class="btn">Save Profile</button>
        </form>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
