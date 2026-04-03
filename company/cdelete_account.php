<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php"; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ ONLY COMPANY ACCESS
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];
$csrf_token = generateCSRFToken();

/* 🔥 FUNCTION: CHECK COLUMN EXISTS */
function columnExists($conn, $table, $column){
    $sql = "SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = '$table' 
            AND COLUMN_NAME = '$column'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    return $row['cnt'] > 0;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
 if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("CSRF validation failed");
    }
    try{
        mysqli_begin_transaction($conn);

        // ================= DELETE COMPANY LOGO =================
        if(columnExists($conn, 'company', 'logo')){
            $res = mysqli_query($conn, "SELECT logo FROM company WHERE uid='$uid'");
            if($row = mysqli_fetch_assoc($res)){
                if(!empty($row['logo'])){
                    $file = "../company/uploads/".$row['logo'];
                    if(file_exists($file)){
                        unlink($file);
                    }
                }
            }
        }

        // ================= GET JOB IDS =================
        $job_ids = [];
        if(columnExists($conn, 'job', 'uid')){
            $jobs = mysqli_query($conn, "SELECT jid FROM job WHERE uid='$uid'");
            while($row = mysqli_fetch_assoc($jobs)){
                $job_ids[] = $row['jid'];
            }
        }

        // ================= DELETE APPLICATIONS =================
        if(!empty($job_ids)){
            foreach($job_ids as $jid){
                mysqli_query($conn, "DELETE FROM application WHERE jid='$jid'");
            }
        }

        // ================= DELETE RELATED TABLES =================

        if(columnExists($conn, 'saved_candidate', 'cid')){
            mysqli_query($conn, "DELETE FROM saved_candidate WHERE cid='$uid'");
        }

        if(columnExists($conn, 'saved_job', 'uid')){
            mysqli_query($conn, "DELETE FROM saved_job WHERE uid='$uid'");
        }

        if(columnExists($conn, 'notifications', 'uid')){
            mysqli_query($conn, "DELETE FROM notifications WHERE uid='$uid'");
        }

        if(columnExists($conn, 'fraud_reports', 'uid')){
            mysqli_query($conn, "DELETE FROM fraud_reports WHERE uid='$uid'");
        }

        if(columnExists($conn, 'feedback', 'uid')){
            mysqli_query($conn, "DELETE FROM feedback WHERE uid='$uid'");
        }

        if(columnExists($conn, 'job_seeker', 'uid')){
            mysqli_query($conn, "DELETE FROM job_seeker WHERE uid='$uid'");
        }

        // ================= DELETE JOBS =================
        if(columnExists($conn, 'job', 'uid')){
            mysqli_query($conn, "DELETE FROM job WHERE uid='$uid'");
        }

        // ================= DELETE COMPANY =================
// ================= DELETE COMPANY =================
$res = mysqli_query($conn, "SELECT cname FROM company WHERE uid='$uid'");
$cname = '';
if($row = mysqli_fetch_assoc($res)){
    $cname = $row['cname']; // store name before deletion
}

mysqli_query($conn, "DELETE FROM company WHERE uid='$uid'");

// ================= INSERT NOTIFICATION FOR ALL SEEKERS =================
if(!empty($cname)){
    $seekers = mysqli_query($conn, "SELECT uid FROM users WHERE role='seeker'");
    $message = $cname." is not available !! Company profile Deleted... ";

    while($row = mysqli_fetch_assoc($seekers)){
        $seeker_uid = $row['uid'];
        mysqli_query($conn, "
            INSERT INTO notifications (uid, message, is_read)
            VALUES ('$seeker_uid', '$message', 0)
        ");
    }
}
        // ================= FINAL DELETE USER =================
        mysqli_query($conn, "DELETE FROM users WHERE uid='$uid'");

        mysqli_commit($conn);
        regenerateCSRFToken();

        session_destroy();

        echo "<script>
            alert('Company account deleted permanently');
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
<title>Career craft |Delete Account</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="icon" href="../image/logo3.jpg" type="image/png"></head>

<body class="bg-black text-white flex items-center justify-center h-screen">

<div class="bg-[#161616] p-8 rounded-xl text-center border border-gray-700 w-96">

<h2 class="text-xl mb-4 text-red-500">Delete Account Permanently?</h2>

<p class="text-gray-400 mb-6">
This action will permanently delete your company, jobs, and all related data.
</p>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

    <button type="submit"
    class="bg-red-600 px-6 py-2 rounded hover:bg-red-700 w-full">
    Yes, Delete
    </button>
</form>

<br>

<a href="cdashboard.php" class="text-yellow-400">Cancel</a>

</div>

</body>
</html>