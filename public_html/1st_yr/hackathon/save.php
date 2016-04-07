<?php
	$oldData = file_get_contents("data.json");
//	var_dump($oldData);
	$o = json_decode($oldData);
	var_dump($o);
/*
	$o[] = $_POST["myData"];
	echo count($o);
	file_put_contents( json_encode($o) );
*/	
