<?php
require "../config/db.php";

if(isset($_POST['keyword'])){

    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);

    $query = mysqli_query($conn,
        "SELECT cname FROM company WHERE cname LIKE '%$keyword%' LIMIT 5"
    );

    if(mysqli_num_rows($query) > 0){

        while($row = mysqli_fetch_assoc($query)){
            echo "<div class='suggest-item p-2 cursor-pointer hover:bg-yellow-400 hover:text-black'>"
                 .$row['cname'].
                 "</div>";
        }

    } else {
        echo "<div class='p-2 text-gray-400'>No company found</div>";
    }
}