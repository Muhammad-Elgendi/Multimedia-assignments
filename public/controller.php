<?php

require_once '../compression-task/ShannonFano.php';
use ShannonFano;

// Render as JSON 
// header('Content-Type: application/json');

// Perpare data variables
$msg = strtoupper($_POST['msg']);

// build code book from a message
$shannon =(new ShannonFano())->setMessage($msg);

$shannon->buildCodeBook()->encodeMessage();

// print code book
// print_r($shannon->getCodeBook());

// print encoded message
echo $shannon->getCompressedCode()."\n";