<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

$csrf_token = generateCSRFToken();

$aid = $_GET['aid'] ?? 0;

if(!$aid){
    header("Location: sdashboard.php");
    exit();
}

$_SESSION['aid'] = $aid;

$app = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT score FROM application WHERE aid = $aid")
);

/* PREVENT SECOND TEST */

if($app['score'] > 0){
    echo "
    <div style='text-align:center;margin-top:100px'>
    <h2>Test already submitted.</h2>
    <a href='sdashboard.php'>Go to Dashboard</a>
    </div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Career Craft | Aptitude Assessment</title>

<link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-gray-200 min-h-screen">
<?php include("../include/navbar.php"); ?>

<a href="find_job.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline  ml-10">
   ← Back
</a>
<div class="max-w-4xl mx-auto  bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800 mb-10">

    <div class="flex justify-between text-center mb-6">
        <h1 class="text-center font-bold text-blue-400">
            Career Craft – Aptitude Assessment
        </h1>
        <div class="text-red-400 font-semibold text-lg">
            Time Left: <span id="timer">10:00</span>
        </div>
    </div>


   

    <form id="quizForm" class="space-y-8"></form>

    <button 
        id="submitBtn"
        onclick="submitTest()"
        disabled
        class="block mx-auto mt-8 px-8 py-3 rounded-lg font-semibold
               bg-gray-600 cursor-not-allowed">
        Submit Test
    </button>
        <?php if ($app['score'] > 0): ?>
        <div class="text-center text-green-400 font-bold mt-6">
            Your Previous Score: <?= $app['score'] ?>
        </div>
        <?php endif; ?>

    <div id="result" class="text-center text-xl font-bold text-blue-400 mt-8"></div>

</div>



<script>
    const csrfToken = "<?php echo $csrf_token; ?>";
// ================= QUESTIONS =================
let questions = [
{
q:"A company increases salaries by 20% and then decreases them by 10%. What is the net change?",
options:["8% increase","10% increase","12% increase","No change"],
answer:0
},
{
q:"Find the missing number: 5, 11, 23, 47, ?",
options:["95","96","94","93"],
answer:2
},
{
q:"A candidate scores 40% and fails by 20 marks. If the passing marks are 200, what is the maximum marks?",
options:["400","450","500","600"],
answer:2
},
{
q:"Choose the correct sentence:",
options:[
"Neither the manager nor the employees was present",
"Neither the manager nor the employees were present",
"Neither manager nor employees were present",
"Neither the manager or the employees were present"
],
answer:1
},
{
q:"If all developers are engineers and some engineers are designers, which conclusion is correct?",
options:[
"All designers are developers",
"Some developers are designers",
"Some engineers are designers",
"No developers are designers"
],
answer:2
},
{
q:"What will be the output of: int x = 5; x += x++ + ++x;",
options:["15","16","17","18"],
answer:2
},
{
q:"A can complete a task in 12 days, B in 18 days. In how many days will they complete it together?",
options:["6.5 days","7 days","7.2 days","8 days"],
answer:2
},
{
q:"Which SQL clause is used to remove duplicate records from a result set?",
options:["UNIQUE","DISTINCT","REMOVE","DELETE"],
answer:1
},
{
q:"If today is Monday, what day will it be after 61 days?",
options:["Wednesday","Thursday","Friday","Saturday"],
answer:1
},
{
q:"A number is increased by 20%, then decreased by 20%. What happens to the number?",
options:["Increases","Decreases","Remains same","Becomes zero"],
answer:1
}
];

// ================= SHUFFLE QUESTIONS =================
questions.sort(() => Math.random() - 0.5);

// ================= RENDER QUESTIONS =================
const quizForm = document.getElementById("quizForm");
const submitBtn = document.getElementById("submitBtn");

questions.forEach((item, index) => {
    quizForm.innerHTML += `
        <div class="border-b border-gray-700 pb-6">
            <h3 class="text-lg font-semibold text-white mb-3">
                Q${index + 1}. ${item.q}
            </h3>

            <div class="space-y-2">
                ${item.options.map((opt, i) => `
                    <label class="flex items-center space-x-2 cursor-pointer hover:text-blue-400">
                        <input type="radio" name="q${index}" value="${i}" 
                               class="accent-blue-500" onchange="checkAllAnswered()">
                        <span>${opt}</span>
                    </label>
                `).join("")}
            </div>
        </div>
    `;
});

// ================= CHECK ALL ANSWERED =================
function checkAllAnswered(){
    let answered = true;
    questions.forEach((_, index) => {
        if(!document.querySelector(`input[name="q${index}"]:checked`)){
            answered = false;
        }
    });

    if(answered){
        submitBtn.disabled = false;
        submitBtn.classList.remove("bg-gray-600","cursor-not-allowed");
        submitBtn.classList.add("bg-green-600","hover:bg-green-500","cursor-pointer");
    }
}

// ================= SUBMIT TEST =================

function submitTest(){
    clearInterval(timerInterval);

    let score = 0;
    questions.forEach((item, index) => {
        const selected = document.querySelector(`input[name="q${index}"]:checked`);
        if(selected && parseInt(selected.value) === item.answer){
            score++;
        }
    });

    const percentage = ((score / questions.length) * 100).toFixed(2);
fetch("save_result.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `score=${score}&percentage=${percentage}&csrf_token=${csrfToken}`
})
.then(response => response.text())
.then(data => {

if(data.includes("success")){
    window.location.href = "sdashboard.php";
}

else if(data.includes("fail")){
    alert("You failed the test. Please attempt again.");
    window.location.reload();
}

else{
    alert(data);
}

});
}


// ================= TIMER (10 MINUTES) =================
let timeLeft = 600; // seconds

const timerDisplay = document.getElementById("timer");

const timerInterval = setInterval(() => {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;

    timerDisplay.innerText =
        `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

    if(timeLeft <= 0){
        clearInterval(timerInterval);
        submitTest();
        alert("Time is up! Test auto-submitted.");
    }

    timeLeft--;
}, 1000);
</script>

</body>
<?php include("../include/footer.php"); ?>

</html>