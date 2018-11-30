<?php 
header( "Content-type: image/png");

$source_gray = imagecreatefrompng('sweety.png');
$source_threshold = imagecreatefrompng('sweety.png');
$source_adapted = imagecreatefrompng('sweety.png');
$source_floyed = imagecreatefrompng('sweety.png');
$source_order = imagecreatefrompng('sweety.png');

// convert to gray
convertToGray($source_gray);
// output the gray png
imagepng( $source_gray , "sweety_gray.png");

// apply threshold_Dithering
thresholdDithering($source_threshold);
// output the binary png 
imagepng( $source_threshold , "sweety_threshold.png");

// apply adapted_threshold_Dithering
adaptedThresholdDithering($source_adapted);
// output the binary png 
imagepng( $source_adapted , "sweety_adapted_threshold.png");

// apply floyed_Dithering
floydDithering($source_floyed);
// output the binary png 
imagepng( $source_floyed , "sweety_floyed_steinberg.png");


// apply order_Dithering
$threshold_matrix = [[0,128,32,160],[192,64,224,96],[48,176,16,144],[240,112,208,80]];
orderDithering($source_order,$threshold_matrix);
// output the binary png 
imagepng( $source_order , "sweety_order.png");


function orderDithering(&$im,$orderThreshold){

    // imagesx — Get image width
    // imagesy — Get image height
    // imagecolorat — Get the index of the color of a pixel
    // imagecolorsforindex — Get the colors for an index
    // imagecolorallocate — Allocate a color for an image ,
    //   Returns a color identifier representing the color composed of the given RGB components.
    // imagesetpixel — Set a single pixel

    $imgwidth = imagesx($im);
    $imgheight = imagesy($im);

    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the gray value for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                $row = $j % count($orderThreshold);
                $column = $i % count($orderThreshold[0]);

                if($colors['red'] >= $orderThreshold[$row][$column])
                    $color = 255;
                else
                    $color = 0;


                // get the color identifier

                $colorValue = imagecolorallocate($im, $color, $color, $color);

                // set the pixel value

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }
}

function floydDithering(&$im){

    // imagesx — Get image width
    // imagesy — Get image height
    // imagecolorat — Get the index of the color of a pixel
    // imagecolorsforindex — Get the colors for an index
    // imagecolorallocate — Allocate a color for an image ,
    //   Returns a color identifier representing the color composed of the given RGB components.
    // imagesetpixel — Set a single pixel

    $imgwidth = imagesx($im);
    $imgheight = imagesy($im);

    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the gray value for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                if($colors['red'] >= 128)
                $color = 255;
                else
                $color = 0;

                $error = $colors['red'] - $color;

                // Current pixel

                // get the color identifier
                $colorValue = imagecolorallocate($im, $color, $color, $color);

                // set the pixel color
                imagesetpixel ($im, $i, $j, $colorValue);


                if($i+1 < $imgwidth){
                    // pixel of i+1,j

                    // get the index of the color

                    $colorIndex1 = ImageColorAt($im, $i+1, $j); 

                    // extract each value for r, g, b

                    $colors = imagecolorsforindex($im, $colorIndex1);

                    $newColor = round($colors['red']+(7/16)*$error);

                    // get the color identifier
                    $colorValue1 = imagecolorallocate($im,$newColor, $newColor, $newColor);

                    // set the pixel color
                    imagesetpixel ($im, $i+1, $j, $colorValue1);
                }

                if($i-1 >=0 && $j+1 < $imgheight){
                    // pixel of i-1,j+1

                    // get the index of the color

                    $colorIndex1 = ImageColorAt($im, $i-1, $j+1); 

                    // extract each value for r, g, b

                    $colors = imagecolorsforindex($im, $colorIndex1);

                    $newColor = round($colors['red']+(3/16)*$error);

                    // get the color identifier
                    $colorValue1 = imagecolorallocate($im,$newColor, $newColor, $newColor);

                    // set the pixel color
                    imagesetpixel ($im, $i-1, $j+1, $colorValue1);
                }

                if($j+1 < $imgheight){
                    // pixel of i,j+1

                    // get the index of the color

                    $colorIndex1 = ImageColorAt($im, $i, $j+1); 

                    // extract each value for r, g, b

                    $colors = imagecolorsforindex($im, $colorIndex1);

                    $newColor = round($colors['red']+(5/16)*$error);

                    // get the color identifier
                    $colorValue1 = imagecolorallocate($im,$newColor, $newColor, $newColor);

                    // set the pixel color
                    imagesetpixel ($im, $i, $j+1, $colorValue1);
                }

                if($i+1 < $imgwidth && $j+1 < $imgheight){
                    // pixel of i+1,j+1

                    // get the index of the color

                    $colorIndex1 = ImageColorAt($im, $i+1, $j+1); 

                    // extract each value for r, g, b

                    $colors = imagecolorsforindex($im, $colorIndex1);

                    $newColor = round($colors['red']+(1/16)*$error);

                    // get the color identifier
                    $colorValue1 = imagecolorallocate($im,$newColor, $newColor, $newColor);

                    // set the pixel color
                    imagesetpixel ($im, $i+1, $j+1, $colorValue1);
                }
        }
    }
}

