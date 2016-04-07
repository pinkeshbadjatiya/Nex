<html>
<body>

Text is -  <?php
$file = 'data.txt';
file_put_contents($file, $_GET["q"]);
$message=shell_exec("cat data.txt");
print_r($message);

?><br>

</body>
</html>
