<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>CareerCraft | Job Portal</title>
    <link href="../dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="image/logo3.jpg" type="image/png"></head>
<style>
    
        /* progress */
        #scroll-progress-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dashoffset 0.2s linear;
        }
</style>    
<body>
       <!-- Scroll Progress Circle with Arrow -->
    <div id="scroll-progress-container"
        class="fixed right-6 bottom-8 w-14 h-14 opacity-0 transition-opacity duration-300 z-50 cursor-pointer flex items-center justify-center">
        <svg class="w-full h-full" viewBox="0 0 100 100">
            <!-- Background Circle -->
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="8" fill="none" />
            <!-- Progress Circle -->
            <circle id="scroll-progress-circle" cx="50" cy="50" r="45" stroke="#D7AE27" stroke-width="8" fill="none"
                stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283" />
        </svg>
        <!-- Arrow Icon (up) -->
        <span id="scroll-arrow" class="absolute text-[#D7AE27] text-2xl">&#10569;</span>
    </div>
        <!-- NAVBAR -->
<nav class="fixed w-full top-0 z-50 bg-black/50 backdrop-blur-md border-b border-gray-800 text-white">        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center gap-2 text-xl font-bold text-[#D7AE27]">
                <img src="http://localhost/php_program/project/image/logo3.jpg" alt="CareerCraft Logo" class="h-10 w-10 object-contain">
                <span>CareerCraft</span>
            </div>

            <!-- Desktop Menu -->
            <div class="flex justify-center">
                <div class="gap-8 font-medium hidden lg:flex">

                    <!-- Nav Links -->
                    <a href="http://localhost/php_program/project/home.php"
                        class="flex items-center h-10 px-3 text-white hover:text-[#D7AE27] hover:underline hover:underline-offset-4">
                        Home</a>
                    <a href="#"
                        class="flex items-center h-10 px-3 text-white hover:text-[#D7AE27] hover:underline hover:underline-offset-4">
                        Find Jobs</a>
                    <a href="#"
                        class="flex items-center h-10 px-3 text-white hover:text-[#D7AE27] hover:underline hover:underline-offset-4">
                        Find Talent</a>
                    <a href="#"
                        class="flex items-center h-10 px-3 text-white hover:text-[#D7AE27] hover:underline hover:underline-offset-4">
                        Post Job</a>
                    <a href="#"
                        class="flex items-center h-10 px-3 text-white hover:text-[#D7AE27] hover:underline hover:underline-offset-4">
                        Job History</a>
                </div>
            </div>
            <!-- Buttons -->
            <div class="hidden lg:flex justify-end gap-3">
                <button
                    class="px-4 py-2 border border-[#D7AE27] text-[#D7AE27] rounded-lg hover:bg-[#D7AE27] hover:text-black transition"  onclick="location.href='http://localhost/php_program/project/auth/login.php'">
                    Login
                </button>

                <button class="px-4 py-2 bg-[#D7AE27] text-black rounded-lg hover:bg-[#8b6c06] transition"
                    onclick="location.href='http://localhost/php_program/project/auth/signup.php'">
                    Sign Up
                </button>
            </div>


            <!-- Hamburger (Mobile Only) -->
            <button id="menu-btn" class="lg:hidden text-2xl text-[#D7AE27]" aria-label="Open Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- MOBILE MENU -->
    <div id="mobile-menu"
        class="fixed top-0 left-0 h-full w-4/5 bg-black/85 backdrop-blur-md transform -translate-x-full transition-transform duration-300 z-50 shadow-xl">

        <div class="flex flex-col h-full justify-between px-4 py-6">
            <!-- Top: Logo + Close -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-2">
                        <img src="http://localhost/php_program/project/image/logo3.jpg" alt="Logo" class="h-10 w-10">
                        <span class="text-[#D7AE27] text-xl font-bold">CareerCraft</span>
                    </div>
                    <button id="close-menu" class="text-2xl text-gray-200" aria-label="Close Menu">&#10005;</button>
                </div>

                <!-- Search Bar -->
                <div class="relative mb-6">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search jobs, skills..."
                        class="w-full pl-10 pr-4 py-3 rounded-full border border-gray-700 bg-gray-900 text-gray-200 placeholder-gray-400
          focus:outline-none focus:ring-2 focus:ring-[#D7AE27] focus:border-[#D7AE27] shadow-md transition duration-300 hover:shadow-lg hover:border-[#D7AE27]">
                </div>

                <!-- Links -->
                <a href="http://localhost/php_program/project/home.php" class="block py-2 text-gray-200 hover:text-[#D7AE27]">Home</a>
                <a href="#" class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Jobs</a>
                <a href="#" class="block py-2 text-gray-200 hover:text-[#D7AE27]">Find Talent</a>
                <a href="#" class="block py-2 text-gray-200 hover:text-[#D7AE27]">Post Job</a>
                <a href="#" class="block py-2 text-gray-200 hover:text-[#D7AE27]">Job History</a>
            </div>

        </div>
    </div>
</body>
<script>
      //=================progress=================
        const progressContainer = document.getElementById('scroll-progress-container');
        const progressCircle = document.getElementById('scroll-progress-circle');
        const arrow = document.getElementById('scroll-arrow');
        const circumference = 2 * Math.PI * 45; // r=45
        let scrollTimeout;

        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = scrollTop / scrollHeight;

            // Update circle progress
            const dashOffset = circumference * (1 - scrollPercent);
            progressCircle.style.strokeDashoffset = dashOffset;

            // Show while scrolling
            progressContainer.style.opacity = 1;

            // Clear previous timeout
            clearTimeout(scrollTimeout);

            // Hide after 1s of no scrolling
            scrollTimeout = setTimeout(() => {
                progressContainer.style.opacity = 0;
            }, 1000);
        });

        // Click scroll to top
        progressContainer.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // ================= MOBILE MENU =================
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenu = document.getElementById('close-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('-translate-x-full');
        });

        closeMenu.addEventListener('click', () => {
            mobileMenu.classList.add('-translate-x-full');
        });
    </script>
</html>