<?php
session_start();
require "../config/db.php";

// // Check admin login (optional)
// if(!isset($_SESSION['aid'])){
//     header("Location: login.php");
//     exit();
// }

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY uid DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Manage Users</title>
 <link href="../dist/styles.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">

<body class="bg-black/90 text-white min-h-screen p-8">
 <div class="flex items-center gap-3">
        <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
        <span class="text-xl font-bold text-[#D7AE27]">
            CareerCraft
        </span>
    </div>
<div class="max-w-7xl mx-auto">

   <h1 class="text-3xl font-bold mb-8 text-[#D7AE27] text-center">
    Manage Users
</h1>

    <div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

        <table class="w-full text-left">

            <thead class="bg-[#D7AE27] text-black">
                <tr>
                    <th class="p-3">UserID</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Contact</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Created</th>
                    <th class="p-3">Profile completed</th>

                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = mysqli_fetch_assoc($result)) { ?>

                <tr class="border-b border-gray-700 hover:bg-black/50 transition">
                    <td class="p-3"><?php echo $row['uid']; ?></td>
                    <td class="p-3"><?php echo $row['uname']; ?></td>
                    <td class="p-3"><?php echo $row['email']; ?></td>
                    <td class="p-3 capitalize"><?php echo $row['role']; ?></td>
                    <td class="p-3"><?php echo $row['contact']; ?></td>

                    <td class="p-3">
                       <!-- Block / Unblock -->
    <?php if($row['status'] == 'active') { ?>
        <a href="blocked_user.php?uid=<?php echo $row['uid']; ?>"
           class="bg-green-500 px-3 py-1 rounded text-sm hover:bg-blue-600 text-white">
           Unblocked
        </a>
    <?php } else { ?>
        <a href="unblocked_user.php?uid=<?php echo $row['uid']; ?>"
           class="bg-red-500 px-3 py-1 rounded text-sm hover:bg-blue-600 text-white">
           Blocked
        </a>
    <?php } ?>
                    </td>

                    <td class="p-3"><?php echo $row['created_at']; ?></td>
                    <td class="p-3"><?php echo $row['is_completed']; ?></td>

                    <td class="p-3 text-center space-x-3">

                      

                        <a href="delete_user.php?uid=<?php echo $row['uid']; ?>"
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