<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php"; 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ CHECK LOGIN
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];
$csrf_token = generateCSRFToken();
/* ================= FUNCTION: CHECK COLUMN EXISTS ================= */
function columnExists($conn, $table, $column){
    $result = mysqli_query($conn, "
        SELECT COUNT(*) as cnt 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = '$table' 
        AND COLUMN_NAME = '$column'
    ");
    $row = mysqli_fetch_assoc($result);
    return $row['cnt'] > 0;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
   if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("CSRF validation failed");
    }
    try{
        mysqli_begin_transaction($conn);

        // ================= DELETE PROFILE IMAGE =================
        if(columnExists($conn, 'job_seeker', 'profile_image')){
            $res = mysqli_query($conn, "SELECT profile_image FROM job_seeker WHERE uid='$uid'");
            if($row = mysqli_fetch_assoc($res)){
                if(!empty($row['profile_image'])){
                    $file = "uploads/".$row['profile_image'];
                    if(file_exists($file)){
                        unlink($file);
                    }
                }
            }
        }

        // ================= DELETE APPLICATION =================
        if(columnExists($conn, 'application', 'uid')){
            mysqli_query($conn, "DELETE FROM application WHERE uid='$uid'");
        }

        // ================= DELETE SAVED JOB =================
        if(columnExists($conn, 'saved_job', 'uid')){
            mysqli_query($conn, "DELETE FROM saved_job WHERE uid='$uid'");
        }

        // ================= DELETE NOTIFICATIONS =================
        if(columnExists($conn, 'notifications', 'uid')){
            mysqli_query($conn, "DELETE FROM notifications WHERE uid='$uid'");
        }

        // ================= DELETE FRAUD REPORTS =================
        if(columnExists($conn, 'fraud_reports', 'uid')){
            mysqli_query($conn, "DELETE FROM fraud_reports WHERE uid='$uid'");
        }

        // ================= DELETE FEEDBACK =================
        if(columnExists($conn, 'feedback', 'uid')){
            mysqli_query($conn, "DELETE FROM feedback WHERE uid='$uid'");
        }

        // ================= DELETE SEEKER PROFILE =================
        mysqli_query($conn, "DELETE FROM job_seeker WHERE uid='$uid'");

        // ================= FINAL DELETE USER =================
        mysqli_query($conn, "DELETE FROM users WHERE uid='$uid'");

        mysqli_commit($conn);
        regenerateCSRFToken();

        session_destroy();

        echo "<script>
            alert('Account deleted permanently');
            window.location='../auth/login.php';
        </script>";
        exit();

    } catch (Exception $e){

        mysqli_rollback($conn);

        echo "<h3 style='color:red'>Error:</h3>";
        echo "<pre>".$e->getMessage()."</pre>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career craft | Delete Account</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white flex items-center justify-center h-screen">

<div class="bg-[#161616] p-8 rounded-xl text-center border border-gray-700 w-96">

<h2 class="text-xl mb-4 text-red-500">Delete Account Permanently?</h2>

<p class="text-gray-400 mb-6">
This action cannot be undone. All your data will be deleted.
</p>

<form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

<button type="submit"
class="bg-red-600 px-6 py-2 rounded hover:bg-red-700 w-full">
Yes, Delete
</button>
</form>

<br>

<a href="sdashboard.php" class="text-yellow-400">Cancel</a>

</div>

</body>
</html>