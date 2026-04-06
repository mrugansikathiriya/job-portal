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
    <th class="p-3">Profile Image</th>
    <th class="p-3">Education</th>
    <th class="p-3">Experience</th>
    <th class="p-3">Skills</th>
    <th class="p-3">Birthdate</th>
        <th class="p-3">Bio</th>

</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?php echo $row['sid']; ?></td>
    <td class="p-3"><?php echo $row['uname']; ?></td>
    <td class="p-3"><?php echo $row['email']; ?></td>
    <td class="p-3"><?php echo $row['sname']; ?></td>
    <td class="p-3">
   <?php if($row['profile_image']) { ?>
    <img src="../seeker/uploads/<?php echo $row['profile_image']; ?>" class="h-12 w-12 rounded">
<?php } else { ?>
    No Logo
<?php } ?>
   
</td>
    <td class="p-3"><?php echo $row['education']; ?></td>
    <td class="p-3"><?php echo $row['experience']; ?></td>
    <td class="p-3"><?php echo $row['skillname']; ?></td>
 <td class="p-3"><?php echo $row['birthdate']; ?></td>
    <td class="p-3">
   <?php if(!empty($row['bio'])) { ?>
    <button 
        class="text-blue-400 hover:underline view-bio-btn"
        data-bio="<?php echo htmlspecialchars($row['bio']); ?>">
        View 
    </button>
<?php } else { ?>
    <span class="text-gray-400">No Bio</span>
<?php } ?>
    </td>    
    




    </tr>

    <?php } ?>

</tbody>
</table>


<!-- BIO MODAL -->
<div id="bioModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
    <div class="bg-[#0f0f0f] text-white p-6 rounded-xl max-w-lg w-full shadow-xl border border-white/20">
        
        <h2 class="text-xl font-bold mb-4 text-[#D7AE27]">Seeker's Bio</h2>
        
        <p id="bioContent" class="text-gray-300 whitespace-pre-line"></p>
        
        <div class="mt-6 text-right">
            <button id="closeModal" 
                class="bg-[#D7AE27] text-black px-5 py-2 rounded hover:bg-yellow-500">
                Close
            </button>
        </div>
    </div>
</div>
</div>

<div class="mt-8">
    <a href="admin_dashboard.php"
       class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
       Back to Dashboard
    </a>
</div>

</div>

<script>
const modal = document.getElementById("bioModal");
const bioContent = document.getElementById("bioContent");
const closeModal = document.getElementById("closeModal");

// Open modal
document.querySelectorAll(".view-bio-btn").forEach(btn => {
    btn.addEventListener("click", function(){
        const bio = this.getAttribute("data-bio");
        bioContent.textContent = bio;
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    });
});

// Close modal
closeModal.addEventListener("click", () => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
});

// Close on outside click
modal.addEventListener("click", (e) => {
    if(e.target === modal){
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }
});
</script>
</body>
</html>