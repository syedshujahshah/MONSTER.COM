<?php
session_start();
require_once 'db.php';

$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$job_type = $_GET['job_type'] ?? '';
$salary_range = $_GET['salary_range'] ?? '';

$query = "SELECT j.*, e.company_name FROM jobs j JOIN employer_profiles e ON j.employer_id = e.user_id WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND j.category LIKE ?";
    $params[] = "%$category%";
}
if ($location) {
    $query .= " AND j.location LIKE ?";
    $params[] = "%$location%";
}
if ($job_type) {
    $query .= " AND j.job_type = ?";
    $params[] = $job_type;
}
if ($salary_range) {
    $query .= " AND j.salary_range LIKE ?";
    $params[] = "%$salary_range%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Search - Job Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: linear-gradient(to right, #1e3c72, #2a5298); color: #fff; }
        .container { max-width: 1200px; margin: 50px auto; padding: 20px; }
        h2 { text-align: center; color: #fff; }
        .search-form { background: #fff; color: #333; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .job-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .job-card { background: #fff; color: #333; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .job-card h3 { margin: 0 0 10px; color: #2a5298; }
        .job-card p { margin: 5px 0; }
        .btn { padding: 10px 20px; background: #2a5298; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1e3c72; }
        .nav { margin-bottom: 20px; }
        .nav a { color: #fff; text-decoration: none; margin-right: 10px; }
        .nav a:hover { color: #ccc; }
        @media (max-width: 768px) { .container { margin: 20px; padding: 10px; } .job-list { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="#" onclick="redirect('index.php')">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirect('<?php echo $_SESSION['user_type'] === 'employer' ? 'employer_profile.php' : 'job_seeker_profile.php'; ?>')">Profile</a>
                <a href="#" onclick="redirect('messaging.php')">Messages</a>
                <a href="#" onclick="redirect('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirect('login.php')">Login</a>
            <?php endif; ?>
        </div>
        <h2>Search Jobs</h2>
        <div class="search-form">
            <form method="GET">
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>">
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">
                </div>
                <div class="form-group">
                    <label for="job_type">Job Type</label>
                    <select id="job_type" name="job_type">
                        <option value="">Any</option>
                        <option value="full-time" <?php echo $job_type === 'full-time' ? 'selected' : ''; ?>>Full-Time</option>
                        <option value="part-time" <?php echo $job_type === 'part-time' ? 'selected' : ''; ?>>Part-Time</option>
                        <option value="remote" <?php echo $job_type === 'remote' ? 'selected' : ''; ?>>Remote</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="salary_range">Salary Range</label>
                    <input type="text" id="salary_range" name="salary_range" value="<?php echo htmlspecialchars($salary_range); ?>">
                </div>
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
        <div class="job-list">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'job_seeker'): ?>
                        <button class="btn" onclick="redirect('apply_job.php?job_id=<?php echo $job['id']; ?>')">Apply</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function redirect(url) { window.location.href = url; }
    </script>
</body>
</html>
