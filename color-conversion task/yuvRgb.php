<?php

$yuvImage = rgbImageToYuv('sweety.png');
imagepng( $yuvImage , "sweety_yuv.png");

$rgbImage = yuvImageToRgb('sweety_yuv.png');
imagepng( $rgbImage , "sweety_rgb.png");

/**
 * Subsampling defines how information is discarded as a ratio of color (which can be discarded more with less discernible effect) against brightness.
 * It is defined for the two chroma (color) channels — red/green (Cr) and blue/yellow (Cb) — as relative to the luma (brightness) channel (Y) of the image
 * by downsampling the image by a factor of two in either the x direction only, or both the x and y directions. 
 * This is shown by the format Y,Cr,Cb, so 1x1,1x1,1x1 gives no subsampling,
 * and thus the highest image quality. 2x2,1x1,1x1 downsamples both the Cr and Cb channels by half,
 * giving the highest compression available here. 2x1,1x1,1x1 only downsamples the Cr channel, again by half.
 */


// subsampling the yuv image using Imagick
$img = new Imagick('sweety_yuv.png');
$img->setSamplingFactors(array('2x2', '1x1', '1x1'));
$img->writeImage('sweety_subsampled.png');

// convert it back to RGB
$subSampledImage = yuvImageToRgb('sweety_subsampled.png');
imagepng( $subSampledImage , "sweety_rgb_subsampled.png");

class RGB{
	public $R;
	public $G;
	public $B;
}

class YUV{
	public $Y;
	public $U;
	public $V;
}

function RGBToYUV(RGB $rgb) {
	$yuv = new YUV();
	$yuv->Y = $rgb->R * .299 + $rgb->G * .587 + $rgb->B * .114;
	$yuv->U = 0.436 * (($rgb->B - $yuv->Y) / (1 - 0.114));
	$yuv->V = 0.615 * (($rgb->R - $yuv->Y) / (1 - 0.299));

	return $yuv;
}

function YUVToRGB(YUV $yuv) {
	$rgb = new RGB();
	$rgb->R = $yuv->Y + $yuv->V * ((1 - 0.299) / 0.615);
	$rgb->G = $yuv->Y - ($yuv->U * (0.114 * (1 - 0.114))/(0.436*0.587)) - ($yuv->V * (0.299 * (1 - 0.299))/(0.615 * 0.587));
	$rgb->B = $yuv->Y + $yuv->U * ((1 - 0.114) / 0.436);

	return $rgb;
}

function yuvImageToRgb($source){

    $im = imagecreatefrompng($source);

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

                // get the color index for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                $yuv = new YUV();
                $yuv->Y = $colors['red'];
                $yuv->U = $colors['green'];
                $yuv->v = $colors['blue'];

                $rgb = YUVToRGB($yuv);


                // grayscale values have r=g=b=color

                $colorValue = imagecolorallocate($im, $rgb->R, $rgb->G, $rgb->B);

                // set the color value to the pixel

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }

    return $im;
}

function rgbImageToYuv($source){

    $im = imagecreatefrompng($source);

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

                // get the color index for current pixel

                $colorIndex = ImageColorAt($im, $i, $j); 

                // extract each value for r, g, b

                $colors = imagecolorsforindex($im, $colorIndex);

                $rgb = new RGB();
                $rgb->R = $colors['red'];
                $rgb->G = $colors['green'];
                $rgb->B = $colors['blue'];

                $yuv = RGBToYUV($rgb);


                // grayscale values have r=g=b=color

                $colorValue = imagecolorallocate($im, $yuv->Y, $yuv->U, $yuv->V);

                // set the color value to the pixel

                imagesetpixel ($im, $i, $j, $colorValue);
        }
    }

    return $im;
}