<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$siteName = "CareerCraft";
$year = date("Y");

// 🔒 Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Safe output helper
function safe($data){
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Card component
function card($title, $content){
    return "
    <div class='bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition duration-300'>
        <h2 class='text-xl font-semibold text-[#D7AE27] mb-3'>".safe($title)."</h2>
        <div class='text-gray-400 leading-relaxed'>
            $content
        </div>
    </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Privacy Policy | <?= safe($siteName) ?></title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>

<body class="bg-black text-gray-300 font-sans">

<!-- NAVBAR -->
<?php include("../include/navbar.php"); ?>

<!-- BACK BUTTON -->
<div class="max-w-6xl mx-auto px-1 pt-20">
    <a href="http://localhost/php_program/project/home.php" 
       class="inline-block text-[#D7AE27] font-semibold hover:underline transition">
       ← Back to Home
    </a>
</div>

<!-- HERO -->
<section class="text-center py-16 border-b border-white/10 px-6">

    <h1 class="text-4xl md:text-5xl  text-white">
        Privacy <span class="text-[#D7AE27]">Policy</span>
    </h1>

    <p class="text-gray-400 mt-6 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
        Please read our policy carefully before using 
        <span class="text-[#D7AE27] font-semibold"><?= safe($siteName) ?></span> – 
        your trusted job portal platform.
    </p>

</section>

<!-- CONTENT -->
<div class="max-w-5xl mx-auto px-6 py-16">

<p class="text-gray-400 mb-10 leading-relaxed text-center max-w-3xl mx-auto">
At <span class="text-[#D7AE27] font-semibold"><?= safe($siteName) ?></span>, 
we respect your privacy and are committed to protecting your personal information.
</p>

<div class="space-y-8">

<?= card("1. Information We Collect", "
<p>
We collect details like name, email, phone number, resume,
education, experience, and job preferences.
</p>
") ?>

<?= card("2. How We Use Information", "
<ul class='list-disc pl-6 space-y-1'>
<li>Connect job seekers with employers</li>
<li>Manage applications</li>
<li>Recommend jobs</li>
<li>Improve experience</li>
<li>Send updates</li>
</ul>
") ?>

<?= card("3. Sharing of Information", "
<p>
Your profile may be shared with recruiters. We never sell your data.
</p>
") ?>

<?= card("4. Data Security", "
<p>
We apply strong security measures, but no system is 100% secure.
</p>
") ?>

<?= card("5. Cookies", "
<p>
Cookies help improve user experience and analyze usage.
</p>
") ?>

<?= card("6. User Rights", "
<p>
You can request access, update, or deletion of your data anytime.
</p>
") ?>

<?= card("7. Policy Updates", "
<p>
".safe($siteName)." may update this policy when needed.
</p>
") ?>

<?= card("8. Contact Us", "
<p>If you have questions:</p>
<p class='mt-2'>
<a href='http://localhost/php_program/project/include/contact.php'
class='text-[#D7AE27] hover:underline'>
Contact Us
</a>
</p>
") ?>

</div>

<!-- LAST UPDATED -->
<p class="text-gray-500 text-sm mt-14 text-center">
Last Updated: <?= safe($year) ?>
</p>

</div>

<!-- FOOTER -->
<footer class="text-center py-6 border-t border-gray-800 text-gray-400 text-sm">
    © <?= date("Y"); ?> CareerCraft. All Rights Reserved.
</footer>

</body>
</html>