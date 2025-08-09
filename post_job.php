<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $salary_range = $_POST['salary_range'];
    $job_type = $_POST['job_type'];
    $experience_level = $_POST['experience_level'];
    $employer_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, category, location, salary_range, job_type, experience_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$employer_id, $title, $description, $category, $location, $salary_range, $job_type, $experience_level]);
    header("Location: employer_profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job - Job Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; color: #333; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2a5298; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
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
            <a href="#" onclick="redirect('employer_profile.php')">Profile</a>
            <a href="#" onclick="redirect('messaging.php')">Messages</a>
            <a href="#" onclick="redirect('logout.php')">Logout</a>
        </div>
        <h2>Post a Job</h2>
        <form method="POST">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="salary_range">Salary Range</label>
                <input type="text" id="salary_range" name="salary_range" required>
            </div>
            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select id="job_type" name="job_type" required>
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                    <option value="remote">Remote</option>
                </select>
            </div>
            <div class="form-group">
                <label for="experience_level">Experience Level</label>
                <input type="text" id="experience_level" name="experience_level" required>
            </div>
            <button type="submit" class="btn">Post Job</button>
        </form>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
