<?php
header('Content-type: image/jpeg');
foreach($_REQUEST as $key=>$value)
{
	$$key=$value;
}
$width = 60;
$height = 30;

$my_image = imagecreatetruecolor($width, $height);

imagefill($my_image, 0, 0, 0xDDDDDDDD);

// add noise
for ($c = 0; $c < 40; $c++){
	$x = rand(0,$width-1);
	$y = rand(0,$height-1);
	imagesetpixel($my_image, $x, $y, 0x11111111);
	}

$x = rand(1,10);
$y = rand(1,10);

$rand_string = $rand_string;
imagestring($my_image, 5, $x, $y, $rand_string, 0x000000);
setcookie('tntcon',$rand_string);

imagejpeg($my_image);
imagedestroy($my_image);

?>