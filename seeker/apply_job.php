<?php
session_start();
require "../config/db.php";

/* Check login */
if(!isset($_SESSION['uid'])){
    header("Location: login.php");
    exit();
}

/* Check job id */
if(!isset($_GET['jid'])){
    header("Location: find_job.php");
    exit();
}

$jid = intval($_GET['jid']);
$uid = intval($_SESSION['uid']);

$resumeErr = "";
$formError = "";

/* Get seeker */
$sql = "
SELECT users.email, users.contact, job_seeker.sid, job_seeker.sname
FROM users
JOIN job_seeker ON job_seeker.uid = users.uid
WHERE users.uid = $uid
";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) == 0){
    die("Seeker profile not found.");
}

$user = mysqli_fetch_assoc($result);

/* ===============================
   REAPPLY LOGIC (unchanged)
=================================*/
$check = mysqli_query($conn,
"SELECT aid, score FROM application 
 WHERE jid=$jid AND sid=".$user['sid']." 
 ORDER BY aid DESC LIMIT 1");

if(mysqli_num_rows($check) > 0){

    $appData = mysqli_fetch_assoc($check);

    if($appData['score'] >= 5){
        die("You already passed this job test. You cannot apply again.");
    }

    if($appData['score'] < 5){
        header("Location: test.php?aid=".$appData['aid']);
        exit();
    }
}

/* ===============================
   FORM SUBMIT
=================================*/
if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty($_FILES['resume']['name'])){
        $resumeErr = "Resume is required.";
    } else {

        $fileName = $_FILES['resume']['name'];
        $fileTmp  = $_FILES['resume']['tmp_name'];
        $fileSize = $_FILES['resume']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['pdf','doc','docx'];

        if(!in_array($ext,$allowed)){
            $resumeErr = "Only PDF, DOC, DOCX allowed.";
        }
        elseif($fileSize > 2*1024*1024){
            $resumeErr = "File must be under 2MB.";
        }
    }

    if(empty($resumeErr)){

        $newName = time()."_".$fileName;
        move_uploaded_file($fileTmp,"uploads/".$newName);

        /* INSERT STRUCTURE SAME STYLE AS YOUR JOB INSERT */
        $sql = "INSERT INTO application
                (jid,sid,resume,score,status)
                VALUES
                ('$jid','".$user['sid']."','$newName',0,'pending')";

        if(mysqli_query($conn,$sql)){

            $aid = mysqli_insert_id($conn);

            mysqli_query($conn,
            "UPDATE job SET applicant = applicant + 1 WHERE jid=$jid");

            header("Location: test.php?aid=".$aid);
            exit();
        }
        else{
            $formError = "Database error.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Apply Job</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#0f0f0f] text-white min-h-screen flex items-center justify-center">

<div class="w-full max-w-2xl bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800">

<h2 class="text-2xl font-semibold text-yellow-400 mb-6 text-center">
Apply for Job
</h2>

<?php if($formError): ?>
<p class="text-red-400 mb-4"><?= $formError ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="applyForm" class="space-y-5">

<!-- Name -->
<input type="text" value="<?= htmlspecialchars($user['sname']) ?>" readonly
class="w-full p-3 bg-gray-800 rounded border border-gray-700">

<!-- Email -->
<input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly
class="w-full p-3 bg-gray-800 rounded border border-gray-700">

<!-- Contact -->
<input type="text" value="<?= htmlspecialchars($user['contact']) ?>" readonly
class="w-full p-3 bg-gray-800 rounded border border-gray-700">

<!-- Resume -->
<input type="file" name="resume" id="resumeField"
class="w-full p-3 bg-gray-800 rounded border border-gray-700">

<p id="resumeErr" class="text-red-400 text-sm">
<?= $resumeErr ?>
</p>

<button type="submit" id="submitBtn"
class="w-full bg-yellow-400 text-black py-3 rounded-lg font-semibold hover:bg-yellow-500">
Continue to Aptitude Test
</button>

</form>
</div>

<script>

/* ===============================
   LIVE VALIDATION
=================================*/

const resumeField = document.getElementById("resumeField");
const resumeErr = document.getElementById("resumeErr");

resumeField.addEventListener("change", function(){

    resumeErr.textContent = "";

    const file = resumeField.files[0];

    if(!file){
        resumeErr.textContent = "Resume is required.";
        return;
    }

    const allowed = ["pdf","doc","docx"];
    const ext = file.name.split(".").pop().toLowerCase();

    if(!allowed.includes(ext)){
        resumeErr.textContent = "Only PDF, DOC, DOCX allowed.";
        return;
    }

    if(file.size > 2*1024*1024){
        resumeErr.textContent = "File must be under 2MB.";
        return;
    }
});

</script>

</body>
</html>