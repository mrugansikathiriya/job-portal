<?php
session_start(); // <--- top of the file

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerCraft | Job Portal</title>
    <link href="dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="image/logo3.jpg" type="image/png">
    <style>
        /* Loader */
        @keyframes fadeZoom {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(1.05);
                visibility: hidden;
            }
        }

        .animate-fadeZoom {
            animation: fadeZoom 2s ease forwards;
        }




        /* Pulsing glow effect for the button */
        @keyframes glowPulse {
            0% {
                box-shadow: 0 0 6px 0 rgba(215, 174, 23, 0.6);
            }

            50% {
                box-shadow: 0 0 12px 6px rgba(215, 174, 23, 0.8);
            }

            100% {
                box-shadow: 0 0 6px 0 rgba(215, 174, 23, 0.6);
            }
        }

        .animate-glowPulse {
            animation: glowPulse 2s infinite;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease forwards;
        }

        .animate-fadeIn.delay-100 {
            animation-delay: 0.1s;
        }

        .animate-fadeIn.delay-200 {
            animation-delay: 0.2s;
        }

        .animate-fadeIn.delay-300 {
            animation-delay: 0.3s;
        }

        /* Fade-in on scroll */
        .fade-in-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s ease;
        }

        .fade-in-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Subtle pulse animation for button glow */
        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 10px rgba(215, 174, 39, 0.4), 0 0 20px rgba(215, 174, 39, 0.2);
            }

            50% {
                box-shadow: 0 0 20px rgba(215, 174, 39, 0.6), 0 0 40px rgba(215, 174, 39, 0.3);
            }
        }

        .pulse-button {
            animation: pulse 2.5s infinite;
        }

        /* Slow pulse for accent circle on image */
        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.4;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.6;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s infinite;
        }

        /* Smooth hover transitions for cards and images */
        .value-card,
        .group img {
            transition: all 0.7s ease;
        }

        /* Delay classes */
        .fade-in-on-scroll.delay-100 {
            transition-delay: 0.1s;
        }

        .fade-in-on-scroll.delay-200 {
            transition-delay: 0.2s;
        }

        .fade-in-on-scroll.delay-300 {
            transition-delay: 0.3s;
        }

        /* Pop / Pulse animation */
        @keyframes popPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.12);
            }
        }

        /* Company recruiter */
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }

            /* Move half of the slider width */
        }

        #logo-slider {
            display: flex;
            gap: 3rem;
            /* Adjust spacing */
            animation: scroll 30s linear infinite;
        }

        #logo-slider:hover {
            animation-play-state: paused;
            /* Pause on hover */
        }
    </style>
</head>


<?php

include("include/navbar.php");?>

