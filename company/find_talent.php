<?php
session_start();
    require "../config/db.php";
    require "../authc/csrf.php";
    require "../array/skill.php";

require "../auth/session_check.php";

    $csrf_token = generateCSRFToken();
 
$uid = $_SESSION['uid'] ?? 0;



// get company id
$cid_query = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$uid'");
$cid_row = mysqli_fetch_assoc($cid_query);
$cid = $cid_row['cid'] ?? 0;

/* ================= FILTER VALUES ================= */
$skill_filter = $_GET['skill'] ?? '';
$experience_filter = $_GET['experience'] ?? '';

$where = "WHERE 1=1";

// Skill filter
if(!empty($skill_filter)){
    $skills = explode(",", $skill_filter);

    foreach($skills as $skill){
        $skill = mysqli_real_escape_string($conn, trim($skill));
        $where .= " AND js.skillname LIKE '%$skill%'";
    }
}

// Experience filter
if($experience_filter !== ""){

    if($experience_filter == "Fresher"){
        $where .= " AND js.experience = 'Fresher'";
    }
    elseif($experience_filter == "1"){
        $where .= " AND js.experience LIKE '%1%'";
    }
    elseif($experience_filter == "2"){
        $where .= " AND js.experience LIKE '%2%'";
    }
    elseif($experience_filter == "3"){
        $where .= " AND (
            js.experience LIKE '%3%' 
            OR js.experience LIKE '%4%' 
            OR js.experience LIKE '%5%'
        )";
    }
}

// fetch seekers + saved status.
$sql = "SELECT 
    js.sid,
    js.sname,
    js.education,
    js.experience,
    js.skillname,
    js.bio,
    js.profile_image,
    u.created_at,

    EXISTS(
        SELECT 1 FROM saved_candidate 
        WHERE saved_candidate.sid = js.sid 
        AND saved_candidate.cid = '$cid'
    ) AS saved

FROM job_seeker js
JOIN users u ON js.uid = u.uid
$where
ORDER BY u.created_at DESC";

    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft |Applicants</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
    <body class="bg-black text-white">
    <?php include("../include/navbar.php"); ?>

<a href="http://localhost/php_program/project/home.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
    <div class="max-w-7xl mx-auto px-6 py-10">

    
        <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27]  mb-16 text-center">
            Find
            <span class="relative inline-block text-white">
                Talent
                <span class="absolute left-0 top-full mt-6 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
            </span>
        </h2>


        <!-- ================= FILTER FORM ================= -->
<form method="GET" class="mb-8">
<div class="flex flex-wrap items-center gap-3 bg-[#161616] p-4 rounded-2xl border border-gray-800">

<!-- Multi Skill Search -->
<div class="relative flex items-center flex-wrap gap-2 bg-[#0f0f0f] border border-gray-700 px-3 py-2 rounded-lg flex-1 min-w-[220px]">

    <i class="fa-solid fa-code text-yellow-400 text-sm"></i>

    <!-- Selected Skills -->
    <div id="selectedSkills" class="flex flex-wrap gap-2"></div>

    <!-- Input -->
    <input type="text" id="skillInput"
    placeholder="Skill"
    autocomplete="off"
    class="bg-transparent outline-none text-sm flex-1 text-white placeholder-gray-400">

    <!-- Hidden input (important) -->
    <input type="hidden" name="skill" id="skillHidden"
    value="<?php echo htmlspecialchars($skill_filter); ?>">

    <!-- Dropdown -->
    <div id="skillDropdown" 
    class="absolute left-0 top-full mt-2 w-full bg-[#161616] border border-gray-700 rounded-lg shadow-lg hidden max-h-40 overflow-y-auto z-50">
    </div>
</div>

   <!-- Experience -->
<div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
    <i class="fa-solid fa-briefcase text-yellow-400 text-sm"></i>

    <select name="experience" onchange="this.form.submit()" 
    class="bg-[#0f0f0f] text-white outline-none w-full text-sm appearance-none">

        <option value="">Experience</option>

        <option value="Fresher" <?php if($experience_filter=='Fresher') echo 'selected'; ?>>
            Fresher
        </option>

        <option value="1" <?php if($experience_filter=='1') echo 'selected'; ?>>
            1 Year
        </option>

        <option value="2" <?php if($experience_filter=='2') echo 'selected'; ?>>
            2 Year
        </option>

        <option value="3" <?php if($experience_filter=='3') echo 'selected'; ?>>
            3+ Year
        </option>

    </select>
</div>
   

    <!-- Clear -->
    <a href="find_talent.php" 
    class="bg-yellow-400 text-black px-4 h-10 flex items-center justify-center rounded-lg text-sm font-semibold hover:bg-yellow-500">
        Clear
    </a>

</div>
</form>





