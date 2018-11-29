<?php
echo "Enter the number of samples : ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
$nSamples = (int) $line;

echo "Enter the samples value successively :\n";
$samples =[];
for($i =0 ; $i < $nSamples ;$i++){
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    $samples[] = (float) $line;
}

echo "Enter the number of bits : ";
$handle =fopen("php://stdin",'r');
$bits = (int)fgets($handle);
$levels = pow(2,$bits);
$range = max($samples)-min($samples);
$delta = $range/$levels;

echo "Enter one of the interpolated samples : ";
$handle =fopen("php://stdin",'r');
$interpolated = (float)fgets($handle);

echo "Enter the the original sample : ";
$handle =fopen("php://stdin",'r');
$orginal = (float)fgets($handle);
$error = abs($orginal -$interpolated);
echo "The level of this sample ".$interpolated." is ".whichLevel($interpolated,min($samples),max($samples),$levels)."\n";
echo "The Error of this reproduced sample is ".$error."\n";
echo "Thank you, ^_^\n";

function whichLevel($sample,$lowLimit,$highLimit,$levels_count){
    $delta = ($highLimit - $lowLimit)/$levels_count;
    $test =$lowLimit;
    for($i = 1 ;$i <= $levels_count ;$i++){
        $test+=$delta;
        if($sample < $test )
            return $i;
    }
}