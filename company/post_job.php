<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../config/db.php";
require "../array/skill.php";
require "../array/location.php";
require "../array/role.php";
require "../authc/csrf.php";


if(!isset($_SESSION['uid']) || $_SESSION['role']!='company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

/* Get company id of logged-in user */
$companyQuery = mysqli_query($conn, "SELECT cid FROM company WHERE uid = $uid");

if(mysqli_num_rows($companyQuery) == 0){
    die("Company not found. Please complete company profile.");
}

$companyData = mysqli_fetch_assoc($companyQuery);
$cid = $companyData['cid'];
$title = $description = $location = $salary = $salary_type = "";
$experience_required = $job_type = $work_mode = $deadline = "";
$skillname = $vacancy = "";

$titleErr = $locationErr = $experienceErr = "";
$jobTypeErr = $workModeErr = $deadlineErr = "";
$skillErr = $vacancyErr = "";

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
 if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $salary = trim($_POST["salary"] ?? "");
    $salary_type = $_POST["salary_type"] ?? "";
    $experience_required = $_POST["experience_required"] ?? "";
    $job_type = $_POST["job_type"] ?? "";
    $work_mode = $_POST["work_mode"] ?? "";
    $deadline = $_POST["deadline"] ?? "";
    $skillname = trim($_POST["skillname"] ?? "");
    $vacancy = $_POST["vacancy"] ?? "";
$status = (strtotime($deadline) < strtotime(date("Y-m-d"))) ? "closed" : "open";
    if ($title === "") $titleErr = "Job title is required";
    if ($location === "") $locationErr = "Location is required";
    if ($experience_required === "") $experienceErr = "Experience is required";
    if ($job_type === "") $jobTypeErr = "Job type is required";
    if ($work_mode === "") $workModeErr = "Work mode is required";
    if ($deadline === "") $deadlineErr = "Deadline date is required";
    if ($skillname === "") $skillErr = "At least one skill is required";
    if ($vacancy === "") $vacancyErr = "Vacancy is required";

    if (
        $titleErr=="" && $locationErr=="" && $experienceErr=="" &&
        $jobTypeErr=="" && $workModeErr=="" && $deadlineErr=="" &&
        $skillErr=="" && $vacancyErr==""
    ) {

$sql = "INSERT INTO job
(uid, cid, title, description, location, salary, salary_type,
experience_required, skillname, job_type, work_mode, deadline,
status, vacancy, applicant, is_approve)
VALUES (
'$uid', '$cid', '$title', '$description', '$location', '$salary',
'$salary_type', '$experience_required', '$skillname',
'$job_type', '$work_mode', '$deadline',
'$status', '$vacancy', 0, 'pending'
)";
        if(mysqli_query($conn,$sql)){
            $_SESSION['post_success'] = "Job posted successfully!";
            $title = $description = $location = $salary = "";
            $experience_required = $job_type = $work_mode = $deadline = "";
            $skillname = $vacancy = "";
            regenerateCSRFToken();

            header("Location: cdashboard.php");
            exit;
    }
        }
       

}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Career Craft | Post job</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
     <style>
            /* Make calendar icon white */
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                cursor: pointer;
            }
            </style>
</head>

<body class="bg-black px-4 py-6 overflow-x-hidden overflow-y-auto">
    <?php include("../include/navbar.php");?>
<a href="cdashboard.php"
   class="inline-block mt-20 mb-4 text-yellow-400 text-sm hover:underline">
   ← Back to Dashboard
</a>


<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl 
p-6 sm:p-8 border border-white/10 text-white  mb-10">

<h2 class="text-2xl md:text-3xl font-bold text-[#D7AE27] mb-6 text-center">
Post New Job
</h2>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5" novalidate>
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<!-- Job Title -->
<div>
<label>Job Title <span class="text-red-500">*</span></label>

<input 
    list="jobTitles"
    id="titleField"
    name="title"
    value="<?= htmlspecialchars($title) ?>"
    class="input-field"
    placeholder="Search job title..."
>

<datalist id="jobTitles">
<?php foreach($technical_roles as $role) { ?>
    <option value="<?= $role ?>">
<?php } ?>
</datalist>

