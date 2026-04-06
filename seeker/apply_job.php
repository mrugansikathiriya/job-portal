<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

/* Check login */
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
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
   PREVENT DUPLICATE APPLICATION
=================================*/
$check = mysqli_query($conn,
"SELECT aid, score, status FROM application 
 WHERE jid=$jid AND sid=".$user['sid']." 
 ORDER BY aid DESC
 LIMIT 1");

if(mysqli_num_rows($check) > 0){

    $appData = mysqli_fetch_assoc($check);

    if($appData['status'] != 'withdrawn'){

        if($appData['score'] == 0){
            header("Location: test.php?aid=".$appData['aid']);
            exit();
        }

        die("You have already applied for this job.");
    }
}

/* ===============================
   FORM SUBMIT
=================================*/
if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    if(empty($_FILES['resume']['name'])){
        $resumeErr = "Resume is required.";
    } else {

        $fileName = basename($_FILES['resume']['name']);
        $newName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);

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

    /* ✅ FIXED CONDITION */
    if(empty($resumeErr)){

        if(move_uploaded_file($fileTmp,"uploads/".$newName)){

            /* check withdrawn */
            $old = mysqli_query($conn,"
                SELECT aid FROM application 
                WHERE jid=$jid AND sid=".$user['sid']." 
                AND status='withdrawn'
                LIMIT 1
            ");

            if(mysqli_num_rows($old) > 0){

                $oldData = mysqli_fetch_assoc($old);
                $aid = $oldData['aid'];

                $update = mysqli_query($conn,"
                    UPDATE application SET 
                        resume='$newName',
                        status='pending',
                        score=0
                    WHERE aid=$aid
                ");

                if($update){

                    /* notification */
                    $jobRes = mysqli_query($conn, "SELECT uid, title FROM job WHERE jid=$jid");
                    $jobData = mysqli_fetch_assoc($jobRes);

                    $message = "New application from " . $user['sname'] . " for job: " . $jobData['title'];

                    mysqli_query($conn, "
                        INSERT INTO notifications (uid, message, is_read)
                        VALUES ('".$jobData['uid']."', '$message', 0)
                    ");

                    regenerateCSRFToken();
                    header("Location: test.php?aid=".$aid);
                    exit();
                } else {
                    $formError = "Database error.";
                }

            } else {

                $insert = mysqli_query($conn,"
                    INSERT INTO application
                    (uid,jid,sid,resume,status,score)
                    VALUES
                    ('$uid','$jid','".$user['sid']."','$newName','pending',0)
                ");

                if($insert){

                    $aid = mysqli_insert_id($conn);

                    mysqli_query($conn,
                        "UPDATE job SET applicant = applicant + 1 WHERE jid=$jid"
                    );

                    /* notification */
                    $jobRes = mysqli_query($conn, "SELECT uid, title FROM job WHERE jid=$jid");
                    $jobData = mysqli_fetch_assoc($jobRes);

                    $message = "New application from " . $user['sname'] . " for job: " . $jobData['title'];

                    mysqli_query($conn, "
                        INSERT INTO notifications (uid, message, is_read)
                        VALUES ('".$jobData['uid']."', '$message', 0)
                    ");

                    regenerateCSRFToken();
                    header("Location: test.php?aid=".$aid);
                    exit();

                } else {
                    $formError = "Database error.";
                }
            }

        } else {
            $formError = "File upload failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Apply Job</title>
<link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">

<?php include("../include/navbar.php"); ?>

<div class="max-w-6xl mx-auto px-4 py-10 mt-18">

<!-- Back Button -->
<div class="mb-6">

<a href="job_details.php?jid=<?= $jid ?>"
class="inline-flex items-center text-[#D7AE27] hover:text-yellow-400 font-medium transition">
<i class="fa-solid fa-arrow-left mr-2"></i>
Back
</a>
</div>

<!-- Apply Job Card -->
<div class="bg-[#0f0f0f] border border-white/10 rounded-2xl shadow-xl p-8">

<h2 class="text-3xl font-bold text-[#D7AE27] mb-8 text-center">
Apply for Job
</h2>

<?php if($formError): ?>
<p class="text-red-400 mb-6 text-center"><?= $formError ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="applyForm" class="space-y-6">

<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<!-- Name -->
<div>
<label class="block mb-2 text-sm font-semibold text-[#D7AE27]">
Full Name
</label>

<input type="text"
value="<?= htmlspecialchars($user['sname']) ?>" readonly
class="w-full p-3 bg-[#1a1a1a] rounded-lg border border-gray-700 
focus:outline-none focus:border-[#D7AE27]">
</div>

<!-- Email -->
<div>
<label class="block mb-2 text-sm font-semibold text-[#D7AE27]">
Email Address
</label>

<input type="email"
value="<?= htmlspecialchars($user['email']) ?>" readonly
class="w-full p-3 bg-[#1a1a1a] rounded-lg border border-gray-700 
focus:outline-none focus:border-[#D7AE27]">
</div>

<!-- Contact -->
<div>
<label class="block mb-2 text-sm font-semibold text-[#D7AE27]">
Contact Number
</label>

<input type="text"
value="<?= htmlspecialchars($user['contact']) ?>" readonly
class="w-full p-3 bg-[#1a1a1a] rounded-lg border border-gray-700 
focus:outline-none focus:border-[#D7AE27]">
</div>

<!-- Resume Upload -->
<div>
<label class="block mb-2 text-sm font-semibold text-[#D7AE27]">
Upload Resume (PDF / DOC / DOCX)
</label>

<input type="file" name="resume" id="resumeField"
class="w-full p-3 bg-[#1a1a1a] rounded-lg border border-gray-700 text-gray-300
file:bg-[#D7AE27] file:text-black file:px-4 file:py-2 file:border-0 file:rounded-lg
hover:file:bg-yellow-500 transition">

<p id="resumeErr" class="text-red-400 text-sm mt-2">
<?= $resumeErr ?>
</p>
</div>

<!-- Submit Button -->
<button type="submit"
class="w-full bg-[#D7AE27] text-black py-3 rounded-xl font-bold text-lg
hover:bg-yellow-500 transition duration-300 transform hover:scale-[1.03]">
Continue to Aptitude Test
</button>

</form>

</div>

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
<?php include("../include/footer.php"); ?>

</html>