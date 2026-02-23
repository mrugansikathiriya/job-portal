<?php
session_start();
require "../config/db.php";

// Get seekers whose users are active
$result = mysqli_query($conn, "
    SELECT job_seeker.*, users.uname, users.email, users.status 
    FROM job_seeker
    INNER JOIN users ON job_seeker.uid = users.uid
    WHERE users.status = 'active'
    ORDER BY job_seeker.sid DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Manage Job Seekers</title>

    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black/90 text-white min-h-screen p-8">

<div class="flex items-center gap-3 mb-6">
    <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
    <span class="text-xl font-bold text-[#D7AE27]">
        CareerCraft
    </span>
</div>

<div class="max-w-7xl mx-auto">

<h1 class="text-3xl font-bold mb-8 text-[#D7AE27] text-center">
    Manage Job Seekers
</h1>

<div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

<table class="w-full text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
    <th class="p-3">Seeker ID</th>
    <th class="p-3">User Name</th>
    <th class="p-3">Email</th>
    <th class="p-3">Full Name</th>
    <th class="p-3">Education</th>
    <th class="p-3">Experience</th>
    <th class="p-3">Skills</th>
    <th class="p-3">Bio</th>
    <th class="p-3">Birthdate</th>
    <th class="p-3 text-center">Actions</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?php echo $row['sid']; ?></td>
    <td class="p-3"><?php echo $row['uname']; ?></td>
    <td class="p-3"><?php echo $row['email']; ?></td>
    <td class="p-3"><?php echo $row['sname']; ?></td>
    <td class="p-3"><?php echo $row['education']; ?></td>
    <td class="p-3"><?php echo $row['experience']; ?></td>
    <td class="p-3"><?php echo $row['skillname']; ?></td>
    <td class="p-3"><?php echo $row['bio']; ?></td>
    <td class="p-3"><?php echo $row['birthdate']; ?></td>

    <td class="p-3 text-center">
        <a href="delete_seeker.php?sid=<?php echo $row['sid']; ?>"
           onclick="return confirm('Are you sure?')"
           class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 text-sm">
           Delete
        </a>
    </td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

<div class="mt-8">
    <a href="admin_dashboard.php"
       class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
       Back to Dashboard
    </a>
</div>

</div>

</body>
</html>