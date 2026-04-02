<?php
require "../config/db.php";
require "admin_auth.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}
// FETCH APPLICATIONS
$result = mysqli_query($conn, "
    SELECT 
        a.*, 
        u.uname, u.email,
        j.title, j.location
    FROM application a
    LEFT JOIN users u ON a.uid = u.uid
    LEFT JOIN job j ON a.jid = j.jid
    ORDER BY a.aid DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Applications</title>

    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black/90 text-white min-h-screen p-8">

<!-- HEADER -->
<div class="flex items-center gap-3 mb-6">
    <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
    <span class="text-xl font-bold text-[#D7AE27]">
        CareerCraft
    </span>
</div>

<div class="max-w-7xl mx-auto">

<h1 class="text-3xl font-bold mb-8 text-[#D7AE27] text-center">
    All Job Applications
</h1>

<div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

<table class="w-full text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
    <th class="p-3">App ID</th>
    <th class="p-3">Seeker</th>
    <th class="p-3">Email</th>
    <th class="p-3">Job</th>
    <th class="p-3">Location</th>
    <th class="p-3">Resume</th>
    <th class="p-3">Score</th>
    <th class="p-3">Status</th>
    <th class="p-3">Interview</th>
    <th class="p-3">Applied</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?php echo $row['aid']; ?></td>

    <td class="p-3"><?php echo $row['uname']; ?></td>

    <td class="p-3"><?php echo $row['email']; ?></td>

    <td class="p-3"><?php echo $row['title']; ?></td>

    <td class="p-3"><?php echo $row['location']; ?></td>

    <!-- RESUME -->
    <td class="p-3">
        <?php if($row['resume']) { ?>
            <a href="../seeker/uploads/<?php echo $row['resume']; ?>" 
               target="_blank"
               class="text-blue-400 hover:underline">
               View
            </a>
        <?php } else {
            echo "No File";
        } ?>
    </td>

    <!-- SCORE -->
    <td class="p-3">
        <?php echo $row['score'] ? $row['score'] : "-"; ?>
    </td>

    <!-- STATUS -->
    <td class="p-3">
        <?php 
        $status = $row['status'];

        if($status == 'applied'){
            echo "<span class='bg-blue-500 px-3 py-1 rounded text-sm'>Applied</span>";
        } elseif($status == 'shortlisted'){
            echo "<span class='bg-yellow-500 px-3 py-1 rounded text-sm text-black'>Shortlisted</span>";
        } elseif($status == 'interview'){
            echo "<span class='bg-purple-500 px-3 py-1 rounded text-sm'>Interview</span>";
        } elseif($status == 'offered'){
            echo "<span class='bg-green-500 px-3 py-1 rounded text-sm'>Offered</span>";
        } elseif($status == 'rejected'){
            echo "<span class='bg-red-500 px-3 py-1 rounded text-sm'>Rejected</span>";
        } else {
            echo "<span class='bg-gray-500 px-3 py-1 rounded text-sm'>$status</span>";
        }
        ?>
    </td>

    <!-- INTERVIEW -->
    <td class="p-3">
        <?php if($row['interview_date']) { ?>
            <?php echo $row['interview_date']; ?><br>
            <small><?php echo $row['interview_time']; ?></small>
        <?php } else {
            echo "Not Scheduled";
        } ?>
    </td>

    <!-- DATE -->
    <td class="p-3">
        <?php echo date("d M Y", strtotime($row['applied_at'])); ?>
    </td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

<!-- BACK BUTTON -->
<div class="mt-8">
    <a href="admin_dashboard.php"
       class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
       Back to Dashboard
    </a>
</div>

</div>

</body>
</html>