<?php
require "../config/db.php";
require "admin_auth.php";

        // 🔐 Admin session check
        if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
            session_unset();
            session_destroy();
            header("Location: ../auth/login.php");
            exit();
        }

        // 🔔 Notification count (unread)
        $admin_uid = $_SESSION['uid'];

        // 🔔 Fetch all notifications for admin view
        if ($_SESSION['role'] === 'admin') {
            // Admin sees all notifications
          $result = mysqli_query($conn, "
    SELECT n.*, u.role AS sender_role, 
        CASE WHEN u.role='seeker' THEN js.sname
             WHEN u.role='company' THEN c.cname
             ELSE u.uname END AS sender_name
    FROM notifications n
    LEFT JOIN users u ON n.uid = u.uid
    LEFT JOIN job_seeker js ON u.uid = js.uid
    LEFT JOIN company c ON u.uid = c.uid
    GROUP BY n.message
    ORDER BY n.created_at DESC
") or die(mysqli_error($conn));
        
} else {
            // Normal users see only their own notifications
            $uid = (int)$_SESSION['uid'];
            $result = mysqli_query($conn, "
                SELECT * FROM notifications WHERE uid='$uid' ORDER BY created_at DESC
            ") or die(mysqli_error($conn));
        }

        $unread_count_query = mysqli_query($conn, "
            SELECT COUNT(*) AS total 
            FROM notifications 
            WHERE is_read = 0
        ");
        $notification_count = mysqli_fetch_assoc($unread_count_query)['total'] ?? 0;

        // ✅ Fetch counts
        $users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
        $jobseekers = $conn->query("SELECT COUNT(*) as total FROM job_seeker")->fetch_assoc()['total'];
        $companies = $conn->query("SELECT COUNT(*) as total FROM company")->fetch_assoc()['total'];
        $jobs = $conn->query("SELECT COUNT(*) as total FROM job")->fetch_assoc()['total'];
        $applications = $conn->query("SELECT COUNT(*) as total FROM application")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Admin Dashboard</title>

 <link href="../dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>
<body class="bg-black text-white min-h-screen">

<!-- Mobile Hamburger  -->
<div class="fixed top-5 right-5 flex items-center gap-4 lg:hidden z-50">
    <!-- 🔔 Notification Bell -->
<a href="http://localhost/php_program/project/include/notifications.php" class="relative inline-block">
        <i class="fa-solid fa-bell text-2xl text-white hover:text-[#D7AE27] transition"></i>
        <?php if($notification_count > 0): ?>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-black z-10"></span>
        <?php endif; ?>
    </a>

    <!-- Hamburger Button -->
    <button onclick="openSidebar()" class="text-2xl text-[#D7AE27]" aria-label="Open Menu">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div class="flex">

    <!-- Desktop Sidebar -->
    <div class="hidden md:block w-64 bg-white/5 min-h-screen p-6 border-r border-[#D7AE27]/30 fixed">

        <!-- Logo + Name Same Line -->
        <div class="flex items-center gap-3 pb-6 border-b border-[#D7AE27]/20">
            <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
            <span class="text-xl font-bold text-[#D7AE27]">CareerCraft</span>
        </div>

        <ul class="space-y-5 mt-6">
            <li><a href="admin_dashboard.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-chart-line"></i> Dashboard</a></li>
            <li><a href="users.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-users"></i> Users</a></li>
            <li><a href="jobseekers.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-user"></i> job Seekers</a></li>
            <li><a href="companies.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-building"></i> Companies</a></li>
            <li><a href="jobs.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-briefcase"></i> Jobs</a></li>
            <li><a href="applications.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-file-alt"></i> Applications</a></li>
                <li>
                <a href="fraud_reports.php" class="flex gap-3 hover:text-[#D7AE27]">
                    <i class="fa fa-exclamation-triangle"></i> Fraud Reports
                </a>
            </li>
        </ul>
    </div>

    <!-- Mobile Sidebar -->
    <div id="sidebar"
    class="md:hidden w-72 bg-black/85 backdrop-blur-md min-h-screen p-6
           fixed top-0 left-0 transform -translate-x-full
           transition-transform duration-500 ease-in-out z-40">


        <!-- Top Section -->
    <div class="flex justify-between items-center mb-6">

    <!-- Logo + Name -->
    <div class="flex items-center gap-3">
        <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
        <span class="text-xl font-bold text-[#D7AE27]">
            CareerCraft
        </span>
    </div>

    <!-- Close Button (RIGHT SIDE) -->
    <button onclick="closeSidebar()" 
            class="text-2xl text-gray-200 hover:text-[#D7AE27]"
            aria-label="Close Menu">
        &#10005;
    </button>

    </div>

        <ul class="space-y-5 mt-6">
            <li><a href="dashboard.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-chart-line"></i> Dashboard</a></li>
            <li><a href="users.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-users"></i> Users</a></li>
           <li><a href="jobseekers.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-user"></i> Job seekers</a></li>
            <li><a href="companies.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-building"></i> Companies</a></li>
            <li><a href="jobs.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-briefcase"></i> Jobs</a></li>
            <li><a href="applications.php" class="flex gap-3 hover:text-[#D7AE27]">
                <i class="fa fa-file-alt"></i> Applications</a></li>
        </ul>
</div>

    <!-- Main Content -->
    <div class="flex-1 p-6 md:ml-64">

       <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-[#D7AE27]">
        Admin Dashboard
    </h1>

        <div class="hidden lg:flex items-center gap-4">
                <a href="http://localhost/php_program/project/include/notifications.php" class="relative inline-block">
                <i class="fa-solid fa-bell text-2xl text-white hover:text-[#D7AE27] transition"></i>
                <?php if($notification_count > 0): ?>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-black z-10"></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
        <!-- Clickable Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <a href="jobseekers.php"
               class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-lg text-gray-300">Job Seekers</h2>
                <p class="text-3xl font-bold text-[#D7AE27] mt-2">
                    <?php echo $jobseekers; ?>
                </p>
            </a>

            <a href="companies.php"
     class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">                <h2 class="text-lg text-gray-300">Companies</h2>
                <p class="text-3xl font-bold text-[#D7AE27] mt-2">
                    <?php echo $companies; ?>
                </p>
            </a>

            <a href="jobs.php"
     class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">                <h2 class="text-lg text-gray-300">Jobs</h2>
                <p class="text-3xl font-bold text-[#D7AE27] mt-2">
                    <?php echo $jobs; ?>
                </p>
            </a>

            <a href="applications.php"
     class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">                <h2 class="text-lg text-gray-300">Applications</h2>
                <p class="text-3xl font-bold text-[#D7AE27] mt-2">
                    <?php echo $applications; ?>
                </p>
            </a>

        </div>

        <!-- Logout -->
        <div class="mt-10">
            <a href="http://localhost/php_program/project/auth/logout.php"
               class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
               Logout
            </a>
        </div>

    </div>


<!-- Sidebar Script -->
<script>
function openSidebar() {
    document.getElementById("sidebar").classList.remove("-translate-x-full");
}
function closeSidebar() {
    document.getElementById("sidebar").classList.add("-translate-x-full");
}
</script>

</body>
</html>