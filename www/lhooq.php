<?php
error_reporting(E_ALL);
// instantiate new Mona class and echo the image
require_once '../share/Mona.class.php';
$img = new Mona();

header('Content-type: image/jpeg');
echo $img->build()->show();

exit();
