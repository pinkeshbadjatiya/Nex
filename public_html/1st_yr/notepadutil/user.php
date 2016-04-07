<html>
 <head>
  <title>Welcome User</title>
 </head>
 <body>

<?php

$nickname = $_POST["nickname"];
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$password = $_POST["password"]

$myFile = "$nickname-$firstname-$lastname.txt";
$fh = fopen($myFile, 'r') or die("can't read file");

$nicknamesaved = fgets($fh);
$firstnamesaved = fgets($fh);
$lastnamesaved = fgets($fh);
$sexsaved = fgets($fh);
$emailsaved = fgets($fh);
$passwordsaved = fgets($fh);

fclose($fh);
if ($password==$passwordsaved) 
{
	echo "<h1>Welcome $firstname $lastname ($nickname)</h1><br/>"
	echo "<p>Current date and time: " . date("r") . "</p>";
	echo "<a href="researchweb.iiit.ac.in/~pinkesh.badjatiya/notepadutil/index.html"> HOME !!</a>"

iiiiiiiiiiiiiiiiiiiiiiiiiiiiiii DISPLAY OF FILE AND DATA - INCLUDE.
}
else
{
	echo "<h1>There occured some internal error.</h1>"
	echo "<p>Either you typed the wrong password or username.<br/>"
	echo "Please try again using correc login information.</p>"

}
?>


 </body>
</html>
