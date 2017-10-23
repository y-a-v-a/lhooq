<?php
error_reporting(0);
session_start();
// instantiate new Mona class and echo the image
require_once 'k.php';
require_once 'lib/Mona.class.php';

$image = null;

function getFromCache() {
  $imgs = glob('cache/img-*.jpg');
  return $imgs[array_rand($imgs)];
}

header('Content-type: image/jpeg');
if (rand(0,2) === 0) {
  try {
    $img = new Mona($key);
  	$data = $img->build()->show();
  	$image = "cache/". uniqid("img-") . '.jpg';
  	@file_put_contents($image ,$data);
  } catch(Exception $e) {
    header('X-lhooq: apifail');
    $image = getFromCache();
  }
} else {
	$image = getFromCache();
}
echo @file_get_contents($image);

exit();
