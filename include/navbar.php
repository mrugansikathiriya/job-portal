<?php
// Start session at the very top (before any HTML)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$notification_count = 0;

if(isset($_SESSION['uid'])){
    $uid = (int)$_SESSION['uid'];

    require __DIR__ . "/../config/db.php";

    $nq = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM notifications 
        WHERE uid = $uid AND is_read = 0
    ") or die(mysqli_error($conn));

    $nr = mysqli_fetch_assoc($nq);
    $notification_count = $nr['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerCraft | Job Portal</title>
    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="image/logo3.jpg" type="image/png">
</head>
<style>
    /* progress */
    #scroll-progress-circle {
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
        transition: stroke-dashoffset 0.2s linear;
    }

    nav { height: 60px; } /* Reduce navbar height */
</style>    
<body>
    <!-- Scroll Progress Circle with Arrow -->
    <div id="scroll-progress-container"
         class="fixed right-6 bottom-8 w-14 h-14 opacity-0 transition-opacity duration-300 z-50 cursor-pointer flex items-center justify-center">
        <svg class="w-full h-full" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="8" fill="none" />
            <circle id="scroll-progress-circle" cx="50" cy="50" r="45" stroke="#D7AE27" stroke-width="8" fill="none"
                    stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283" />
        </svg>
        <span id="scroll-arrow" class="absolute text-[#D7AE27] text-2xl">&#10569;</span>
    </div>

    <!-- NAVBAR -->
    <nav class="fixed w-full top-0 z-50 bg-black/50 backdrop-blur-md border-b border-gray-800 text-white flex items-center">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between w-full">

            <!-- Logo -->
            <div class="flex items-center gap-2 text-xl font-bold text-[#D7AE27]">
                <img src="http://localhost/php_program/project/image/logo3.jpg" alt="CareerCraft Logo" class="h-10 w-10 object-contain">
                <span>CareerCraft</span>
            </div>

            <!-- Desktop Menu -->
          <div class="hidden lg:flex items-center gap-8 font-medium">

    <a href="http://localhost/php_program/project/home.php" class="hover:text-[#D7AE27] hover:underline">Home</a>

    <?php if(!isset($_SESSION['uid'])): ?>
        <!-- Guest: show all links -->
        <a href="http://localhost/php_program/project/seeker/find_job.php" class="hover:text-[#D7AE27] hover:underline">Find Jobs</a>
        <a href="http://localhost/php_program/project/company/find_talent.php" class="hover:text-[#D7AE27] hover:underline">Find Talent</a>
        <a href="http://localhost/php_program/project/company/post_job.php" class="hover:text-[#D7AE27] hover:underline">Post Job</a>
        <a href="http://localhost/php_program/project/seeker/job_history.php" class="hover:text-[#D7AE27] hover:underline">Job History</a>
    
    <?php elseif($_SESSION['role'] == 'seeker'): ?>
        <!-- Seeker -->
        <a href="http://localhost/php_program/project/seeker/find_job.php" class="hover:text-[#D7AE27] hover:underline">Find Jobs</a>
        <a href="http://localhost/php_program/project/seeker/job_history.php" class="hover:text-[#D7AE27] hover:underline">Job History</a>
    
    <?php elseif($_SESSION['role'] == 'company'): ?>
        <!-- Company -->
            <a href="http://localhost/php_program/project/company/find_talent.php" class="hover:text-[#D7AE27] hover:underline">Find Talent</a>
        <a href="http://localhost/php_program/project/company/post_job.php" class="hover:text-[#D7AE27] hover:underline">Post Job</a>
           <a href="http://localhost/php_program/project/company/candidate_history.php"
 class="hover:text-[#D7AE27] hover:underline">Candidate History</a>
    <?php endif; ?>

</div>
<div class="hidden lg:flex justify-end gap-3 items-center relative">

<?php if(isset($_SESSION['uid'])): ?>

    <!-- 🔔 Notification Bell -->
<a href="http://localhost/php_program/project/include/notifications.php" class="relative inline-block">

    <i class="fa-solid fa-bell text-xl text-white hover:text-[#D7AE27] transition"></i>

    <?php if($notification_count > 0): ?>
        <!-- RED DOT -->
<span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-black z-10"></span>    <?php endif; ?>

</a>


                <div class="relative group">
            <?php if(!empty($_SESSION['p_image'])): ?>

                <?php
                    if($_SESSION['role'] == 'company'){
                        $imagePath = "http://localhost/php_program/project/company/uploads/" . $_SESSION['p_image'];
                    } else {
                        $imagePath = "http://localhost/php_program/project/seeker/uploads/" . $_SESSION['p_image'];
                    }
                ?>

                <img src="<?= $imagePath ?>" 
                    class="w-10 h-10 rounded-full object-cover cursor-pointer border-2 border-[#D7AE27]"
                    alt="Profile">

            <?php else: ?>

                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['uname']) ?>&background=D7AE27&color=000"
                    class="w-10 h-10 rounded-full cursor-pointer border-2 border-[#D7AE27]"
                    alt="Profile">

            <?php endif; ?>

        <!-- Dropdown Menu -->
        <div class="absolute right-0 mt-2 w-48 bg-black border border-gray-700 rounded-md shadow-lg 
                    opacity-0 group-hover:opacity-100 invisible group-hover:visible 
                    transition-all duration-200 z-50">

            <?php if($_SESSION['role'] == 'company'): ?>
                <a href="http://localhost/php_program/project/company/cdashboard.php"
                   class="block px-4 py-2 text-white hover:bg-gray-800">Dashboard</a>
                <a href="http://localhost/php_program/project/company/cedit_profile.php"
                   class="block px-4 py-2 text-white hover:bg-gray-800">Edit Profile</a>
            <?php else: ?>
                <a href="http://localhost/php_program/project/seeker/sdashboard.php"
                   class="block px-4 py-2 text-white hover:bg-gray-800">Dashboard</a>
                <a href="http://localhost/php_program/project/seeker/sedit_profile.php"
                   class="block px-4 py-2 text-white hover:bg-gray-800">Edit Profile</a>
            <?php endif; ?>

    <a href="http://localhost/php_program/project/auth/logout.php"
               class="block px-4 py-2 text-red-400 hover:bg-gray-800">Logout</a>
        </div>
     </div>

        <?php else: ?>

        <!-- If Not Logged In -->
        <button class="px-4 py-2 border border-[#D7AE27] text-[#D7AE27] rounded-lg hover:bg-[#D7AE27] hover:text-black transition"
            onclick="location.href='http://localhost/php_program/project/auth/login.php'">
            Login
        </button>

        <button class="px-4 py-2 bg-[#D7AE27] text-black rounded-lg hover:bg-[#8b6c06] transition"
            onclick="location.href='http://localhost/php_program/project/auth/signup.php'">
            Sign Up
        </button>

        <?php endif; ?>
        </div>
        </div>
        <div class="flex items-center gap-4 lg:hidden">

            <?php if(isset($_SESSION['uid'])): ?>
        <!-- 🔔 Mobile Bell -->
        <a href="http://localhost/php_program/project/include/notifications.php" class="relative">

            <i class="fa-solid fa-bell text-xl text-white"></i>

            <?php if($notification_count > 0): ?>
                <!-- RED DOT -->
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border border-black"></span>
            <?php endif; ?>

        </a>
    <?php endif; ?>

            <!-- Hamburger (Mobile Only) -->
            <button id="menu-btn" class="lg:hidden text-2xl text-[#D7AE27]" aria-label="Open Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- MOBILE MENU -->
    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-4/5 bg-black/85 backdrop-blur-md transform -translate-x-full transition-transform duration-300 z-50 shadow-xl">
        <div class="flex flex-col h-full justify-between px-4 py-6">
            <div>
                <!-- Top: Logo + Close -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-2">
                        <img src="http://localhost/php_program/project/image/logo3.jpg" alt="Logo" class="h-10 w-10">
                        <span class="text-[#D7AE27] text-xl font-bold">CareerCraft</span>
                    </div>
                    <button id="close-menu" class="text-2xl text-gray-200" aria-label="Close Menu">&#10005;</button>
                </div>

                <!-- Search Bar -->
                <div class="relative mb-6">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search jobs, skills..."
                           class="w-full pl-10 pr-4 py-3 rounded-full border border-gray-700 bg-gray-900 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#D7AE27]">
                </div>

                <!-- Links -->
            <a href="http://localhost/php_program/project/home.php" 
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Home</a>

            <?php if(!isset($_SESSION['uid'])): ?>
            <!-- Guest: show all links -->
            <a href="http://localhost/php_program/project/seeker/find_job.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Jobs</a>

            <a href="http://localhost/php_program/project/company/find_talent.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Talent</a>

            <a href="http://localhost/php_program/project/company/post_job.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Post Job</a>

            <a href="http://localhost/php_program/project/company/candidate_history.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Candidate History</a>

            <?php elseif($_SESSION['role'] == 'seeker'): ?>
            <!-- Seeker -->
            <a href="http://localhost/php_program/project/seeker/find_job.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Jobs</a>

        

            <a href="http://localhost/php_program/project/seeker/job_history.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Job History</a>

        <?php elseif($_SESSION['role'] == 'company'): ?>
            <!-- Company -->
            <a href="http://localhost/php_program/project/company/find_talent.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Talent</a>

            <a href="http://localhost/php_program/project/company/post_job.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Post Job</a>

            <a href="http://localhost/php_program/project/company/job_history.php"
            class="block py-2 text-gray-200 hover:text-[#D7AE27]">Job History</a>
            <?php endif; ?>

                <!-- Mobile Login / Profile -->
               <!-- Bottom Section -->
        <div class="mt-6 border-t border-gray-700 pt-4">

        <?php if(isset($_SESSION['uid'])): ?>

            <!-- Profile Section -->
            <div class="flex items-center gap-3 mb-4">
                <?php if(!empty($_SESSION['p_image'])): ?>

                        <?php
                            if($_SESSION['role'] == 'company'){
                                $imagePath = "http://localhost/php_program/project/company/uploads/" . $_SESSION['p_image'];
                            } else {
                                $imagePath = "http://localhost/php_program/project/seeker/uploads/" . $_SESSION['p_image'];
                            }
                        ?>

                        <img src="<?= $imagePath ?>" 
                            class="w-10 h-10 rounded-full object-cover cursor-pointer border-2 border-[#D7AE27]"
                            alt="Profile">

                    <?php else: ?>

                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['uname']) ?>&background=D7AE27&color=000"
                            class="w-10 h-10 rounded-full cursor-pointer border-2 border-[#D7AE27]"
                            alt="Profile">

                    <?php endif; ?>

                <div>
                    <p class="text-white font-semibold"><?= $_SESSION['uname']; ?></p>
                    <p class="text-sm text-gray-400"><?= ucfirst($_SESSION['role']); ?></p>
                </div>
            </div>

            <!-- Dropdown Links (Now Visible in Mobile) -->
            <?php if($_SESSION['role'] == 'company'): ?>
                <a href="http://localhost/php_program/project/company/cdashboard.php"
                class="block py-2 text-gray-200 hover:text-[#D7AE27]">Dashboard</a>
                <a href="http://localhost/php_program/project/company/cedit_profile.php"
                class="block py-2 text-gray-200 hover:text-[#D7AE27]">Edit Profile</a>
            <?php else: ?>
                        <a href="http://localhost/php_program/project/seeker/sdashboard.php"
                class="block py-2 text-gray-200 hover:text-[#D7AE27]">Dashboard</a>
                        <a href="http://localhost/php_program/project/seeker/sedit_profile.php"
                class="block py-2 text-gray-200 hover:text-[#D7AE27]">Edit Profile</a>
            <?php endif; ?>

            <a href="http://localhost/php_program/project/auth/logout.php"
            class="block py-2 text-red-400 hover:text-red-500">Logout</a>

        <?php else: ?>

            <!-- Not Logged In -->
            <button class="w-full py-2 mb-2 border border-[#D7AE27] text-[#D7AE27] rounded-lg"
                onclick="location.href='http://localhost/php_program/project/auth/login.php'">
                Login
            </button>

            <button class="w-full py-2 bg-[#D7AE27] text-black rounded-lg"
                onclick="location.href='http://localhost/php_program/project/auth/signup.php'">
                Sign Up
            </button>

        <?php endif; ?>

        </div>
    </div>
        </div>
    </div>

    <script>
        //=================progress=================
        const progressContainer = document.getElementById('scroll-progress-container');
        const progressCircle = document.getElementById('scroll-progress-circle');
        const arrow = document.getElementById('scroll-arrow');
        const circumference = 2 * Math.PI * 45;
        let scrollTimeout;

        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = scrollTop / scrollHeight;
            progressCircle.style.strokeDashoffset = circumference * (1 - scrollPercent);
            progressContainer.style.opacity = 1;
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                progressContainer.style.opacity = 0;
            }, 1000);
        });

        progressContainer.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Mobile Menu
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenu = document.getElementById('close-menu');
        menuBtn.addEventListener('click', () => mobileMenu.classList.remove('-translate-x-full'));
        closeMenu.addEventListener('click', () => mobileMenu.classList.add('-translate-x-full'));
    </script>
</body>
</html>