<?php
	$file="temp_svg.svg";
	$o = $_POST["svg"];
	$old = file_get_contents($file);
	
	$length = 7;
	$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	//echo $randomString;

	file_put_contents($file,$o);
	system('rsvg-convert temp_svg.svg > export_png/'.$randomString.'.png');
	//echo $_SERVER['REQUEST_URI'].'-'.$_SERVER['PHP_SELF'].'-'
	//echo 'http://'.$_SERVER['HTTP_HOST'].'/DrawPad/export_png/'.$randomString.'.png';
	echo 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/export_png/'.$randomString.'.png';
	//echo 'http://'.$_SERVER['REQUEST_URI'].'/DrawPad/export_png/'.$randomString.'.png';
	//echo file_put_contents( "data.json", json_encode($o) );
?>