<body class="bg-black text-gray-200 font-sans">

    <!-- Loader -->
    <div id="loader" class="fixed inset-0 bg-black flex flex-col justify-center items-center z-50 animate-fadeZoom">
        <div class="text-[#D7AE27] text-lg tracking-widest mb-4 font-mono">LOADING</div>
        <div class="w-40 h-1 bg-gray-700 relative overflow-hidden rounded">
            <div class="absolute h-full w-full animate-pulse bg-[#D7AE27]"></div>
        </div>
    </div>
 
   

    <!-- Image cursoel -->
     <div id="carousel" class="relative h-[460px] overflow-hidden rounded-2xl shadow-2xl mt-10">

    <!-- SLIDES -->
    <div class="absolute inset-0 slide opacity-100 transition-opacity duration-1000"
         data-img="image/img2.jpg"
         data-title="Build Your <span class='text-[#D7AE27]'>Career</span> With Confidence">
    </div>

    <div class="absolute inset-0 slide opacity-0 transition-opacity duration-1000"
         data-img="image/img11.jpg"
         data-title="Your Dream Job <span class='text-[#D7AE27]'>Starts Here</span>">
    </div>

    <div class="absolute inset-0 slide opacity-0 transition-opacity duration-1000"
         data-img="image/img3.jpg"
         data-title="Find Work That <span class='text-[#D7AE27]'>Matters</span>">
    </div>

    <!-- IMAGE -->
    <img id="carouselImage" src="image/img2.jpg"
         class="w-full h-full object-cover">

    <!-- OVERLAY (ONLY ONE TIME) -->
    <div class="absolute inset-0 pointer-events-none
        bg-[radial-gradient(ellipse_85%_65%_at_center,rgba(2,6,23,0.92)_0%,rgba(2,6,23,0.75)_40%,rgba(2,6,23,0.45)_70%,transparent_100%)]
        flex flex-col justify-center px-6 md:px-12">

        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h1 id="carouselTitle" class="text-3xl md:text-4xl font-bold text-white">
                Build Your <span class="text-[#D7AE27]">Career</span> With Confidence
            </h1>
            <p class="text-gray-300">Find jobs that match your skills</p>
        </div>

             <!-- Search Bar -->
                        <div class="hidden md:flex relative max-w-3xl mx-auto mt-6 gap-2 pointer-events-auto z-20">
                            <i
                                class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" class="job-search w-full pl-12 pr-4 py-3 rounded-full border border-white placeholder-gray-300
              text-white font-sans focus:border-[#D7AE27] focus:ring-2 focus:ring-[#D7AE27] outline-none transition"
                                placeholder="Search jobs, skills...">
                            <button
                                class="search-btn w-36 bg-[#D7AE27] text-black rounded-full hover:bg-amber-500 hover:text-white transition-all duration-300 transform">
                                Search
                            </button>
                        </div>

                        <!-- Mobile Buttons -->
                        <div class="md:hidden px-4 py-6 flex flex-col items-center space-y-4 pointer-events-auto z-20">

                    

                            <!-- Get Started -->
                            <div class="flex justify-center">
                                <button onclick="location.href='#hire'" class="w-64 bg-[#FBBF24] text-black py-3 rounded-lg font-semibold
                transform transition-all duration-300 hover:bg-[#D7AE27] hover:text-white hover:scale-105 hover:shadow-xl
                active:scale-95 active:shadow-inner">Get Started</button>
                            </div>

                        </div>
    </div>

    <!-- CONTROLS -->
    <button id="prev"
        class="absolute left-4 top-1/2 -translate-y-1/2 text-3xl text-white/70 hover:text-white transition">&#10096;</button>

    <button id="next"
        class="absolute right-4 top-1/2 -translate-y-1/2 text-3xl text-white/70 hover:text-white transition">&#10097;</button>

</div>
 

    <!-- Trusted Companies -->
    <section class="bg-black py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">

            <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27] mb-6">
                Trusted By
                <span class="relative inline-block text-white">
                    Top Companies
                    <span class="absolute left-1/2 -translate-x-1/2 -bottom-3 w-56 h-1 bg-[#D7AE27] rounded-sm"></span>
                </span>
            </h2>
            <p class="text-gray-400 mb-20">Leading companies hire talent through CareerCraft</p>

            <!-- Infinite Scroll Container -->
            <div class="relative overflow-hidden">
                <div id="logo-slider" class="flex gap-12 items-center">
                    <!-- Logos injected by JS -->
                </div>
            </div>

        </div>
    </section>
    <!-- jobs -->
    <?php
        require "config/db.php";

        date_default_timezone_set('Asia/Kolkata');

        // Fetch ONLY 3 latest jobs
        $sql = "SELECT job.*, company.cname, company.logo,
                TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
                FROM job
                JOIN company ON job.cid = company.cid
                WHERE job.deadline >= NOW()
                ORDER BY job.posted_at DESC
                LIMIT 3";

        $result = mysqli_query($conn, $sql);
        ?>

    <div class="max-w-7xl mx-auto px-6 mt-16">

        <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27] mb-16 text-center">
            Recommended 
            <span class="relative inline-block text-white">
                Jobs
                <span class="absolute left-0 top-full mt-14 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
            </span>
        </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">

        <?php while($row = mysqli_fetch_assoc($result)) { 

            // Time logic
            $seconds = $row['seconds_old'];

            if ($seconds < 60) {
                $posted = "Just now";
            } elseif ($seconds < 3600) {
                $posted = floor($seconds / 60) . " minutes ago";
            } elseif ($seconds < 86400) {
                $posted = floor($seconds / 3600) . " hours ago";
            } elseif ($seconds < 172800) {
                $posted = "1 day ago";
            } else {
                $posted = floor($seconds / 86400) . " days ago";
            }

            $logo = !empty($row['logo']) 
                ? "company/uploads/".$row['logo'] 
                : "https://via.placeholder.com/70";
        ?>

            <!-- Job Card -->
            <div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300">

                <!-- Company Info -->
                <div class="flex items-center gap-4 mb-5">
                    <img src="<?php echo $logo; ?>" 
                    class="w-14 h-14 rounded-xl object-cover bg-white p-1">

                    <div>
                        <h3 class="text-lg font-semibold">
                            <?php echo $row['title']; ?>
                        </h3>

                        <p class="text-gray-400 text-sm">
                            <?php echo $row['cname']; ?>
                        </p>
                    </div>
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 text-xs mb-5">
                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                        <?php echo $row['experience_required']; ?>
                    </span>

                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                        <?php echo $row['job_type']; ?>
                    </span>

                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                        <?php echo $row['location']; ?>
                    </span>
                </div>

                <!-- Salary + Posted -->
                <div class="flex justify-between items-center text-sm mb-3">
                    <div class="font-semibold text-white text-base">
                        ₹ <?php echo $row['salary']; ?> LPA
                    </div>

                    <div class="text-gray-400">
                                        <i class="fa-regular fa-clock text-yellow-400"></i>

                        <?php echo $posted; ?>
                    </div>
                </div>

                <!-- View Details -->
                <a href="http://localhost/php_program/project/seeker/job_details.php?jid=<?php echo $row['jid']; ?>" 
                class="block text-center bg-yellow-400 text-black py-2 rounded-xl font-semibold hover:bg-yellow-500 transition">
                    View Details
                </a>

            </div>

        <?php } ?>

            </div>

            <!-- View More Button -->
        <div class="text-center mt-10">
            <a href="http://localhost/php_program/project/seeker/find_job.php"
            class="inline-block bg-yellow-400 text-black px-6 py-3 rounded-xl font-semibold 
            hover:bg-yellow-500 transition animate-pulse">
                View More Jobs →
            </a>
        </div>

    </div>
    <!-- hire process -->
    <section id="hire" class="bg-black py-20 px-6 md:px-20">
        <div class="max-w-7xl mx-auto">

            <!-- Heading -->
            <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27] mb-20 ml-10 text-center lg:ml-10">
                Hiring Process in
                <span class="relative inline-block text-white">
                    3 simple steps
                    <span
                        class="absolute left-1/2 transform -translate-x-1/2 bottom-0 w-60 h-1 mb-[-15px]  bg-[#D7AE27] rounded-sm"></span>
                </span>
            </h2>

            <!-- Steps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-14">

                <!-- Step 1 -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">

                    <span
                        class="absolute -top-6 left-6 w-12 h-12 bg-indigo-600 text-white flex items-center justify-center rounded-full font-bold text-lg shadow-lg">
                        1
                    </span>

                    <img src="image/p3.png" class="w-32 mx-auto mb-6" alt="Create Job">

                    <h3 class="text-xl font-semibold text-white mb-2">
                        Create Job Opening
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        HR creates a new job opening with role details, skills required,
                        and department information.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">

                    <span
                        class="absolute -top-6 left-6 w-12 h-12 bg-indigo-600 text-white flex items-center justify-center rounded-full font-bold text-lg shadow-lg">
                        2
                    </span>

                    <img src="image/p2.png" class="w-32 mx-auto mb-6" alt="HR Approval">

                    <h3 class="text-xl font-semibold text-white mb-2">
                        HR Review & Approval
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Job request is reviewed and approved by the HR team to ensure
                        compliance with company policies.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">

                    <span
                        class="absolute -top-6 left-6 w-12 h-12 bg-indigo-600 text-white flex items-center justify-center rounded-full font-bold text-lg shadow-lg">
                        3
                    </span>

                    <img src="image/p1.png" class="w-32 mx-auto mb-6" alt="Interview">

                    <h3 class="text-xl font-semibold text-white mb-2">
                        Interview & Onboard
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Shortlisted candidates are interviewed, selected,
                        and smoothly onboarded into the organization.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- about us -->
    <section class="bg-black text-gray-300 py-16 px-6 md:px-20" id="about">
        <div class="max-w-6xl mx-auto">

            <!-- Heading -->
            <div class="text-center mb-8">
                <h2 id="about" class="text-4xl md:text-4xl font-bold text-center text-white mb-10 relative">
                    <span class="text-[#D7AE27] relative z-10">About</span> Us
                    <!-- Decorative underline -->
                    <span
                        class="absolute left-1/2 transform -translate-x-1/2 bottom-0 w-32 h-1 mb-[-15px] bg-[#D7AE27] rounded-sm"></span>
                </h2>
                <p class="text-gray-400 max-w-2xl mx-auto">
                    Building careers and connecting talent with the right opportunities
                </p>
            </div>
            <!-- Features with Counters -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-10 text-center">

                <!-- CARD 1 -->
                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10">

                    <!-- Glow -->
                    <div
                        class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#D7AE27]/10 to-transparent opacity-0 ">
                    </div>

                    <div class="relative flex flex-col items-center">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="80" cy="80" r="70" stroke="#1f2937" stroke-width="10" fill="none" />
                                <circle cx="80" cy="80" r="70" stroke="#D7AE27" stroke-width="10" fill="none"
                                    stroke-linecap="round" stroke-dasharray="440" stroke-dashoffset="440"
                                    class="progress-ring" data-max="100" />
                            </svg>

                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="counter text-4xl font-extrabold text-[#D7AE27]" data-target="30">0</span>
                            </div>
                        </div>

                        <p class="mt-6 text-xl font-semibold text-white tracking-wide">
                            Jobs Posted
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Active internal openings
                        </p>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10">

                    <div
                        class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#D7AE27]/10 to-transparent opacity-0">
                    </div>

                    <div class="relative flex flex-col items-center">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="80" cy="80" r="70" stroke="#1f2937" stroke-width="10" fill="none" />
                                <circle cx="80" cy="80" r="70" stroke="#D7AE27" stroke-width="10" fill="none"
                                    stroke-linecap="round" stroke-dasharray="440" stroke-dashoffset="440"
                                    class="progress-ring" data-max="100" />
                            </svg>

                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="counter text-4xl font-extrabold text-[#D7AE27]" data-target="50">0</span>
                            </div>
                        </div>

                        <p class="mt-6 text-xl font-semibold text-white tracking-wide">
                            Candidates Hired
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Successful placements
                        </p>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10">

                    <div
                        class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#D7AE27]/10 to-transparent opacity-0">
                    </div>

                    <div class="relative flex flex-col items-center">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="80" cy="80" r="70" stroke="#1f2937" stroke-width="10" fill="none" />
                                <circle cx="80" cy="80" r="70" stroke="#D7AE27" stroke-width="10" fill="none"
                                    stroke-linecap="round" stroke-dasharray="440" stroke-dashoffset="440"
                                    class="progress-ring" data-max="100" />
                            </svg>

                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="counter text-4xl font-extrabold text-[#D7AE27]" data-target="12">0</span>
                            </div>
                        </div>

                        <p class="mt-6 text-xl font-semibold text-white tracking-wide">
                            Years of Experience
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Trusted recruitment expertise
                        </p>
                    </div>
                </div>

            </div>
            <!-- HERO SECTION -->
            <section id="about" class="relative bg-black py-15 md:py-20 overflow-hidden">

                <div class="max-w-7xl mx-auto px-6 md:px-6">
                    <div class="grid md:grid-cols-2 gap-3 items-center">

                        <!-- LEFT: Heading + Animated CTA -->
                        <div>
                            <span class="text-xs uppercase tracking-[0.35em] text-[#D7AE27] font-semibold">
                                About us
                            </span>

                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mt-6 mb-8 leading-tight">
                                Where Talent <br class="hidden sm:block">
                                Meets Opportunity
                            </h2>

                            <!-- Gold divider -->
                            <div class="w-20 h-[2px] bg-[#D7AE27] mb-8"></div>

                            <!-- SMALLER EXPLORE JOBS BUTTON -->
                            <a href="http://localhost/php_program/project/seeker/find_job.php" class="relative inline-flex items-center justify-center px-6 py-3 rounded-lg
                  bg-gradient-to-r from-[#D7AE27] to-[#FFD700] text-black font-semibold text-base
                  tracking-wide shadow-[0_2px_10px_rgba(215,174,23,0.4)]
                  hover:scale-105 hover:shadow-[0_4px_20px_rgba(215,174,23,0.6)]
                  transition-all duration-300 group animate-glowPulse overflow-hidden">

                                <!-- Gradient glow overlay -->
                                <span
                                    class="absolute inset-0 bg-gradient-to-r from-[#FFD700] to-[#D7AE27] opacity-20 blur-xl animate-gradientMove"></span>

                                <!-- Button Text -->
                                Explore Jobs >>



                            </a>
                        </div>

                        <!-- RIGHT: VIDEO -->
                        <div
                            class="relative h-[280px] sm:h-[360px] md:h-[420px] rounded-xl overflow-visible md:overflow-hidden -mr-10">

                            <video
                                class="absolute inset-0 left-1/2 w-[170%] h-full -translate-x-1/2 object-cover opacity-85"
                                autoplay muted loop playsinline poster="image/img6.webp">
                                <source src="image/video1.mp4" type="video/mp4">
                            </video>



                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/20 to-transparent z-10">
                            </div>

                        </div>
                    </div>
                </div>

            </section>

            <section class="bg-black w-full overflow-hidden py-20">
                <div class="max-w-full mx-auto grid md:grid-cols-3 gap-10">

                    <!-- LEFT PANEL -->
                    <div
                        class="bg-gradient-to-b from-[#0a0a0a] to-[#0a0a0a] text-white p-12 flex flex-col justify-center border-r border-gray-800  shadow-lg fade-in-on-scroll">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-6 leading-snug text-[#D7AE27]">
                            Our Mission &<br>Vision Statement
                        </h2>
                        <div class="w-20 h-[3px] bg-[#D7AE27] mb-6 rounded"></div>
                        <p class="text-gray-400 text-sm md:text-base leading-relaxed max-w-md">
                            CareerCraft streamlines company hiring with clarity, transparency, and efficient
                            recruitment workflows, ensuring a seamless experience for both HR teams and candidates.
                        </p>

                        <!-- READ MORE BUTTON -->
                        <div class="mt-10">
                            <a href="about.php" target="_blank"
                                class="relative inline-block px-8 py-3 font-semibold text-[#0a0a0a] bg-[#D7AE27] rounded-lg overflow-hidden group transition-all duration-500 hover:text-white pulse-button">
                                <span
                                    class="absolute inset-0 bg-gradient-to-r from-[#FFD966] via-[#D7AE27] to-[#FFD966] opacity-0 group-hover:opacity-100 transition-opacity duration-500 blur-lg rounded-lg"></span>
                                <span class="relative z-10">Read More</span>
                                <!-- Glowing border effect -->
                                <span
                                    class="absolute -inset-0.5 bg-gradient-to-r from-[#FFD966] via-[#D7AE27] to-[#FFD966] rounded-lg blur opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                            </a>
                        </div>
                    </div>

                    <!-- RIGHT CONTENT -->
                    <div class="md:col-span-2 p-12 space-y-10">

                        <!-- CARDS -->
                        <div class="grid sm:grid-cols-2 gap-8">

                            <!-- Vision Card -->
                            <div
                                class="value-card bg-gradient-to-br from-[#111111] to-[#1a1a1a] p-8 border border-gray-800 shadow-md hover:shadow-xl transform transition-transform duration-500 hover:-translate-y-2 fade-in-on-scroll delay-100">
                                <div class="icon-badge text-3xl mb-4 ">👁</div>
                                <h3 class="card-title text-xl font-semibold text-[#D7AE27] mb-3">Vision</h3>
                                <p class="text-gray-300 text-sm md:text-base leading-relaxed ">
                                    To become a trusted recruitment platform that empowers organizations with
                                    scalable and transparent hiring solutions.
                                </p>
                            </div>

                            <!-- Mission Card -->
                            <div
                                class="value-card bg-gradient-to-br from-[#111111] to-[#1a1a1a] p-8 border border-gray-800 shadow-md hover:shadow-xl transform transition-transform duration-500 hover:-translate-y-2 fade-in-on-scroll delay-200">
                                <div class="icon-badge text-3xl mb-4">🎯</div>
                                <h3 class="card-title text-xl font-semibold text-[#D7AE27] mb-3">Mission</h3>
                                <p class="text-gray-300 text-sm md:text-base leading-relaxed">
                                    To simplify hiring through seamless candidate journeys, structured job
                                    management,
                                    and data-driven recruitment.
                                </p>
                            </div>

                        </div>

                        <!-- IMAGE -->
                        <div
                            class="relative group rounded-xl overflow-hidden border border-gray-800 shadow-lg fade-in-on-scroll delay-300">
                            <img src="image/img6.webp"
                                class="w-full h-[360px] object-cover transition-transform duration-700 group-hover:scale-105"
                                alt="CareerCraft Team">
                            <!-- Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-black/30 via-black/10 to-transparent transition-opacity duration-500 group-hover:opacity-0">
                            </div>
                            <!-- Accent Circle -->
                            <div
                                class="absolute right-6 top-6 w-12 h-12 bg-[#D7AE27]/20 rounded-full animate-pulse-slow">
                            </div>
                        </div>

                    </div>
                </div>
            </section>



    </section>

      <section id="feedback_section">
            
            <?php
                require "config/db.php";

        $sql = "SELECT feedback.*, users.p_image, users.role
        FROM feedback
        JOIN users ON feedback.uid = users.uid
        ORDER BY feedback.fid DESC";

            $result = $conn->query($sql);
            ?>

            <!-- FEEDBACK SECTION -->

            <div class="max-w-6xl mx-auto mt-6 px-6 mb-16">

            <h2 class="text-3xl font-bold text-yellow-400 mb-8 text-center">
            User Feedback
            </h2>

            <div class="grid md:grid-cols-3 gap-6">

            <?php while($row=$result->fetch_assoc()) { ?>

            <div class="bg-gradient-to-b from-[#0a0a0a] to-[#0a0a0a]  p-6 rounded-xl shadow-lg hover:scale-105 transition">

        <?php
            if(!empty($row['p_image'])){

                if($row['role'] == 'company'){
                    $imagePath = "http://localhost/php_program/project/company/uploads/" . $row['p_image'];
                }else{
                    $imagePath = "http://localhost/php_program/project/seeker/uploads/" . $row['p_image'];
                }
            ?>
    
        <img src="<?= $imagePath ?>" 
        class="w-12 h-12 rounded-full mb-3 object-cover border-2 border-[#D7AE27]">

        <?php } else { ?>

        <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=D7AE27&color=000"
        class="w-12 h-12 rounded-full mb-3 border-2 border-[#D7AE27]">

        <?php } ?>

            <h3 class="text-lg font-semibold">
            <?php echo $row['name']; ?>
            </h3>


            <p class="text-yellow-400 mb-2">
            <?php echo str_repeat("⭐️",$row['rating']); ?>
            </p>

            <p class="text-gray-400 text-sm">
            <?php echo $row['message']; ?>
            </p>

            </div>

            <?php } ?>

            </div>
            </div>
            <div class="flex justify-center mt-6 mb-6">
            <a href="http://localhost/php_program/project/include/feedback.php"
            class="bg-yellow-400 text-black px-6 py-2 rounded hover:bg-yellow-500">
            Give Feedback
            </a>
            </div>
    </section>
              
  

<?php
 include("include/footer.php");
?>


    <!-- SCRIPT -->
    <script>
        // ================= LOADER (UNCHANGED) =================
        window.addEventListener("load", () => {
            const loader = document.getElementById("loader");
            const mainContent = document.getElementById("main-content");
            loader.classList.add("animate-fadeZoom");
            setTimeout(() => {
                mainContent.classList.add("opacity-100");
                loader.style.display = "none";
            }, 2000);
        });

      


const slides = document.querySelectorAll(".slide");
const img = document.getElementById("carouselImage");
const title = document.getElementById("carouselTitle");

let current = 0;

function showSlide(index) {
    const slide = slides[index];

    img.src = slide.dataset.img;
    title.innerHTML = slide.dataset.title;

    slides.forEach(s => s.style.opacity = "0");
    slide.style.opacity = "1";
}

document.getElementById("next").onclick = () => {
    current = (current + 1) % slides.length;
    showSlide(current);
};

document.getElementById("prev").onclick = () => {
    current = (current - 1 + slides.length) % slides.length;
    showSlide(current);
};

// Auto slide
setInterval(() => {
    current = (current + 1) % slides.length;
    showSlide(current);
}, 5000);
        //  company logo
        // List of company logos (can be PNG or JPG)
        const logos = [
            "logo/google.jpg",
            "logo/amazon.png",
            "logo/microsoft.png",
            "logo/meta.png",
            "logo/netflix.png",
            "logo/apple.png",
            "logo/figma.jpg",
            "logo/ibm.png",
            "logo/infosys.png",
            "logo/meesho.jpg",
            "logo/spotify.jpg",
            "logo/walmart.jpg",
            "logo/youtube.png"
        ];

        const slider = document.getElementById("logo-slider");

        // Add logos twice to allow smooth infinite scroll
        logos.concat(logos).forEach(logo => {
            const img = document.createElement("img");
            img.src = logo;
            img.alt = "Company Logo";
            img.className = "h-12 md:h-16 lg:h-20";
            slider.appendChild(img);
        });


        // Simple slide-up animation when blocks enter viewport
        document.addEventListener("DOMContentLoaded", () => {

            /* ================= COUNTER + RING ANIMATION ================= */
            const counters = document.querySelectorAll(".counter");
            const rings = document.querySelectorAll(".progress-ring");
            const circumference = 440;

            const animateCounter = (counter, ring) => {
                const target = +counter.dataset.target;
                const max = +ring.dataset.max;
                const percentage = Math.min(target / max, 1);
                let current = 0;
                const duration = 2000;
                const increment = target / (duration / 16);

                const update = () => {
                    current += increment;
                    const progress = Math.min(current / target, 1);
                    counter.innerText = Math.ceil(current).toLocaleString();

                    ring.style.strokeDashoffset =
                        circumference - (percentage * progress * circumference);

                    if (current < target) {
                        requestAnimationFrame(update);
                    } else {
                        counter.innerText = target.toLocaleString() + "+";
                        ring.style.strokeDashoffset =
                            circumference - (percentage * circumference);
                    }
                };

                update();
            };

            const counterObserver = new IntersectionObserver(entries => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        animateCounter(counters[i], rings[i]);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.6 });

            counters.forEach(counter => counterObserver.observe(counter));


            /* ================= VIDEO AUTOPLAY ON SCROLL ================= */
            const videos = document.querySelectorAll(".reveal-video video");

            const videoObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    entry.isIntersecting ? entry.target.play() : entry.target.pause();
                });
            }, { threshold: 0.4 });

            videos.forEach(video => videoObserver.observe(video));

        });
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll(".animate-item").forEach(el => observer.observe(el));

        // Fade-in on scroll
        const faders = document.querySelectorAll('.fade-in-on-scroll');

        const appearOnScroll = new IntersectionObserver(function (entries, observer) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // FIXED LINE
                }
            });
        }, { threshold: 0.2 });

        faders.forEach(fader => {
            appearOnScroll.observe(fader);
        });



    </script>

</body>

</html>
