<?php

$translateX = 100;
$translateY = 100;

$scaleX = 2;
$scaleY = 1.5;

$alphaAngle = 45;

// Create a blank image
$image = imagecreatetruecolor(3840, 2160);

// set background to white
$white = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $white);

// Allocate a color for the polygon
// imagecolorallocate return the color or false
$basicColor = imagecolorallocate($image, 0, 0, 0);

// Set the line thickness to 5
imagesetthickness($image, 5);

// Draw the polygon
$points = array(
    100, 0,
    1500, 400,
    1000, 500
);
imagepolygon($image,$points,count($points)/2,$basicColor);

// Draw translated the polygon
$tranlatedColor = imagecolorallocate($image, 255, 0, 0);
imagepolygon($image,translate($translateX,$translateY,$points),count($points)/2,$tranlatedColor);

// Draw scaled the polygon
$scaledColor = imagecolorallocate($image, 0, 255, 0);
imagepolygon($image,scale($scaleX,$scaleY,$points),count($points)/2,$scaledColor);

// Draw rotated the polygon
$rotatedColor = imagecolorallocate($image, 0, 255, 255);
imagepolygon($image,rotate($alphaAngle,$points),count($points)/2,$rotatedColor);

// Output the picture to the browser
header('Content-type: image/png');

imagepng($image,'output.png');
imagedestroy($image);

function translate($translateX,$translateY,$points){
    for($i =0;$i<count($points);$i++){
        if($i % 2 == 0){
            $points[$i] +=$translateX; 
        }
        else{
            $points[$i] +=$translateY;
        }
    }
    return $points;
}

function scale($scaleX,$scaleY,$points){
    for($i =0;$i<count($points);$i++){
        if($i % 2 == 0){
            $points[$i] *=$scaleX; 
        }
        else{
            $points[$i] *=$scaleY;
        }
    }
    return $points;
}

function rotate($alphaAngle,$points){
    $points = array_chunk($points, 2);
    $merged = array();
    foreach($points as $point){
        $x = $point[0];
        $y = $point[1];
        $point[0] =($x * cos($alphaAngle)) - ($y * sin($alphaAngle)) ;
        $point[1] =($x * sin($alphaAngle)) + ($y * cos($alphaAngle)) ;
        $merged = array_merge($merged,$point);
    }
    return $merged;
}
