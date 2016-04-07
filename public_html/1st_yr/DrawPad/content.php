<html>
<body>
<?php
	$file = 'data.txt';
	//echo "dkdsjk";
	file_put_contents($file, $_GET["q"]);
	$message=shell_exec("cat data.txt");
	//print_r(urlencode($message));
	echo htmlentities($message);

	//print_r('Text is -' + ' ' + $_GET["q"]);
?>

<br/>
</body>
</html>
