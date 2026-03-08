<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../config/db.php";
require "../authc/csrf.php";

if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

$msg="";
$uid = $_SESSION['uid'];
$name = $_SESSION['uname'];

if(isset($_POST['submit']))
{

    if(!validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF Token");
    }

    $rating=$_POST['rating'];
    $message=$_POST['message'];

    if(empty($rating))
    {
        $msg="Please select a star rating!";
    }
    else
    {

        $check="SELECT * FROM feedback WHERE uid='$uid'";
        $result = mysqli_query($conn,$check);

        if(mysqli_num_rows($result) > 0)
        {
            echo "<script>
            alert('You have already submitted feedback!');
            window.location='http://localhost/php_program/project/home.php';
            </script>";
            exit();
        }
        else
        {

            $sql="INSERT INTO feedback(uid,name,rating,message)
            VALUES('$uid','$name','$rating','$message')";

            if(mysqli_query($conn,$sql))
            {

                regenerateCSRFToken();

                $_SESSION['success']="Feedback submitted successfully!";
                header("location:http://localhost/php_program/project/home.php/#feedback_section");
                exit();
            }

        }

    }

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Career Craft | Feedback</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

<style>

.star{
font-size:34px;
cursor:pointer;
color:#555;
transition:0.3s;
}

.star:hover{
color:gold;
transform:scale(1.2);
}

.active{
color:gold;
}

</style>

</head>

<body class="bg-black text-white">

<?php include("../include/navbar.php"); ?>

<div class="max-w-4xl mx-auto pt-20 pb-16 px-4">

<a href="http://localhost/php_program/project/home.php"
class="inline-block mb-6 text-yellow-400 text-sm hover:underline ">
← Back
</a>

<div class="flex justify-center">

<div class="max-w-5xl mx-auto bg-[#0f0f0f] shadow-2xl 
p-6 sm:p-8 border border-white/10 text-white mb-20">
<h1 class="text-3xl font-bold text-center text-yellow-400 mb-3">
Your Feedback Matters
</h1>

<p class="text-center mb-4">
Help us improve CareerCraft
</p>

<?php
if($msg!="")
{
echo "<p class='text-red-500 text-center mb-4 font-semibold'>$msg</p>";
}
?>

<form method="post" onsubmit="return validateForm()">

<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

<!-- USER INFO -->
<div class="flex flex-col items-center mb-4">

<?php if(!empty($_SESSION['profile_image'])): ?>  

    <?php
        if($_SESSION['role'] == 'company'){
            $imagePath = "http://localhost/php_program/project/company/uploads/" . $_SESSION['profile_image'];
        } else {
$imagePath = "http://localhost/php_program/project/seeker/uploads/" . $_SESSION['profile_image'];        }
    ?>

    <img src="<?= $imagePath ?>" 
        class="w-14 h-14 rounded-full object-cover border-2 border-[#D7AE27] mb-2"
        alt="Profile">

    <?php else: ?>

    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['uname']) ?>&background=D7AE27&color=000"
        class="w-14 h-14 rounded-full border-2 border-[#D7AE27] mb-2"
        alt="Profile">

<?php endif; ?>
<input type="text" name="name"
value="<?php echo $_SESSION['uname']; ?>"
readonly
class="text-center p-2 bg-[#161616] rounded w-full">

</div>


<label>Rating</label>

<div class="flex gap-3 mb-2">

<span class="star">★</span>
<span class="star">★</span>
<span class="star">★</span>
<span class="star">★</span>
<span class="star">★</span>

</div>

<p id="ratingError" class="text-red-500 text-sm mb-3"></p>

<input type="hidden" name="rating" id="rating">

<label>Message</label>

<textarea name="message" rows="4"
class="w-full p-2 mb-4 bg-[#161616] rounded"></textarea>

<button name="submit"
class="w-full bg-yellow-400 text-black py-2 rounded font-semibold hover:bg-yellow-500 transition">
Submit Feedback
</button>

</form>

</div>
</div>
</div>

<script>

let stars=document.querySelectorAll(".star");
let rating=document.getElementById("rating");
let error=document.getElementById("ratingError");

stars.forEach((star,index)=>{

star.addEventListener("click",()=>{

stars.forEach(s=>s.classList.remove("active"));

for(let i=0;i<=index;i++)
stars[i].classList.add("active");

rating.value=index+1;

error.innerText="";

});

});

function validateForm(){

if(rating.value==""){
error.innerText="Please select a star rating!";
return false;
}

return true;

}

</script>

<?php include("../include/footer.php"); ?>

</body>
</html>