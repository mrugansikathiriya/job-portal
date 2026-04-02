<?php
require "../config/db.php";
require "admin_auth.php";
require "../authc/csrf.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}
// Get seekers whose users are active
$result = mysqli_query($conn, "
    SELECT job_seeker.*, users.uname, users.email, users.status 
    FROM job_seeker
    INNER JOIN users ON job_seeker.uid = users.uid
    WHERE users.status = 'active'
    ORDER BY job_seeker.sid DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Manage Job Seekers</title>

    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black/90 text-white min-h-screen p-8">

<div class="flex items-center gap-3 mb-6">
    <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
    <span class="text-xl font-bold text-[#D7AE27]">
        CareerCraft
    </span>
</div>

<div class="max-w-7xl mx-auto">

<h1 class="text-3xl font-bold mb-8 text-[#D7AE27] text-center">
    Manage Job Seekers
</h1>

<div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

<table class="w-full text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
    <th class="p-3">Seeker ID</th>
    <th class="p-3">User Name</th>
    <th class="p-3">Email</th>
    <th class="p-3">Full Name</th>
    <th class="p-3">Education</th>
    <th class="p-3">Experience</th>
    <th class="p-3">Skills</th>
    <th class="p-3">Bio</th>
    <th class="p-3">Birthdate</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?php echo $row['sid']; ?></td>
    <td class="p-3"><?php echo $row['uname']; ?></td>
    <td class="p-3"><?php echo $row['email']; ?></td>
    <td class="p-3"><?php echo $row['sname']; ?></td>
    <td class="p-3"><?php echo $row['education']; ?></td>
    <td class="p-3"><?php echo $row['experience']; ?></td>
    <td class="p-3"><?php echo $row['skillname']; ?></td>
    <td class="p-3">
    <?php 
    $bio = $row['bio'];
    $short = substr($bio, 0, 50); // first 50 characters
    ?>

    <span class="short-text">
        <?php echo $short; ?><?php echo (strlen($bio) > 50) ? '...' : ''; ?>
    </span>

    <span class="full-text hidden">
        <?php echo $bio; ?>
    </span>

    <?php if(strlen($bio) > 50) { ?>
        <button class="text-blue-400 ml-2 read-more-btn">
            Read More
        </button>
    <?php } ?>
    </td>    
    <td class="p-3"><?php echo $row['birthdate']; ?></td>


    </tr>

    <?php } ?>

</tbody>
</table>
</div>

<div class="mt-8">
    <a href="admin_dashboard.php"
       class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
       Back to Dashboard
    </a>
</div>

</div>
<script>
document.querySelectorAll('.read-more-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const parent = this.parentElement;
        const shortText = parent.querySelector('.short-text');
        const fullText = parent.querySelector('.full-text');

        if(fullText.classList.contains('hidden')){
            fullText.classList.remove('hidden');
            shortText.classList.add('hidden');
            this.innerText = "Read Less";
        } else {
            fullText.classList.add('hidden');
            shortText.classList.remove('hidden');
            this.innerText = "Read More";
        }
    });
});
</script>
</body>
</html>