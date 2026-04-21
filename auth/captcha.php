<?php
session_start();

    header("Content-type: image/jpeg");

    $width = 140;
    $height = 45;

    $image = imagecreatetruecolor($width, $height);

    // Random background color
    $bgColor = imagecolorallocate($image, rand(200,255), rand(200,255), rand(200,255));
    imagefill($image, 0, 0, $bgColor);

    // Generate random code
    $code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
    $_SESSION["vercode"] = $code;

    // Add noise dots
    for ($i = 0; $i < 100; $i++) {
        $noiseColor = imagecolorallocate($image, rand(150,200), rand(150,200), rand(150,200));
        imagesetpixel($image, rand(0,$width), rand(0,$height), $noiseColor);
    }

    // Add random lines
    for ($i = 0; $i < 6; $i++) {
        $lineColor = imagecolorallocate($image, rand(100,180), rand(100,180), rand(100,180));
        imageline($image, rand(0,$width), rand(0,$height),
                rand(0,$width), rand(0,$height), $lineColor);
    }

    // Add distorted text
    for ($i = 0; $i < strlen($code); $i++) {

        $textColor = imagecolorallocate($image, rand(0,120), rand(0,120), rand(0,120));

        imagestring(
            $image,
            5,
            15 + ($i * 18),
            rand(5,15),
            $code[$i],
            $textColor
        );
    }

    imagejpeg($image);
    imagedestroy($image);
?>