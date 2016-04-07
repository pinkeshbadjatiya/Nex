<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="UTF-8">
    <title>Pinkesh Badjatiya</title>
    <script>

        <?php
          $oldData = file_get_contents("links");
          echo 'var x="'.($oldData).'";';
        ?>

window.onload = function() {
        var A = document.getElementById('txtField');
        A.innerHTML = x.replace(/_/g,'\r');
      }

    </script>
  </head>

  <body>
<br/>

	<h1> INSTRUCTIONS:</h1>
	<p> 1. Links should be of format 'A2', where A is the URL of page and 2 is the no of visits required. </p>
	<p> 2. URL's must have 'http://' before </p>
<br/>
  <form action="home_save.php" method="GET" >
    <textarea rows="14" cols="50" id="txtField" name="q" placeholder="Type Your Links here :)"></textarea>
    <input type="submit" value="Submit">
  </form>

  </body>
</html>











