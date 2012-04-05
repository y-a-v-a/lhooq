<?php
error_reporting(E_ALL);
// instantiate new Mona class and echo the image
require_once 'lib/Mona.class.php';
$img = new Mona();
$image = null;

header('Content-type: image/jpeg');
if (rand(0,2) === 0) {
	$data = $img->build()->show();
	$image = "cache/". uniqid("img-") . '.jpg';
	@file_put_contents($image ,$data);
} else {
	$imgs = glob('cache/*.jpg');
	$image = $imgs[array_rand($imgs)];
}
echo @file_get_contents($image);

exit();
