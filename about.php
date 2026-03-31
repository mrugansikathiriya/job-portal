<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About us | Job Portal</title>
    <link href="dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="image/logo3.jpg" type="image/png">
    <style>
        /* Process cards */
        .process-card {
            background: #0b1020;
            border: 1px solid #444;
            padding: 1.5rem 1rem;
            border-radius: 14px;
            font-weight: 600;
            color: #D7AE27;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.5s ease, box-shadow 0.5s ease, background 0.3s ease;
        }

        .process-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.8);
            background: #1f1f2f;
        }

        #scroll-progress-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dashoffset 0.2s linear;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInDown {
            animation: fadeInDown 1s ease forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 1s ease forwards;
        }

        .animate-fadeInRight {
            animation: fadeInRight 1s ease forwards;
        }

        .animate-fadeInUp {
            animation: fadeInUp 1s ease forwards;
        }

        .animate-fadeIn {
            animation: fadeInDown 1s ease forwards;
        }

        .delay-150 {
            animation-delay: 0.15s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* heading image */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease forwards;
        }
    </style>
</head>

<body class="bg-black text-gray-300 font-sans">

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
    <?php
    include("include/navbar.php");
    ?>

    <!-- About Us Section -->
    <section id="about" class="bg-black text-gray-300">

        <!-- Hero / Banner -->
        <div class="max-w-7xl mx-auto py-20 px-4 md:px-6">
            <div class="relative h-[460px] md:h-[60vh] overflow-hidden rounded-2xl shadow-2xl">

                <!-- Background Image -->
                <img src="image\about.jpg" alt="About Us Background" class="w-full h-full object-cover brightness-30">

                <!-- Overlay Content -->
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-6">
                    <h2 class="text-4xl md:text-5xl font-bold text-white relative">
                        About <span class="text-[#D7AE27]">Us</span>
                        <span class="absolute left-1/2 -translate-x-1/2 -bottom-4 w-28 h-1 bg-[#D7AE27] rounded"></span>
                    </h2>

                    <p class="text-gray-400 max-w-2xl mt-8 text-sm md:text-base animate-fadeIn">
                        At <span class="text-[#D7AE27] font-semibold">CareerCraft</span>, we believe in creating
                        opportunities for talented professionals to grow and thrive. Our mission is to connect
                        skilled individuals with meaningful careers, fostering innovation and excellence.
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <section class="bg-black flex items-center justify-center px-6 py-8">
            <div class="max-w-3xl text-center text-gray-300 space-y-6">

                <p>
                    Established in 2024,
                    <span class="text-yellow-400 font-semibold">CareerCraft</span> is committed to delivering the best
                    work
                    environment for our employees. Our team thrives on innovation, collaboration, and growth.
                </p>

                <p>
                    We prioritize career development, employee satisfaction, and building a strong community.
                    Join us and become part of a team where your talent is truly valued.
                </p>

                <ul class="space-y-4 inline-block text-left">
                    <li class="flex items-center gap-3">
                        <span class="text-purple-400">✔</span>
                        Transparent hiring process –
                        <a href="home.php#hire" class="text-yellow-400 hover:underline">Click here</a>
                    </li>

                    <li class="flex items-center gap-3">
                        <span class="text-purple-400">✔</span>
                        Career growth opportunities
                    </li>

                    <li class="flex items-center gap-3">
                        <span class="text-purple-400">✔</span>
                        Inclusive work culture
                    </li>
                </ul>

            </div>
        </section>


    </section>


    <!-- Mission & Vision Section -->
    <section class="bg-black text-gray-300 py-20 px-6 md:px-20">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">

            <!-- Vision Card -->
            <div class="flex flex-col items-center text-center space-y-4 animate-fadeInLeft">
                <h2 class="text-3xl font-bold">Our Vision</h2>

                <!-- Image -->
                <div class=" w-full max-w-sm overflow-hidden shadow-2xl">
                    <img src="image/imgv.jpg" alt="Our Vision"
                        class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105">
                </div>
                <div class="w-20 h-1 bg-yellow-500 rounded-full mb-2"></div>
                <!-- Text -->
                <p class="text-gray-400 text-base md:text-lg">
                    To become a trusted internal recruitment platform that empowers organizations with scalable and
                    transparent hiring solutions.
                </p>
            </div>

            <!-- Mission Card -->
            <div class="flex flex-col items-center text-center space-y-4 animate-fadeInRight">
                <h2 class="text-3xl font-bold ">Our Mission</h2>

                <!-- Image -->
                <div class="w-full max-w-sm overflow-hidden shadow-2xl">
                    <img src="image/imgm.jpg" alt="Our Mission"
                        class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105">
                </div>
                <div class="w-20 h-1 bg-yellow-500 rounded-full mb-2"></div>
                <!-- Text -->
                <p class="text-gray-400 text-base md:text-lg">
                    To simplify hiring through seamless candidate journeys, structured job management, and data-driven
                    recruitment.
                </p>
            </div>

        </div>
    </section>
    <!-- Contact Details Section -->
    <section id="contact" class="bg-black text-gray-400 py-24 px-6 md:px-20 mb-6">
        <div class="max-w-7xl mx-auto text-center">

            <!-- Heading -->
            <h2 class="text-3xl md:text-4xl font-bold text-[#D7AE27] mb-4">
                Contact<span class="text-white"> Us</span>
            </h2>
            <p class="max-w-2xl mx-auto mb-16 text-gray-500">
                Have questions or ideas? We’re always open to a conversation.
            </p>

            <!-- Contact Cards -->
            <div class="grid md:grid-cols-3 gap-10">

                <!-- Address -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                    <span class="text-3xl mb-4 block">📍</span>
                    <h3 class="text-xl font-semibold text-white mb-2">Address</h3>
                    <p class="leading-relaxed">
                        123 Innovation Street,<br>
                        Surat, Gujarat, India
                    </p>
                </div>

                <!-- Email -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                    <span class="text-3xl mb-4 block"><i class="fa-solid fa-envelope"></i></span>
                    <h3 class="text-xl font-semibold text-white mb-2">Email</h3>
                    <p>
                        <a href="mailto:CareerCraft12@gmail.com" class="hover:text-white transition">
                            CareerCraft12@gmail.com
                        </a>
                    </p>
                </div>

                <!-- Phone -->
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 text-center
                       hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                    <span class="text-3xl mb-4 block"><i class="fa-solid fa-phone"></i></span>
                    <h3 class="text-xl font-semibold text-white mb-2">Phone</h3>
                    <p>
                        <a href="tel:+919876543210" class="hover:text-white transition">
                            +91 98765 43210
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </section>

<?php
  include("include/footer.php");
?>







    <!-- Scroll Animations -->
    <script>

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        }, { threshold: 0.2 });
        document.querySelectorAll('.fade-left, .fade-right, .fade-up, .zoom-in, .rotate-in').forEach(el => observer.observe(el));

        //================ progress==================
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

</body>

</html>