<?php if(mysqli_num_rows($result) == 0){ ?>
<div class="flex flex-col items-center justify-center py-20 w-full">
    <div class="text-6xl mb-4">&#x1F614;</div>
    <p class="text-2xl text-[#D7AE27] font-semibold text-center">
        No Seeker Found
    </p>
</div>
<?php } ?>



    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

    <?php while($row = mysqli_fetch_assoc($result)) { 

    $img = !empty($row['profile_image']) 
            ? "../seeker/uploads/".$row['profile_image'] 
            : "https://via.placeholder.com/100";

    $date = date("d M Y", strtotime($row['created_at']));
    ?>

    <!-- CARD -->
    <!-- CARD -->
<?php $saved = !empty($row['saved']); ?>

    <div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">


    <!-- SAVE ICON (NOW INSIDE CARD ✅) -->
<form method="POST" action="toggle_save_candidate.php" class="absolute top-4 right-4">
    <input type="hidden" name="sid" value="<?= $row['sid'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <button type="submit" class="text-xl bg-transparent border-0">
        <i class="<?= $saved 
            ? 'fa-solid fa-bookmark text-yellow-400'
            : 'fa-regular fa-bookmark text-white hover:text-yellow-400' ?>">
        </i>
    </button>
</form>  

    <!-- TOP -->
    <div class="flex items-center gap-4">

        <img src="<?= $img ?>" 
            class="w-16 h-16 rounded-xl object-cover bg-white p-1">

        <div>
            <h3 class="text-lg font-semibold"><?= $row['sname'] ?></h3>
            <p class="text-gray-400 text-sm"><?= $row['education'] ?></p>
            <p class="text-gray-500 text-xs mt-1">Joined: <?= $date ?></p>
        </div>

    </div>

    <!-- TAGS -->
    <div class="flex flex-wrap gap-2 mt-4 text-xs">

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['experience'] ?>
        </span>

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['skillname'] ?>
        </span>

    </div>

    <!-- BIO -->
    <p class="text-gray-400 text-sm mt-4 leading-relaxed">
        <?= !empty($row['bio']) ? substr($row['bio'], 0, 80)."..." : "No description available" ?>
    </p>

    <!-- STATUS -->
    <div class="flex justify-between items-center mt-5">

        <span class="text-green-400 text-sm font-medium">
            ● Available for Hiring
        </span>

        <span class="text-gray-500 text-xs">
            Active
        </span>

    </div>

    <!-- BUTTON -->
    <a href="seeker_details.php?sid=<?= $row['sid'] ?>" 
    class="block mt-5 bg-[#D7AE27] text-black text-center py-2 rounded-xl 
            font-semibold hover:bg-yellow-500 transition">
        View Profile
    </a>

</div>

    <?php } ?>

    </div>

    </div>

    <script>
const skills = <?php echo json_encode($skillsList); ?>;

const input = document.getElementById("skillInput");
const dropdown = document.getElementById("skillDropdown");
const selectedDiv = document.getElementById("selectedSkills");
const hiddenInput = document.getElementById("skillHidden");

let selected = hiddenInput.value ? hiddenInput.value.split(",") : [];

// render chips
function renderSkills(){
    selectedDiv.innerHTML = "";
    selected.forEach(skill => {
        const chip = document.createElement("div");
        chip.className = "bg-yellow-400 text-black px-2 py-1 rounded text-xs flex items-center gap-1";

        chip.innerHTML = `
            ${skill}
            <span class="cursor-pointer">&times;</span>
        `;

        chip.querySelector("span").onclick = () => {
            selected = selected.filter(s => s !== skill);
            updateHidden();
            renderSkills();
            input.form.submit();
        };

        selectedDiv.appendChild(chip);
    });
}

// update hidden input
function updateHidden(){
    hiddenInput.value = selected.join(",");
}

// search input
input.addEventListener("input", function(){
    const value = this.value.toLowerCase();
    dropdown.innerHTML = "";

    if(value === ""){
        dropdown.classList.add("hidden");
        return;
    }

    const filtered = skills.filter(skill => 
        skill.toLowerCase().includes(value) && !selected.includes(skill)
    );

    filtered.forEach(skill => {
        const div = document.createElement("div");
        div.textContent = skill;
        div.className = "px-3 py-2 cursor-pointer hover:bg-yellow-400 hover:text-black text-sm";

        div.onclick = () => {
            selected.push(skill);
            updateHidden();
            renderSkills();
            dropdown.classList.add("hidden");
            input.value = "";
            input.form.submit(); // auto filter
        };

        dropdown.appendChild(div);
    });

    dropdown.classList.remove("hidden");
});

// hide dropdown
document.addEventListener("click", function(e){
    if(!input.contains(e.target) && !dropdown.contains(e.target)){
        dropdown.classList.add("hidden");
    }
});

// initial render
renderSkills();
</script>
<?php include("../include/footer.php");?>

    </body>
</html>