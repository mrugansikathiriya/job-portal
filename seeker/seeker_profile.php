<?php
session_start();

require "../config/db.php";
require "../array/skill.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
    exit();
}
if(isset($_SESSION['is_completed']) && $_SESSION['is_completed'] == 1){
    header("Location: sdashboard.php"); // Seeker dashboard
    exit();
}

$uid = $_SESSION['uid'];

$sname = $education = $experience = $bio = "";
$skillname = "";
$birthdate = "";
$imageName = "";

$snameErr = $educationErr = $experienceErr = $skillErr = $birthDateErr = "";
$imageErr = "";

// Fetch existing data
$result = mysqli_query($conn,"SELECT * FROM job_seeker WHERE uid='$uid'");
$existing = mysqli_fetch_assoc($result);

if($existing){
    $sname = $existing['sname'];
    $education = $existing['education'];
    $experience = $existing['experience'];
    $skillname = $existing['skillname'];
    $bio = $existing['bio'];
    $birthdate = $existing['birthdate'];
    $imageName = $existing['profile_image'];
}

// Form submit
if($_SERVER["REQUEST_METHOD"] === "POST"){

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $sname = trim($_POST["sname"] ?? "");
    $education = trim($_POST["education"] ?? "");
    $experience = $_POST["experience"] ?? "";
    $skillname = trim($_POST["skillname"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $birthdate = $_POST["birthdate"] ?? "";

    // Image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(!in_array($ext, $allowed)){
            $imageErr = "Only JPG, PNG, WEBP allowed";
        } elseif($_FILES['profile_image']['size'] > 2 * 1024 * 1024){
            $imageErr = "Image must be less than 2MB";
        } else {
            $imageName = "user_" . $uid . "_" . time() . ".jpg";
            $targetFile = $targetDir . $imageName;

            $check = getimagesize($_FILES["profile_image"]["tmp_name"]);

            if ($check === false) {
                $imageErr = "File is not a valid image";
            } else {
                list($width, $height) = getimagesize($_FILES["profile_image"]["tmp_name"]);
                $src = imagecreatefromstring(file_get_contents($_FILES["profile_image"]["tmp_name"]));
                $dst = imagecreatetruecolor(300, 300);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, 300, 300, $width, $height);
                imagejpeg($dst, $targetFile, 80);
                imagedestroy($src);
                imagedestroy($dst);
            }
        }
    }

    // Validation
    if ($sname === "") $snameErr = "Full name required";
    if ($education === "") $educationErr = "Education required";
    if ($experience === "") $experienceErr = "Experience required";
    if ($skillname === "") $skillErr = "At least one skill required";
    if ($birthdate === "") $birthDateErr = "Birthdate required";

    if ($snameErr=="" && $educationErr=="" && $experienceErr=="" && $skillErr=="" && $birthDateErr=="" && $imageErr=="") {

      $imageQuery = $imageName != "" ? "profile_image='$imageName'" : "";

$sql = "UPDATE job_seeker SET
            sname='$sname',
            education='$education',
            experience='$experience',
            skillname='$skillname',
            bio='$bio',
            birthdate='$birthdate'";

if($imageQuery != "") $sql .= ", $imageQuery";

$sql .= " WHERE uid='$uid'";

    if(mysqli_query($conn,$sql)){

    // ✅ STEP 1: Send notification to all companies
    $message = mysqli_real_escape_string($conn, "New seeker profile added. Check it now!");

    $companies = mysqli_query($conn, "SELECT uid FROM users WHERE role='company'");

    while ($row = mysqli_fetch_assoc($companies)) {
        $company_id = (int)$row['uid'];

        mysqli_query($conn, "
            INSERT INTO notifications (uid, message, is_read, created_at)
            VALUES ($company_id, '$message', 0, NOW())
        ");
    }

    // ✅ STEP 2: Your existing code
    mysqli_query($conn,"UPDATE users SET is_completed=1 WHERE uid='$uid'");
    $_SESSION['is_completed'] = 1;
    $_SESSION['profile_success'] = "Profile completed successfully!";
    
    if($imageName != "") $_SESSION['profile_image'] = $imageName;

    regenerateCSRFToken();
    header("Location: sdashboard.php");
    exit();
}
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Career Craft | Job Seeker Registration</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
.input-field{
    width:100%;
    background:black;
    border:1px solid rgba(255,255,255,0.2);
    border-radius:6px;
    padding:10px;
    margin-top:5px;
    color:white;
}
.error{
    color:#f87171;
    font-size:14px;
    margin-top:4px;
}
</style>
</head>
<body class="bg-black px-4 py-6 overflow-x-hidden overflow-y-auto text-white">

<?php include("../include/navbar.php");?>

<a href="../auth/login.php" class="absolute left-4 top-4 mt-20 text-yellow-400 text-sm hover:underline">← Back</a>

   <?php if(isset($_SESSION['login_success'])): ?>
<div id="flashMessage"
     class="fixed top-15 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['login_success']; ?></span>

    <!-- Close Button -->
    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['login_success']); ?>
<?php endif; ?>
<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl p-6 sm:p-8 border border-white/10 mt-20 mb-10">
<h2 class="text-2xl md:text-3xl font-bold text-[#D7AE27] mb-6 text-center">
Job Seeker Registration
</h2>

<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5" novalidate>
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<!-- Profile Image -->
<div class="md:col-span-2 mb-4">
<label class="block mb-2">Profile Image</label>
<div class="flex items-center gap-6">
<img id="imagePreview"
src="<?= !empty($imageName) ? '../uploads/profile/'.$imageName : 'https://ui-avatars.com/api/?name='.urlencode($sname ?? 'User').'&background=D7AE27&color=000'; ?>"
class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 shadow">
<input type="file" name="profile_image" id="profileInput" accept="image/*" class="text-white">
</div>
<p id="imageErr" class="error"><?= $imageErr ?></p>
</div>

<!-- Full Name -->
<div>
<label>Full Name <span class="text-red-500">*</span></label>
<input name="sname" id="sname" value="<?= htmlspecialchars($sname) ?>" class="input-field">
<p id="snameErr" class="error"><?= $snameErr ?></p>
</div>

<!-- Education -->
<div>
<label>Education <span class="text-red-500">*</span></label>
<input name="education" id="education" value="<?= htmlspecialchars($education) ?>" class="input-field">
<p id="educationErr" class="error"><?= $educationErr ?></p>
</div>

<!-- Birthdate -->
<div>
<label>Birth Date <span class="text-red-500">*</span></label>
<input type="date" name="birthdate" id="birthdate" value="<?= htmlspecialchars($birthdate) ?>" class="input-field">
<p id="birthDateErr" class="error"><?= $birthDateErr ?></p>
</div>

<!-- Experience -->
<div>
<label>Experience <span class="text-red-500">*</span></label>
<select name="experience" id="experience" class="input-field">
<option value="">Select Experience</option>
<option value="Fresher" <?= ($experience=="Fresher")?"selected":"" ?>>Fresher</option>
<option value="0-1 Years" <?= ($experience=="0-1 Years")?"selected":"" ?>>0-1 Years</option>
<option value="1-3 Years" <?= ($experience=="1-3 Years")?"selected":"" ?>>1-3 Years</option>
<option value="3-5 Years" <?= ($experience=="3-5 Years")?"selected":"" ?>>3-5 Years</option>
<option value="5+ Years" <?= ($experience=="5+ Years")?"selected":"" ?>>5+ Years</option>
</select>
<p id="experienceErr" class="error"><?= $experienceErr ?></p>
</div>

<!-- Skills -->
<div class="md:col-span-2">
<label>Skills <span class="text-red-500">*</span></label>
<div class="relative">
    <input type="text" id="skillInput" placeholder="Type skill and press Enter" class="input-field">
    <div id="suggestionBox"
         class="absolute left-0 right-0 bg-black border border-white/20 rounded-md mt-1 hidden max-h-40 overflow-y-auto z-50">
    </div>
</div>

<div id="skillTags" class="flex flex-wrap gap-2 mt-3"></div>
<input type="hidden" name="skillname" id="skillHidden" value="<?= htmlspecialchars($skillname) ?>">
<p id="skillErr" class="error"><?= $skillErr ?></p>
</div>

<!-- Bio -->
<div class="md:col-span-2">
<label>Bio</label>
<textarea name="bio" id="bio" rows="4" class="input-field resize-none"><?= htmlspecialchars($bio) ?></textarea>
</div>

<div class="md:col-span-2 mt-6 flex justify-center">
<button class="bg-[#D7AE27] text-black px-8 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
Register
</button>
</div>
</form>
</div>

<script>
const skillsArray = <?= json_encode($skillsList) ?>;
const skillInput = document.getElementById("skillInput");
const skillTags = document.getElementById("skillTags");
const skillHidden = document.getElementById("skillHidden");
let skills = <?= json_encode(explode(",",$skillname)) ?> || [];

// Render skills
function renderSkills(){
    skillTags.innerHTML = "";
    skills.forEach(skill=>{
        const div = document.createElement("div");
        div.className = "bg-[#D7AE27] text-black px-3 py-1 rounded-full flex items-center gap-2";
        div.innerHTML = skill+`<button onclick="removeSkill('${skill}')" class="font-bold">×</button>`;
        skillTags.appendChild(div);
    });
    skillHidden.value = skills.join(",");
    validateSkills();
}

// Add/Remove Skills
function addSkill(skill){
    if(!skills.includes(skill)){
        skills.push(skill);
        renderSkills();
    }
}
function removeSkill(skill){
    skills = skills.filter(s=>s!==skill);
    renderSkills();
}

// Input events
skillInput.addEventListener("keydown", function(e){
    if(e.key==="Enter" && this.value.trim()!==""){
        e.preventDefault();
        addSkill(this.value.trim());
        this.value="";
    }
});
const suggestionBox = document.getElementById("suggestionBox");

skillInput.addEventListener("input", function () {
    const value = this.value.toLowerCase().trim();

    suggestionBox.innerHTML = "";

    if (value === "") {
        suggestionBox.classList.add("hidden");
        return;
    }

    const filtered = skillsArray.filter(skill =>
        skill.toLowerCase().includes(value) &&
        !skills.includes(skill)
    );

    if (filtered.length === 0) {
        suggestionBox.classList.add("hidden");
        return;
    }

    filtered.forEach(skill => {
        const div = document.createElement("div");
        div.className = "px-3 py-2 hover:bg-[#D7AE27] hover:text-black cursor-pointer";
        div.textContent = skill;

        div.addEventListener("click", function () {
            addSkill(skill);
            skillInput.value = "";
            suggestionBox.classList.add("hidden");
        });

        suggestionBox.appendChild(div);
    });

    suggestionBox.classList.remove("hidden");
});

// Hide suggestion when clicking outside
document.addEventListener("click", function (e) {
    if (!e.target.closest("#skillInput") && !e.target.closest("#suggestionBox")) {
        suggestionBox.classList.add("hidden");
    }
});

// Image preview & validation
const imageInput = document.getElementById("profileInput");
const imagePreview = document.getElementById("imagePreview");
const imageErr = document.getElementById("imageErr");

imageInput.addEventListener("change", function(){
    const file = this.files[0];
    if(!file) return;
    const allowed = ["image/jpeg","image/png","image/webp"];
    if(!allowed.includes(file.type)){
        imageErr.textContent = "Only JPG, PNG, WEBP allowed";
    } else if(file.size > 2*1024*1024){
        imageErr.textContent = "Image must be less than 2MB";
    } else {
        imageErr.textContent = "";
        const reader = new FileReader();
        reader.onload = e => imagePreview.src = e.target.result;
        reader.readAsDataURL(file);
    }
});

// Live validation
const sname = document.getElementById("sname");
const education = document.getElementById("education");
const birthdate = document.getElementById("birthdate");
const experience = document.getElementById("experience");
const skillErr = document.getElementById("skillErr");

function checkRequired(input, err, msg){
    if(input.value.trim()===""){ err.textContent = msg; return false; }
    err.textContent = ""; return true;
}
function validateSkills(){ 
    if(skills.length===0){ skillErr.textContent="At least one skill required"; return false;}
    skillErr.textContent=""; return true;
}

// Add listeners
sname.addEventListener("input", ()=>checkRequired(sname,document.getElementById("snameErr"),"Full name required"));
education.addEventListener("input", ()=>checkRequired(education,document.getElementById("educationErr"),"Education required"));
birthdate.addEventListener("change", ()=>checkRequired(birthdate,document.getElementById("birthDateErr"),"Birthdate required"));
experience.addEventListener("change", ()=>checkRequired(experience,document.getElementById("experienceErr"),"Experience required"));

// Form submission
document.querySelector("form").addEventListener("submit", function(e){
    let valid = true;
    if(!checkRequired(sname,document.getElementById("snameErr"),"Full name required")) valid=false;
    if(!checkRequired(education,document.getElementById("educationErr"),"Education required")) valid=false;
    if(!checkRequired(birthdate,document.getElementById("birthDateErr"),"Birthdate required")) valid=false;
    if(!checkRequired(experience,document.getElementById("experienceErr"),"Experience required")) valid=false;
    if(!validateSkills()) valid=false;

    const file = imageInput.files[0];
    if(file && (file.size>2*1024*1024 || !["image/jpeg","image/png","image/webp"].includes(file.type))){
        valid=false;
        imageErr.textContent="Invalid image file";
    }

    if(!valid) e.preventDefault();
});

// Initial render
renderSkills();

function closeFlash() {
    const flash = document.getElementById("flashMessage");
    if (flash) {
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }
}

// Auto hide after 1 minute (60000 milliseconds)
setTimeout(function(){
    closeFlash();
}, 60000);
</script>

<?php include("../include/footer.php");?>
</body>
</html>