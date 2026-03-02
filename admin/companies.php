<?php
require "../config/db.php";
require "admin_auth.php";

// Get companies whose users are active
$result = mysqli_query($conn, "
    SELECT company.*, users.uname, users.email, users.status 
    FROM company
    INNER JOIN users ON company.uid = users.uid
    WHERE users.status = 'active'
    ORDER BY company.cid DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Manage Companies</title>

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
    Manage Companies
</h1>

<div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

<table class="w-full text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
    <th class="p-3">Company ID</th>
    <th class="p-3">User Name</th>
    <th class="p-3">Email</th>
    <th class="p-3">Company Name</th>
    <th class="p-3">Logo</th>
    <th class="p-3">Website</th>
    <th class="p-3">Location</th>
    <th class="p-3">Established</th>
    <th class="p-3">Verified</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?php echo $row['cid']; ?></td>
    <td class="p-3"><?php echo $row['uname']; ?></td>
    <td class="p-3"><?php echo $row['email']; ?></td>
    <td class="p-3"><?php echo $row['cname']; ?></td>

   <td class="p-3">
    <?php if($row['logo']) { ?>
  
            <?php echo $row['logo']; ?>
    <?php } else { 
        echo "No Logo"; 
    } ?>
</td>

<td class="p-3">
    <?php if($row['website']) { ?>
        <a href="<?php echo $row['website']; ?>" 
           target="_blank"
           class="text-blue-400 hover:underline">
            <?php echo $row['website']; ?>
        </a>
    <?php } else {
        echo "No Website";
    } ?>
</td>

    <td class="p-3"><?php echo $row['location']; ?></td>
    <td class="p-3"><?php echo $row['established_at']; ?></td>

    <td class="p-3">
        <?php if($row['is_verified'] == 1) { ?>
            <span class="bg-green-500 px-3 py-1 rounded text-sm">Verified</span>
        <?php } else { ?>
            <a href="verify_company.php?cid=<?php echo $row['cid']; ?>"
               class="bg-yellow-500 px-3 py-1 rounded text-sm hover:bg-yellow-600 text-black">
               Verify
            </a>
        <?php } ?>
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