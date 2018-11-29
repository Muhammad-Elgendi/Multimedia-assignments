<?php
/**
 * Developed by : Muhammad Elgendi
 * Task : Apply foreground to background by using a mask (some image processing with GD library)
 */
// Load source and mask and new backgrounds
$source = imagecreatefrompng( 'sweety.png' );
$mask = imagecreatefrompng( 'sweety_mask.png' );
$background = imagecreatefrompng( 'snowy.png' );
// Apply mask to source with background
imageAlphaMaskBackground( $source, $mask ,$background );
// Output
header( "Content-type: image/png");
imagepng( $source , "sweety_new.png");

function imageAlphaMaskBackground( &$picture, $mask ,$background ) {
    // Get sizes and set up new picture
    $xSize = imagesx( $picture ); // get x of image
    $ySize = imagesy( $picture ); // get y
    $newPicture = imagecreatetruecolor( $xSize, $ySize );// Create a new true color image 24-bit  (bit depth) image
    // sets the flag to attempt to save full alpha channel information (as opposed to single-color transparency) when saving PNG images.
    // imagesavealpha( $newPicture, true ); 
    // imagefill : Performs a flood fill starting at the given coordinate (top left is 0, 0) with the given color in the image.
    // imagecolorallocatealpha : Allocate a color for an image with the addition of the transparency parameter alpha.
    // imagecolorallocatealpha return the color or false
    // make image transperent
    // imagefill( $newPicture, 0, 0, imagecolorallocate( $newPicture, 0, 0, 0 ) );

    // Perform the equation foreground * alpha + background * 1-alpha or something like this :)
    /* 
       imagecolorsforindex — Get the colors for an index 
       Returns an associative array with red, green, blue and alpha keys that contain the appropriate values for the specified color index.
    */
    /*
      imagecolorat :  Returns the index of the color of the pixel at the specified location in the image specified by image.
      If the image is a truecolor image, this function returns the RGB value of that pixel as integer. Use bitshifting and masking to access the distinct red, green and blue component values:
    */
    // imagesetpixel() draws a pixel at the specified coordinate.
    for( $x = 0; $x < $xSize; $x++ ) {
        for( $y = 0; $y < $ySize; $y++ ) {            
            $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );            
            $backColor = imagecolorsforindex( $background, imagecolorat( $background, $x, $y ) );
            $color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
            $alphaZero = ($alpha[ 'red' ] == 255) ? 1 : 0;
            $alpha = (127 - floor( $alpha[ 'red' ] / 2 )) == 127 ? 1 : 0;   
            // echo  'zero '.$alphaZero.' alpha'.$alpha;               
            imagesetpixel( $newPicture, $x, $y, imagecolorallocate( $newPicture, $backColor[ 'red' ]*$alpha+ $color[ 'red' ]*$alphaZero, $backColor[ 'green' ]*$alpha+ $color[ 'green' ]*$alphaZero, $backColor[ 'blue' ]*$alpha+$color[ 'blue']*$alphaZero) );
            // imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
        }
    }

    // Copy back to original picture
    imagedestroy( $picture ); // imagedestroy() frees any memory associated with image image.
    $picture = $newPicture;
}


