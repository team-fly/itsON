<?php
    $base=$_REQUEST['image'];
	$image_name=$_REQUEST['image_name'];
    $binary=base64_decode($base);
    header('Content-Type: bitmap; charset=utf-8');
    $file = fopen($image_name.'.jpg', 'wb');
    fwrite($file, $binary);
    fclose($file);
    echo 'Image upload complete!!, Please check your php file directory……';
?>