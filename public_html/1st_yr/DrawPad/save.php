<?php
	if ($_POST["myData"] == "reset_key_9361")
	{
		file_put_contents("data.json", "");
	}
	else if ($_POST["myData"] == "reset_key_0000")
	{
		$oldData = file_get_contents("data.json");
		echo ($oldData);
		var_dump($oldData);
		$o = json_decode($oldData);
		var_dump($o);
		$o[] = $_POST["chatData"];
		//	count($o);
    	echo file_put_contents( "data.json", json_encode($o) );
	}
	else
	{
		$oldData = file_get_contents("data.json");
		echo ($oldData);
		var_dump($oldData);
		$o = json_decode($oldData);
		var_dump($o);
		$o[] = $_POST["myData"];
		//	count($o);
    	echo file_put_contents( "data.json", json_encode($o) );
	}
?>
