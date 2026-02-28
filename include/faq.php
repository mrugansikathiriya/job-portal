<?php
// faq.php 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQ - Career Craft</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link href="../dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">    <link rel="icon" href="../image/logo3.jpg" type="image/png"></head>  
  
</head>

<body class="bg-black text-white font-sans">

<!-- Back Button -->
<div class="max-w-6xl mx-auto px-6 pt-6">
    <a href="../home.php" 
       class="inline-block text-[#D7AE27] font-semibold hover:underline transition duration-300">
       ← Back to Home
    </a>
</div>

<!-- Hero Section -->
<section class="text-center py-20 px-6 border-b border-white/10">
    <h2 class="text-4xl font-bold mb-4 text-[#D7AE27]">
        Frequently Asked Questions
    </h2>
    <p class="text-gray-300 max-w-2xl mx-auto">
        Find answers to common questions about applying for IT jobs,
        uploading resumes, and posting job openings on Career Craft.
    </p>
</section>

<!-- FAQ Search Bar - -->
<div class="max-w-4xl mx-auto px-6 pb-14 mt-16">    <div class="relative">
<i class="fa-solid fa-magnifying-glass absolute -left-6 top-1/2 -translate-y-1/2 text-[#D7AE27]"></i>
        <input 
            type="text" 
            id="faqSearch" 
            placeholder="Search FAQs..." 
            onkeyup="filterFAQs()"
            class="w-full pl-12 pr-4 py-4 rounded-full 
                   bg-white/5 backdrop-blur-xl 
                   border border-white/10 
                   text-white placeholder-gray-400
                   focus:outline-none 
                   focus:border-[#D7AE27]
                   focus:ring-2 focus:ring-[#D7AE27]/40
                   transition duration-300"
        >
    </div>
</div>

<!-- FAQ Container -->
<section class="max-w-5xl mx-auto px-6 py-20 space-y-20">

    <!-- ============================= -->
    <!-- JOB SEEKER SECTION -->
    <!-- ============================= -->
    <div>
        <h3 class="text-3xl font-bold text-primary mb-10 border-b border-primary/40 pb-3">
            For Job Seekers
        </h3>

        <div class="space-y-6">

            <!-- FAQ ITEM -->
            <!-- Repeatable Card Design -->
            
            <!-- 1 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(1)" class="faq-btn">
                    <span>Is it free to apply for IT jobs?</span>
                    <span id="icon-1" class="faq-icon">+</span>
                </button>
                <div id="faq-1" class="faq-content">
                    Yes, our platform is 100% free for job seekers across India. 
                    We never charge candidates for applying to jobs.
                </div>
            </div>

            <!-- 2 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(2)" class="faq-btn">
                    <span>How do I upload my resume?</span>
                    <span id="icon-2" class="faq-icon">+</span>
                </button>
                <div id="faq-2" class="faq-content">
                    Log in → Go to Dashboard → Click Edit Profile → Upload Resume 
                    (PDF/DOC/DOCX) → Save.
                </div>
            </div>

            <!-- 3 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(3)" class="faq-btn">
                    <span>Can I update my resume later?</span>
                    <span id="icon-3" class="faq-icon">+</span>
                </button>
                <div id="faq-3" class="faq-content">
                    Yes. You can update or replace your resume anytime from your profile dashboard.
                </div>
            </div>

            <!-- 4 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(4)" class="faq-btn">
                    <span>Do you offer remote IT jobs?</span>
                    <span id="icon-4" class="faq-icon">+</span>
                </button>
                <div id="faq-4" class="faq-content">
                    Yes. Many companies post remote and work-from-home IT jobs. 
                    You can filter by Remote while searching.
                </div>
            </div>

            <!-- 5 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(5)" class="faq-btn">
                    <span>What if someone asks me for money for a job?</span>
                    <span id="icon-5" class="faq-icon">+</span>
                </button>
                <div id="faq-5" class="faq-content">
                    We never charge job seekers. If anyone asks for payment claiming to 
                    represent us, please report immediately to our support team.
                </div>
            </div>

            <!-- 6 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(6)" class="faq-btn">
                    <span>How long does it take to receive responses after applying?</span>
                    <span id="icon-6" class="faq-icon">+</span>
                </button>
                <div id="faq-6" class="faq-content">
                    Response time depends on employer and job demand.<br><br>
                    • High-demand roles: 24–72 hours<br>
                    • Some companies: 1–2 weeks<br>
                    • Large companies: Longer hiring process
                </div>
            </div>

            <!-- 7 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(7)" class="faq-btn">
                    <span>Why is my resume not uploading?</span>
                    <span id="icon-7" class="faq-icon">+</span>
                </button>
                <div id="faq-7" class="faq-content">
                    Possible reasons:<br><br>
                    • File size too large<br>
                    • Unsupported format<br>
                    • Slow internet connection<br>
                    • Special characters in file name<br><br>
                    Try renaming the file and uploading again.
                </div>
            </div>

            <!-- 16 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(16)" class="faq-btn">
                    <span>Do I need to create an account to apply for jobs?</span>
                    <span id="icon-16" class="faq-icon">+</span>
                </button>
                <div id="faq-16" class="faq-content">
                    Yes. You must create a free account to apply for jobs 
                    so employers can view your profile and resume.
                </div>
            </div>

            <!-- 17 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(17)" class="faq-btn">
                    <span>Can I apply for multiple jobs?</span>
                    <span id="icon-17" class="faq-icon">+</span>
                </button>
                <div id="faq-17" class="faq-content">
                    Yes. You can apply to as many jobs as you want that match your skills and interests.
                </div>
            </div>

            <!-- 20 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(20)" class="faq-btn">
                    <span>What should I do if I forget my password?</span>
                    <span id="icon-20" class="faq-icon">+</span>
                </button>
                <div id="faq-20" class="faq-content">
                    Click on "Forgot Password" on the login page and follow 
                    the instructions to reset your password via email.
                </div>
            </div>

        </div>
    </div>


    <!-- ============================= -->
    <!-- EMPLOYER SECTION -->
    <!-- ============================= -->
    <div>
        <h3 class="text-3xl font-bold text-primary mb-10 border-b border-primary/40 pb-3">
            For Employers / Companies
        </h3>

        <div class="space-y-6">

            <!-- 8 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(8)" class="faq-btn">
                    <span>Is it free for employers to post jobs?</span>
                    <span id="icon-8" class="faq-icon">+</span>
                </button>
                <div id="faq-8" class="faq-content">
                    Yes. Employers and startups in India can post IT job listings for free on our platform.
                </div>
            </div>

            <!-- 9 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(9)" class="faq-btn">
                    <span>How does Career Craft protect user data?</span>
                    <span id="icon-9" class="faq-icon">+</span>
                </button>
                <div id="faq-9" class="faq-content">
                    We implement industry-standard security practices:<br><br>
                    • Data is stored securely<br>
                    • Passwords are encrypted<br>
                    • Only registered employers can view candidate profiles<br>
                    • No selling or sharing of personal data<br>
                    • Users can delete accounts anytime
                </div>
            </div>

            <!-- 10 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(10)" class="faq-btn">
                    <span>How do I post a job?</span>
                    <span id="icon-10" class="faq-icon">+</span>
                </button>
                <div id="faq-10" class="faq-content">
                    Log in as Company → Go to Dashboard → Click "Post Job" → 
                    Fill job details → Publish.
                </div>
            </div>

            <!-- 11 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(11)" class="faq-btn">
                    <span>How can I manage or edit my posted jobs?</span>
                    <span id="icon-11" class="faq-icon">+</span>
                </button>
                <div id="faq-11" class="faq-content">
                    From your Employer Dashboard, you can edit, close, delete, 
                    or mark a job as filled anytime.
                </div>
            </div>

            <!-- 13 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(13)" class="faq-btn">
                    <span>Can I close a job once filled?</span>
                    <span id="icon-13" class="faq-icon">+</span>
                </button>
                <div id="faq-13" class="faq-content">
                    Yes. You can mark the job as "Closed" or "Filled" anytime.
                </div>
            </div>

            <!-- 14 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(14)" class="faq-btn">
                    <span>Are there limits on job postings?</span>
                    <span id="icon-14" class="faq-icon">+</span>
                </button>
                <div id="faq-14" class="faq-content">
                    Currently, employers can post multiple IT jobs for free.
                    We monitor postings to prevent spam or misuse.
                </div>
            </div>

            <!-- 15 -->
            <div class="faq-card">
                <button onclick="toggleFAQ(15)" class="faq-btn">
                    <span>How can I contact a candidate?</span>
                    <span id="icon-15" class="faq-icon">+</span>
                </button>
                <div id="faq-15" class="faq-content">
                    You can contact candidates directly using the contact details 
                    provided in their profile or resume after they apply.
                </div>
            </div>

        </div>
    </div>

</section>

<!-- ============================= -->
<!-- STYLING -->
<!-- ============================= -->

<style>
.faq-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    transition: all 0.3s ease;
}

.faq-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}

