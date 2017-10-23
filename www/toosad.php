<?php
error_reporting(0);
session_start();
// instantiate new BasJan class and echo the image
require_once 'k.php';
require_once 'BasJan.class.php';

$image = null;

function getFromCache() {
  $imgs = glob('cache/basjan-*.jpg');
  return $imgs[array_rand($imgs)];
}

header('Content-type: image/jpeg');
if (rand(0,2) === 0) {
  try {
    $img = new BasJan($key);
  	$data = $img->build()->show();
  	$image = "cache/". uniqid("basjan-") . '.jpg';
  	@file_put_contents($image ,$data);
  } catch (Exception $e) {
    header('X-lhooq: apifail');
    $image = getFromCache();
  }
} else {
  $image = getFromCache();
}
echo @file_get_contents($image);

exit();