<p id="titleErr" class="error"><?= $titleErr ?></p>
</div>
<!-- Location -->
<div>
<label>Location <span class="text-red-500">*</span></label>
<select id="locationField" name="location" class="input-field">
<option value="">Select Location</option>
<?php foreach($locationList as $loc): ?>
<option value="<?= $loc ?>" <?= ($location==$loc)?"selected":"" ?>>
<?= $loc ?>
</option>
<?php endforeach; ?>
</select>
<p id="locationErr" class="error"><?= $locationErr ?></p>
</div>

<!-- Experience -->
<div>
<label>Experience <span class="text-red-500">*</span></label>
<select id="experience_required" name="experience_required" class="input-field">
<option value="">Select Experience</option>
<option value="0">Fresher</option>
<option value="1">1 Year</option>
<option value="2">2 Years</option>
<option value="3">3+ Years</option>
</select>
<p id="experience_requiredErr" class="error"><?= $experienceErr ?></p>
</div>

<!-- Job Type -->
<div>
<label>Job Type <span class="text-red-500">*</span></label>
<select id="job_type" name="job_type" class="input-field">
<option value="">Select Job Type</option>
<option value="full-time">Full Time</option>
<option value="part-time">Part Time</option>
<option value="internship">Internship</option>
<option value="contract">Contract</option>
</select>
<p id="job_typeErr" class="error"><?= $jobTypeErr ?></p>
</div>

<!-- Work Mode -->
<div>
<label>Work Mode <span class="text-red-500">*</span></label>
<select id="work_mode" name="work_mode" class="input-field">
<option value="">Select Work Mode</option>
<option value="remote">Remote</option>
<option value="onsite">Onsite</option>
<option value="hybrid">Hybrid</option>
</select>
<p id="work_modeErr" class="error"><?= $workModeErr ?></p>
</div>

<!-- Vacancy -->
<div>
<label>Vacancy <span class="text-red-500">*</span></label>
<input type="number" id="vacancy" name="vacancy"
value="<?= htmlspecialchars($vacancy) ?>"
class="input-field">
<p id="vacancyErr" class="error"><?= $vacancyErr ?></p>
</div>

<!-- Deadline -->
<div>
    <label>Deadline Date <span class="text-red-500">*</span></label>
    <input type="date" 
           id="deadline" 
           name="deadline"
           value="<?= htmlspecialchars($deadline ?? '') ?>"
           min="<?= date('Y-m-d') ?>" 
           class="input-field">
           
    <p id="deadlineErr" class="error"><?= $deadlineErr ?? '' ?></p>
</div>

<!-- Skills -->
<div class="md:col-span-2 relative">
<label class="block mb-2">Skills <span class="text-red-500">*</span></label>

<input type="text" id="skillInput"
placeholder="Type skill and press Enter"
class="input-field">

<div id="suggestions"
class="absolute bg-black border border-white/20 w-full mt-1 rounded-md hidden max-h-40 overflow-y-auto z-10">
</div>

<div id="skillTags" class="flex flex-wrap gap-2 mt-3"></div>

<input type="hidden" name="skillname" id="skillHidden"
value="<?= htmlspecialchars($skillname ?? '') ?>">

<p class="error"><?= $skillErr ?></p>
</div>

<!-- Salary -->
<div>
<label>Salary</label>
<input name="salary" class="input-field">
</div>

<div>
<label>Salary Type</label>
<select name="salary_type" class="input-field">
<option value="">Select</option>
<option value="fixed">Fixed</option>
<option value="range">Range</option>
<option value="negotiable">Negotiable</option>
</select>
</div>

<!-- Description -->
<div class="md:col-span-2">
<label>Description</label>
<textarea name="description" rows="4"
class="input-field resize-none"><?= htmlspecialchars($description) ?></textarea>
</div>

<div class="md:col-span-2 mt-6 flex justify-center">
<button class="bg-[#D7AE27] text-black px-8 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
Post Job
</button>
</div>

</form>
</div>

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



<script>
// ================= LIVE VALIDATION =================

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

<?php include("../include/footer.php");?>

</body>
</html>