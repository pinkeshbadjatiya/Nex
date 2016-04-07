<?php
	$file = 'links';
	file_put_contents($file, str_replace("\r\n","_",$_GET["q"]));
	$message=shell_exec("cat links");
	//print_r(urlencode($message));
	echo htmlentities($message);
	header("Location: http://researchweb.iiit.ac.in/~pinkesh.badjatiya/home.php");
	die();
?>
