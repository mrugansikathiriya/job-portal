<?php
require "../config/db.php";
require "../array/skill.php";
session_start();

$uid = $_SESSION['uid'] ?? 1;
    $success = false;

$sname = $education = $experience = $bio = "";
$skillname = "";
$birthdate = "";
$birthDateErr = "";
$snameErr = $educationErr =$experienceErr = $skillErr = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $sname = trim($_POST["sname"] ?? "");
    $education = trim($_POST["education"] ?? "");
    $experience = $_POST["experience"] ?? 0;
    $skillname = trim($_POST["skillname"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $birthdate = $_POST["birthdate"] ?? "";

    if ($sname === "") $snameErr = "Full name required";
    if ($education === "") $educationErr = "Education required";
    if ($skillname === "") $skillErr = "At least one skill required";
    if ($birthdate === "") $birthDateErr = "birthdate date is required";
    if ($experience==="") $experienceErr = "Experience is required";


    if ($snameErr=="" && $educationErr=="" && $skillErr=="" && $birthDateErr=="" && $experienceErr=="") {

       $sql = "UPDATE job_seeker
        SET sname='$sname',
            education='$education',
            experience='$experience',
            skillname='$skillname',
            bio='$bio',
            birthdate='$birthdate'
        WHERE uid='$uid'";   // ← close properly
         if(mysqli_query($conn,$sql)){

        mysqli_query($conn,"
        UPDATE users SET is_completed=1 WHERE uid='$uid'
        ");

        $_SESSION['is_completed']=1;
    $success = true;

        $sname = $education = $experience = $bio = "";
        $skillname = "";
        $birthdate = "";

    header("Location:http://localhost/php_program/project/home.php");
        exit;
    }
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Career Craft | Job Seeker Profile</title>
    <link href="../dist/styles.css" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="icon" href="../image/logo3.jpg" type="image/png">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Make calendar icon white */
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                cursor: pointer;
            }
            </style>
     </head>

<body class="bg-black text-white px-4 py-8  ">
 <?php if($success): ?>
    <div id="successToast"
    class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500">
    Job Seeker Registered Successfully
    </div>
    <?php endif; ?>
    <?php include("../include/navbar.php");?>
     <a href="http://localhost/php_program/project/auth/login.php" class="absolute left-4 top-4  mt-20 text-yellow-400 text-sm hover:underline">← Back</a>

<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl 
p-8 border border-white/10 mt-20 mb-10">


<h2 class="text-3xl font-bold text-[#D7AE27] mb-8 text-center">
Job Seeker Profile
</h2>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

<!-- Full Name -->
<div>
<label class="block mb-2">Full Name *</label>
<input name="sname"
value="<?= htmlspecialchars($sname) ?>"
class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none">
<p class="text-red-500 text-sm mt-1"><?= $snameErr ?></p>
</div>

<!-- Education -->
<div>
<label class="block mb-2">Education *</label>
<input name="education"
value="<?= htmlspecialchars($education) ?>"
class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none">
<p class="text-red-500 text-sm mt-1"><?= $educationErr ?></p>
</div>
<!-- birthdate -->
<div>
    <label class="block mb-1 font-semibold">
        Birth Date <span class="text-red-500">*</span>
    </label>

    <input type="date"
           name="birthdate"
           value="<?= htmlspecialchars($birthdate) ?>"
class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none">

    <p class="text-red-500 text-sm mt-1">
        <?= $birthDateErr ?>
    </p>
</div>
<!-- Experience -->
<div>
    <label class="block mb-1 font-semibold text-white">
        Experience <span class="text-red-500">*</span>
    </label>

    <select name="experience"
      class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none">


        <option value="" class="bg-black text-white">
            -- Select Experience --
        </option>

        <option value="Fresher" <?= ($experience=="Fresher") ? "selected" : "" ?> class="bg-black">
            Fresher
        </option>

        <option value="0-1 Years" <?= ($experience=="0-1 Years") ? "selected" : "" ?> class="bg-black">
            0-1 Years
        </option>

        <option value="1-3 Years" <?= ($experience=="1-3 Years") ? "selected" : "" ?> class="bg-black">
            1-3 Years
        </option>

        <option value="3-5 Years" <?= ($experience=="3-5 Years") ? "selected" : "" ?> class="bg-black">
            3-5 Years
        </option>

        <option value="5+ Years" <?= ($experience=="5+ Years") ? "selected" : "" ?> class="bg-black">
            5+ Years
        </option>

    </select>

    <p class="text-red-500 text-sm mt-1">
        <?= $experienceErr ?>
    </p>
</div>

<!-- Skills -->
<div class="md:col-span-2 relative">
<label class="block mb-2">Skills *</label>

<input type="text" id="skillInput"
placeholder="Type skill and press Enter"
class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none">

<div id="suggestions"
class="absolute bg-black border border-white/20 w-full mt-1 rounded-md hidden max-h-40 overflow-y-auto z-10"></div>

<div id="skillTags" class="flex flex-wrap gap-2 mt-3"></div>

<input type="hidden" name="skillname" id="skillHidden">

<p class="text-red-500 text-sm mt-1"><?= $skillErr ?></p>
</div>

<!-- Bio -->
<div class="md:col-span-2">
<label class="block mb-2">Bio</label>
<textarea name="bio" rows="4"
class="w-full bg-black border border-white/20 rounded-md p-3 focus:border-[#D7AE27] focus:outline-none"><?= htmlspecialchars($bio) ?></textarea>
</div>

<!-- Submit -->
<div class="md:col-span-2 text-center mt-4">
<button
class="bg-[#D7AE27] text-black px-10 py-3 rounded-md font-semibold hover:bg-yellow-400 transition">
Save Profile
</button>
</div>

</form>
</div>

<script>

const skillsArray = <?= json_encode($skillsList) ?>;
const input = document.getElementById("skillInput");
const suggestionsBox = document.getElementById("suggestions");
const tagsContainer = document.getElementById("skillTags");
const hiddenInput = document.getElementById("skillHidden");

let skills = [];

/* SHOW SUGGESTIONS */
input.addEventListener("input", function() {
    const value = this.value.toLowerCase();
    suggestionsBox.innerHTML="";
    if(value===""){
        suggestionsBox.classList.add("hidden");
        return;
    }

    const filtered = skillsArray.filter(skill =>
        skill.toLowerCase().includes(value)
    );

    filtered.forEach(skill=>{
        const div=document.createElement("div");
        div.className="p-2 hover:bg-[#D7AE27] hover:text-black cursor-pointer";
        div.innerText=skill;
        div.onclick=()=>{
            addSkill(skill);
            input.value="";
            suggestionsBox.classList.add("hidden");
        };
        suggestionsBox.appendChild(div);
    });

    suggestionsBox.classList.remove("hidden");
});

/* ADD SKILL ON ENTER */
input.addEventListener("keydown", function(e){
    if(e.key==="Enter" && this.value.trim()!==""){
        e.preventDefault();
        addSkill(this.value.trim());
        this.value="";
        suggestionsBox.classList.add("hidden");
    }
});

function addSkill(skill){
    if(skills.includes(skill)) return;
    skills.push(skill);
    renderSkills();
}

function removeSkill(skill){
    skills=skills.filter(s=>s!==skill);
    renderSkills();
}

function renderSkills(){
    tagsContainer.innerHTML="";
    skills.forEach(skill=>{
        const div=document.createElement("div");
        div.className="bg-[#D7AE27] text-black px-3 py-1 rounded-full flex items-center gap-2";
        div.innerHTML=skill+
        `<button onclick="removeSkill('${skill}')"
        class="font-bold">×</button>`;
        tagsContainer.appendChild(div);
    });
    hiddenInput.value=skills.join(",");
}

</script>
    <?php include("../include/footer.php");?>

</body>
</html>