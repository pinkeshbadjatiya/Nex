<html>
 <head>
  <title>Success !!</title>
 </head>
 <body>

<?php
$output = shell_exec('ls -a');
echo "<pre>$output</pre>";
?>

<?php
$nickname = $_POST["nickname"];
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$sex = $_POST["sex"];
$email = $_POST["email"];
$password = $_POST["password"]

$myFile = "$nickname-$firstname-$lastname.txt";
$fh = fopen($myFile, 'w') or die("can't open file");

$stringData = "$nickname\n";
fwrite($fh, $stringData);

$stringData = "$firstname\n";
fwrite($fh, $stringData);

$stringData = "$lastname\n";
fwrite($fh, $stringData);

$stringData = "$sex\n";
fwrite($fh, $stringData);

$stringData = "$email\n";
fwrite($fh, $stringData);

$stringData = "$password\n";
fwrite($fh, $stringData);

fclose($fh);

echo "<h1>Dear $firstname $lastname ($nickname)</h1><br/>"
echo "<p>The registration was done successfully !! <br/>Please login to use the free notepad service.</p>"
echo "<a href="researchweb.iiit.ac.in/~pinkesh.badjatiya/notepadutil/login.html"> LOGIN HERE !!</a>"

?>


 </body>
</html>
