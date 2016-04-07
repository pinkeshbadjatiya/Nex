


var standardbody=(document.compatMode=="CSS1Compat")? document.documentElement : document.body //create reference to common "body" across doctypes

var nav = (!document.all || window.opera);
var tmr = null;
var spd = 50;
var x = 0;
var x_offset = 5;
var y = 0;
var y_offset = 15;

document.onmousemove = get_mouse; 

function get_mouse(e)
{    
  x = (nav) ? e.pageX : event.clientX+standardbody.scrollLeft;
  y = (nav) ? e.pageY : event.clientY+standardbody.scrollTop;
  x += x_offset;
  y += y_offset;
  beam(1);     
}

function beam(n)
{
  if(n<5)
  {
    document.getElementById('div'+n).style.top=y+'px'
      document.getElementById('div'+n).style.left=x+'px'
      document.getElementById('div'+n).style.visibility='visible'
      n++;
    tmr=setTimeout("beam("+n+")",spd);
  }
  else
  {
    clearTimeout(tmr);
    fade(4);
  }   
} 

function fade(n)
{
  if(n>0) 
  {
    document.getElementById('div'+n).style.visibility='hidden'
      n--;
    tmr=setTimeout("fade("+n+")",spd);
  }
  else clearTimeout(tmr);
} 

