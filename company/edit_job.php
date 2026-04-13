<?php
session_start();

require "../config/db.php";
require "../array/skill.php";
require "../array/location.php";
require "../array/role.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];
$jid = intval($_GET['jid'] ?? 0);

// Fetch job and company
$jobRes = mysqli_query($conn, "SELECT * FROM job JOIN company ON job.cid = company.cid WHERE job.jid='$jid' AND company.uid='$uid'");
if(mysqli_num_rows($jobRes) == 0){
    die("Job not found or unauthorized");
}
$job = mysqli_fetch_assoc($jobRes);

$errors = [];
if($_SERVER['REQUEST_METHOD'] == 'POST'){

     if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
        $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $experience = trim($_POST['experience_required']);
    $job_type = trim($_POST['job_type']);
    $work_mode = trim($_POST['work_mode']);
    $salary = trim($_POST['salary']);
    $deadline = $_POST['deadline'];
    $description = trim($_POST['description']);

    if(!$title) $errors[] = "Job title required";

    if(empty($errors)){
        $stmt = $conn->prepare("UPDATE job SET title=?, location=?, experience_required=?, job_type=?, work_mode=?, salary=?, deadline=?, description=? WHERE jid=?");
        $stmt->bind_param("ssssssssi",$title,$location,$experience,$job_type,$work_mode,$salary,$deadline,$description,$jid);
        $stmt->execute();
        $stmt->close();
        $_SESSION['jobedit_success'] = "job updated successfully!";

            regenerateCSRFToken();

        header("Location: view_job.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Edit Job</title>
   <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png"></head>
<style>
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
<body class="bg-black text-white min-h-screen">
<?php include("../include/navbar.php"); ?>
<a href="cdashboard.php"
   class="inline-block mt-20 mb-4 text-yellow-400 text-sm hover:underline">
   ← Back
</a>

<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl 
p-6 sm:p-8 border border-white/10 text-white  mb-10">

<h2 class="text-2xl md:text-3xl font-bold text-[#D7AE27] mb-6 text-center">
Edit Job
</h2>

<?php if(!empty($errors)) { foreach($errors as $err) echo "<p class='text-red-500'>$err</p>"; } ?>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5" novalidate>

<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<!-- Job Title -->
<div>
<label>Job Title <span class="text-red-500">*</span></label>

<input 
    list="jobTitles"
    id="titleField"
    name="title"
    value="<?= htmlspecialchars($job['title']) ?>"
    class="input-field"
    placeholder="Search job title..."
>

<datalist id="jobTitles">
<?php foreach($technical_roles as $role) { ?>
    <option value="<?= $role ?>">
<?php } ?>
</datalist>

<p id="titleErr" class="error"></p>
</div>


<!-- Location -->
<div>
<label>Location <span class="text-red-500">*</span></label>
<select id="locationField" name="location" class="input-field">
<option value="">Select Location</option>
<?php foreach($locationList as $loc): ?>
<option value="<?= $loc ?>" <?= ($job['location']==$loc)?"selected":"" ?>>
<?= $loc ?>
</option>
<?php endforeach; ?>
</select>
<p id="locationErr" class="error"></p>
</div>


<!-- Experience -->
<div>
<label>Experience <span class="text-red-500">*</span></label>
<select id="experience_required" name="experience_required" class="input-field">
<option value="">Select Experience</option>
<option value="0" <?= ($job['experience_required']=="0")?"selected":"" ?>>Fresher</option>
<option value="1" <?= ($job['experience_required']=="1")?"selected":"" ?>>1 Year</option>
<option value="2" <?= ($job['experience_required']=="2")?"selected":"" ?>>2 Years</option>
<option value="3" <?= ($job['experience_required']=="3")?"selected":"" ?>>3+ Years</option>
</select>
<p id="experience_requiredErr" class="error"></p>
</div>


<!-- Job Type -->
<div>
<label>Job Type <span class="text-red-500">*</span></label>
<select id="job_type" name="job_type" class="input-field">
<option value="">Select Job Type</option>
<option value="full-time" <?= ($job['job_type']=="full-time")?"selected":"" ?>>Full Time</option>
<option value="part-time" <?= ($job['job_type']=="part-time")?"selected":"" ?>>Part Time</option>
<option value="internship" <?= ($job['job_type']=="internship")?"selected":"" ?>>Internship</option>
<option value="contract" <?= ($job['job_type']=="contract")?"selected":"" ?>>Contract</option>
</select>
<p id="job_typeErr" class="error"></p>
</div>


<!-- Work Mode -->
<div>
<label>Work Mode <span class="text-red-500">*</span></label>
<select id="work_mode" name="work_mode" class="input-field">
<option value="">Select Work Mode</option>
<option value="remote" <?= ($job['work_mode']=="remote")?"selected":"" ?>>Remote</option>
<option value="onsite" <?= ($job['work_mode']=="onsite")?"selected":"" ?>>Onsite</option>
<option value="hybrid" <?= ($job['work_mode']=="hybrid")?"selected":"" ?>>Hybrid</option>
</select>
<p id="work_modeErr" class="error"></p>
</div>


<!-- Salary -->
<div>
<label>Salary</label>
<input type="text"
name="salary"
value="<?= htmlspecialchars($job['salary']) ?>"
class="input-field">
</div>


<!-- Deadline -->
<div>
<label>Deadline Date <span class="text-red-500">*</span></label>
<input type="date"
id="deadline"
name="deadline"
value="<?= htmlspecialchars($job['deadline']) ?>"
min="<?= date('Y-m-d') ?>"
class="input-field">
<p id="deadlineErr" class="error"></p>
</div>


<!-- Description -->
<div class="md:col-span-2">
<label>Description</label>
<textarea name="description"
rows="4"
class="input-field resize-none"><?= htmlspecialchars($job['description']) ?></textarea>
</div>


<!-- Button -->
<div class="md:col-span-2 mt-6 flex justify-center">
<button class="bg-[#D7AE27] text-black px-8 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
Update Job
</button>
</div>

</form>
</div>
<script>
    
const titleField = document.getElementById("titleField");
const titleErr = document.getElementById("titleErr");

const locationField = document.getElementById("locationField");
const locationErr = document.getElementById("locationErr");

const experienceField = document.getElementById("experience_required");
const experienceErr = document.getElementById("experience_requiredErr");

const jobTypeField = document.getElementById("job_type");
const jobTypeErr = document.getElementById("job_typeErr");

const workModeField = document.getElementById("work_mode");
const workModeErr = document.getElementById("work_modeErr");

const vacancyField = document.getElementById("vacancy");
const vacancyErr = document.getElementById("vacancyErr");

const deadlineField = document.getElementById("deadline");
const deadlineErr = document.getElementById("deadlineErr");

const skillHiddenField = document.getElementById("skillname");
const skillErr = document.getElementById("skillErr");

titleField.addEventListener("input", () =>
    titleErr.textContent = titleField.value.trim() ? "" : "Job title is required"
);

locationField.addEventListener("change", () =>
    locationErr.textContent = locationField.value ? "" : "Location is required"
);

experienceField.addEventListener("change", () =>
    experienceErr.textContent = experienceField.value ? "" : "Experience is required"
);

jobTypeField.addEventListener("change", () =>
    jobTypeErr.textContent = jobTypeField.value ? "" : "Job type is required"
);

workModeField.addEventListener("change", () =>
    workModeErr.textContent = workModeField.value ? "" : "Work mode is required"
);

vacancyField.addEventListener("input", () =>
    vacancyErr.textContent = vacancyField.value.trim() ? "" : "Vacancy is required"
);

deadlineField.addEventListener("change", () =>
    deadlineErr.textContent = deadlineField.value ? "" : "Deadline date is required"
);

function validateSkillLive(){
    skillErr.textContent = skillHiddenField.value.trim() ? "" : "At least one skill is required";
}




const skillsArray = <?= json_encode($skillsList) ?>;

const input = document.getElementById("skillInput");
const suggestionsBox = document.getElementById("suggestions");
const tagsContainer = document.getElementById("skillTags");
const hiddenInput = document.getElementById("skillHidden");

let skills = hiddenInput.value ? hiddenInput.value.split(",") : [];

input.addEventListener("input", function() {
    const value = this.value.toLowerCase();
    suggestionsBox.innerHTML = "";

    if (value === "") {
        suggestionsBox.classList.add("hidden");
        return;
    }

    const filtered = skillsArray.filter(skill =>
        skill.toLowerCase().includes(value) &&
        !skills.includes(skill)
    );

    filtered.forEach(skill => {
        const div = document.createElement("div");
        div.className="p-2 hover:bg-[#D7AE27] hover:text-black cursor-pointer";
        div.innerText = skill;
        div.onclick = () => {
            addSkill(skill);
            input.value="";
            suggestionsBox.classList.add("hidden");
        };
        suggestionsBox.appendChild(div);
    });

    suggestionsBox.classList.remove("hidden");
});

input.addEventListener("keydown", function(e){
    if(e.key === "Enter" && this.value.trim() !== ""){
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
    skills = skills.filter(s => s !== skill);
    renderSkills();
}

function renderSkills(){
    tagsContainer.innerHTML="";

    skills.forEach(skill=>{
        const div = document.createElement("div");
        div.className="bg-[#D7AE27] text-black px-3 py-1 rounded-full flex items-center gap-2";
        div.innerHTML = skill +
        `<button onclick="removeSkill('${skill}')"
        class="font-bold">×</button>`;
        tagsContainer.appendChild(div);
    });

    hiddenInput.value = skills.join(",");
}

renderSkills();
    </script>
<?php include("../include/footer.php"); ?>
</body>
</html>