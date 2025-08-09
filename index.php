<?php
session_start();
require_once 'db.php';

$stmt = $pdo->query("SELECT j.*, e.company_name FROM jobs j JOIN employer_profiles e ON j.employer_id = e.user_id ORDER BY j.posted_at DESC LIMIT 5");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Home</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #fff; color: #333; padding: 10px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header a { color: #2a5298; text-decoration: none; font-weight: bold; }
        .header a:hover { color: #1e3c72; }
        .job-list { margin-top: 20px; }
        .job-card { background: #fff; color: #333; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .job-card h3 { margin: 0 0 10px; color: #2a5298; }
        .job-card p { margin: 5px 0; }
        .btn { padding: 10px 20px; background: #2a5298; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1e3c72; }
        @media (max-width: 768px) { .container { padding: 10px; } .header { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Job Portal</h1>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirect('<?php echo $_SESSION['user_type'] === 'employer' ? 'employer_profile.php' : 'job_seeker_profile.php'; ?>')">Profile</a>
                <a href="#" onclick="redirect('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirect('signup.php')">Sign Up</a>
                <a href="#" onclick="redirect('login.php')">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <h2>Featured Jobs</h2>
        <div class="job-list">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                    <button class="btn" onclick="redirect('job_search.php')">View Details</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
