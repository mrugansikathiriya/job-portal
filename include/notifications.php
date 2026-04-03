<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

$uid = (int)$_SESSION['uid'];
$role = $_SESSION['role'] ?? 'user';

// Fetch notifications
if($role === 'admin'){
    // Admin sees all notifications
    $result = mysqli_query($conn, "
    SELECT n.*, u.role AS sender_role, 
        CASE WHEN u.role='seeker' THEN js.sname
             WHEN u.role='company' THEN c.cname
             ELSE u.uname END AS sender_name
    FROM notifications n
    LEFT JOIN users u ON n.uid = u.uid
    LEFT JOIN job_seeker js ON u.uid = js.uid
    LEFT JOIN company c ON u.uid = c.uid
    GROUP BY n.message
    ORDER BY n.created_at DESC
") or die(mysqli_error($conn));

} else {
    // Normal users see only their own notifications
    $result = mysqli_query($conn, "
        SELECT * FROM notifications WHERE uid='$uid' ORDER BY created_at DESC
    ") or die(mysqli_error($conn));

    // Mark as read only for normal users
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE uid='$uid'") or die(mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Notifications</title>
<link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png"></head>

<body class="bg-black text-white p-6">

<?php
$back_url = 'http://localhost/php_program/project/home.php'; // default

if (isset($_SESSION['uid']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $back_url = 'http://localhost/php_program/project/admin/admin_dashboard.php';
}
?>
<a href="<?php echo $back_url; ?>" 
   class="inline-block mb-6 text-yellow-400 text-sm hover:underline">
    ← Back
</a>

<h2 class="text-3xl font-bold text-yellow-400 mb-6 flex items-center gap-3">
    <span class="bg-yellow-400 text-black p-2 rounded-full">
        <i class="fa-solid fa-bell"></i>
    </span>
    Notifications
</h2>

<?php if(mysqli_num_rows($result) > 0): ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
        
        <div class="bg-[#161616] border border-gray-800 rounded-xl p-4 mb-4 
                    shadow-md hover:shadow-yellow-400/10 hover:border-yellow-400 
                    transition duration-300">

            <div class="flex justify-between items-start gap-4">

                <p class="text-white text-sm md:text-base leading-relaxed">
                    <?= htmlspecialchars($row['message']) ?>
                </p>

                <span class="text-xs text-gray-400 whitespace-nowrap">
                    <?= $row['created_at'] ?>
                </span>

            </div>

        </div>

    <?php endwhile; ?>

<?php else: ?>
    <p class="text-gray-400">No notifications found</p>
<?php endif; ?>
</body>
</html>