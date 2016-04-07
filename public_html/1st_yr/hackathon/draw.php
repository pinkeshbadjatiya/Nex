<!DOCTYPE html>
<html>
<head>
<script>
function showHint(str)
{
  var xmlhttp;
  if (str.length==0)
      { 
          document.getElementById("txtHint").innerHTML="";
            return;
            }
    xmlhttp=new XMLHttpRequest();

  xmlhttp.onreadystatechange=function()
      {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                      document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
                          }
            }
  xmlhttp.open("GET","content.php?q="+str,true);
  xmlhttp.send();
}
</script>
</head>
<body>

<h3>Start typing a name in the input field below:</h3>
<form action=""> 
First name: <input type="text" id="txt1" onkeyup="showHint(this.value)" />
</form>
<p>Suggestions: <span id="txtHint"></span></p> 

</body>
</html>