function adaptedThresholdDithering(&$im){

    // imagesx — Get image width
    // imagesy — Get image height
    // imagecolorat — Get the index of the color of a pixel
    // imagecolorsforindex — Get the colors for an index
    // imagecolorallocate — Allocate a color for an image ,
    //   Returns a color identifier representing the color composed of the given RGB components.
    // imagesetpixel — Set a single pixel

    $imgwidth = imagesx($im);
    $imgheight = imagesy($im);

    $sum = 0;

    // compute the sum of colors in image
    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the gray value for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                $sum +=$colors['red'];
        }
    }

    $avgIntensity = $sum/($imgheight*$imgwidth);

    // apply adapted dithering

    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the gray value for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                if($colors['red'] > (int)$avgIntensity)
                $color = 255;
                else
                $color = 0;


                // grayscale values have r=g=b=color

                $colorValue = imagecolorallocate($im, $color, $color, $color);

                // set the gray value

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }
}

function thresholdDithering(&$im){

    // imagesx — Get image width
    // imagesy — Get image height
    // imagecreate — Create a new palette based image 
    //   returns an image identifier representing a blank image of specified size.
    // imagecolorat — Get the index of the color of a pixel
    // imagecolorsforindex — Get the colors for an index
    // imagecolorallocate — Allocate a color for an image ,
    //   Returns a color identifier representing the color composed of the given RGB components.
    // imagesetpixel — Set a single pixel

    $imgwidth = imagesx($im);
    $imgheight = imagesy($im);

    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the gray value for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                if($colors['red'] < 128)
                $color = 0;
                else
                $color = 255;


                // grayscale values have r=g=b=color

                $colorValue = imagecolorallocate($im, $color, $color, $color);

                // set the gray value

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }
}

function convertToGray(&$im){

    // imagesx — Get image width
    // imagesy — Get image height
    // imagecolorat — Get the index of the color of a pixel
    // imagecolorsforindex — Get the colors for an index
    // imagecolorallocate — Allocate a color for an image ,
    //   Returns a color identifier representing the color composed of the given RGB components.
    // imagesetpixel — Set a single pixel

    $imgwidth = imagesx($im);
    $imgheight = imagesy($im);

    for ($i=0; $i<$imgwidth; $i++){
        for ($j=0; $j<$imgheight; $j++){

                // get the rgb value for current pixel

                $rgb = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $rgb);

                $rr = $colors['red'];
                $gg = $colors['green'];
                $bb = $colors['blue'];

                // get the Value from the RGB value

                $gray = round(0.299*$rr+0.587*$gg+0.114*$bb);

                // grayscale values have r=g=b=gray

                $colorValue = imagecolorallocate($im, $gray, $gray, $gray);

                // set the gray value

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }
}