.faq-btn {
    width: 100%;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
}

.faq-icon {
    font-size: 1.5rem;
    color: #6366f1;
    transition: transform 0.3s ease;
}

.faq-content {
    max-height: 0;
    overflow: hidden;
    transition: all 0.4s ease;
    padding: 0 20px;
    color: rgba(255,255,255,0.75);
}

.faq-content.open {
    padding-bottom: 20px;
    max-height: 500px;
}
</style>


<!-- Script -->
<script>
function toggleFAQ(id) {
    const content = document.getElementById(`faq-${id}`);
    const icon = document.getElementById(`icon-${id}`);

    content.classList.toggle("open");

    if (content.classList.contains("open")) {
        icon.textContent = "−";
        icon.style.transform = "rotate(180deg)";
    } else {
        icon.textContent = "+";
        icon.style.transform = "rotate(0deg)";
    }
}

function filterFAQs() {
    const query = document.getElementById('faqSearch').value.toLowerCase();
    const faqs = document.querySelectorAll('#faq-1, #faq-2, #faq-3, #faq-4, #faq-5, #faq-6, #faq-7, #faq-8, #faq-9, #faq-10, #faq-11, #faq-12, #faq-13, #faq-14, #faq-15, #faq-16, #faq-17, #faq-18, #faq-19, #faq-20, #faq-21');

    faqs.forEach((faq) => {
        const question = faq.previousElementSibling.querySelector('span').innerText.toLowerCase();
        const parentCard = faq.parentElement;

        if(question.includes(query)) {
            parentCard.classList.remove('hidden');
        } else {
            parentCard.classList.add('hidden');
        }
    });
}
</script>

</body>
</html>