<?php file_put_contents( "data.json", ""); ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="description" content="Draw Pad">
    <title>Draw Pad</title>

    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://github.hubspot.com/pace/pace.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="js/colpick.js" type="text/javascript"></script>
    
	<link rel="stylesheet" href="css/colpick.css" type="text/css"/>
	<script src="animo/animo.js" type="text/javascript"></script>
	<link href="animo/animate-animo.css" rel="stylesheet" type="text/css">

    <!--
        All thanks to .....
        http://www.howopensource.com/2014/12/introduction-to-server-sent-events/
    -->

    <script>
        function showHint(str) {
        	//str = str.replace(/\n\r?/g, '\\r');
            //str=encodeURI(str);
            str = str.replace(/\n/g,'$');
            console.log(str);
            var xmlhttp;

            xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("txtHint").value = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", "content.php?q=" + str, true);
            xmlhttp.send();
        }

function doMagicOut() {
		$('#d').animo({animation: "fadeOutLeft", duration: 0.5, keep: true}, function() {
			$('#r').animo({animation: "fadeOutUp", duration: 0.5, keep: true}, function() {
				$('#a').animo({animation: "fadeOutDown", duration: 0.5, keep: true}, function() {
					$('#w').animo({animation: "fadeOutRight", duration: 0.5, keep: true}, function() {
						$('#p').animo({animation: "fadeOutLeft", duration: 0.5, keep: true}, function() {
							$('#a2').animo({animation: "fadeOutUp", duration: 0.5, keep: true}, function() {
								$('#d2').animo({animation: "fadeOutRight", duration: 0.5, keep: true}, doMagicIn());
							});
						});
					});
				});
			});
		});
	}

	function doMagicIn() {
		$('#d').animo({animation: "fadeInLeft", duration: 0.5}, function() {
			        	$('#d').css('opacity','1');
			$('#r').animo({animation: "fadeInUp", duration: 0.5}, function() {
				        	$('#r').css('opacity','1');
				$('#a').animo({animation: "fadeInDown", duration: 0.5}, function() {
					        	$('#a').css('opacity','1');
					$('#w').animo({animation: "fadeInRight", duration: 0.5}, function() {
						        	$('#w').css('opacity','1');
						$('#p').animo({animation: "fadeInLeft", duration: 0.5}, function() {
							        	$('#p').css('opacity','1');
							$('#a2').animo({animation: "fadeInUp", duration: 0.5}, function() {
								        	$('#a2').css('opacity','1');
								$('#d2').animo({animation: "fadeInRight", duration: 0.5});
								        	$('#d2').css('opacity','1');
							});
						});
					});
				});
			});
		});
	}

        window.onload = function() {
        	//r = document.getElementById('preLoad');
        	//$('#preLoad').animate({opacity:'0'},function() {
        	//	document.body.removeChild(r);
        	//});

        	$('#d').css('opacity','0');
        	$('#r').css('opacity','0');
        	$('#a').css('opacity','0');
        	$('#w').css('opacity','0');
        	$('#p').css('opacity','0');
        	$('#a2').css('opacity','0');
        	$('#d2').css('opacity','0');
        	document.getElementById('txt1').innerHTML = "";
        	showHint("");
	       	$('body').animate({ padding:'0'});
	        $('#svgDiv').mouseenter(function() {
	        	$(this).css('cursor','url(css/pen.cur),auto');
	        	$('body').animate({ borderWidth : '0', padding:'15'});

	        		}).mouseleave(function() {
	        		 	$(this).css('cursor','url(css/arrow.cur),auto');
	        		 	$('body').animate({ borderWidth : '15', padding:'0'});
	        		 		});
	      	$('#txt1').mouseenter(function() {$(this).css('cursor','url(css/IBeam.cur),auto');}).mouseleave(function() {$(this).css('cursor','url(css/arrow.cur),auto');

	      	});

			doMagicIn();
	  }

    </script>
</head>

<body>


<div style="position:fixed; bottom:0px; right:25px; width:250px;padding:0px;">
    <table id="chatTable">
        <tr>
            <td onclick="$('#HIDE1, #HIDE2').toggle()">
                <p class="sideHeader">Send Message</p>
            </td>
        </tr>
        <tr id="HIDE1">
            <td>
                <div id="displayChat">
                    <div style="margin:0 auto;color:red;"> USERCODE is <span id="usercode" style="color:green;"> unknown </span> </div>
                    <div class="me TXT"> pa lakj sks sjsk sksj ks sjksjs kjs ksj </div>
                    <div class="other TXT"> anka jaskj ajs akshjakshjakhdj d j djd jd d </div>
                    <div class="me TXT"> pa lakj sks sjsk sksj ks sjksjs kjs ksj </div>
                </div>
            </td>
        </tr>
        <tr id="HIDE2">
            <td>
                <form id="Form">
                    <input type="text" name="input" placeholder="Press Enter To Send the msg" id="chat"></input>
                </form>
            </td>
        </tr>
    </table>
</div>

<script>
            $('#HIDE1, #HIDE2').hide()
</script>
<!--
	<div id="preLoad">
		<img src="css/loading.gif" />
	</div>
-->

    <br/>
    <br/>
    <br/>


    <table id="headingTable">
        <tr>
            <td>
            	<img id="mono" src=css/draw_pad_icon.png alt="mono_draw_pad"/>
            </td>
            <td style="text-align:left">

            		<h1 style="display:inline-block" id="d">D</h1>
					<h1 style="display:inline-block" id="r">r</h1>
					<h1 style="display:inline-block" id="a">a</h1>
					<h1 style="display:inline-block" id="w">w</h1>
					<h1 style="display:inline-block" id="p">P</h1>
					<h1 style="display:inline-block" id="a2">a</h1>
					<h1 style="display:inline-block" id="d2">d</h1>
            </td>
        </tr>
    </table>

    <p id="subHead">An Online Teaching Tool to digitialize the teaching process.</p>

  	<div style="text-align:center; margin:0px auto;">
  		<div  class="underLine" style="text-align:center; margin:0px auto; margin-top:20px; height:10px; width:50px;background-color:#2E9EFF">
   		</div>
   		<div id="dummy" style="height:0px;">
        </div>
   	</div>


    <br/>

        <div style='text-align:center;'>
            <div  id='asd'>
                <div id="svgDiv" style="background-color:white;">
                    <svg id="mySVG" width="100%" height="100%" onmousedown=startDrawing(evt) onmousemove=svgCursor(evt) onmouseup=endDrawing(evt)>
                    </svg>
                </div>
            </div>

	    <table id="buttons" >
            	<tr>
            		<td td="td0" style="padding:15px;">
            			<p class="btn" id="resetBtn" onclick="reset()">Clear</p>
            			<h4 id="erase">Erased !!</h4>
           			</td>
           			<td id="td1"  style="padding:15px;">
            			<p class="btn" id="strokeBtn" onclick="stroke_width()">StrokeWidth</p>
            			<p id="sliderResult"></p>
            			<div id="slider"></div>
            		</td>
            		<td id="td2"  style="padding:15px;">
            			<p class="btn" id="colorBtn" onclick="stroke_color()">StrokeColor</p>
            			<table>
            				<tr>
            					<td><div id="colorResult"></div></td>
            					<td><h4 id="hex"></h4></td>
            				</tr>
            			</table>
            		</td>
                    <td id="td3"  style="padding:15px;">
                        <p class="btn" id="backgroundBtn" onclick="background_color()">Background</p>
                        <table>
                            <tr>
                                <td><div id="backgroundResult"></div></td>
                                <td><h4 id="hexBack"></h4></td>
                            </tr>
                        </table>
                    </td>
            	</tr>
        </table>

            <br/>
            <br/>

            <table id="table">
                <tr>
                    <td>
                        <p>
                            Content:
                        </p>
                    </td>
                    <td >
                        <form action="">
                            <textarea class="txtArea" rows="4" cols="100" id="txt1" placeholder="Hi Bob !! Hope you got my message :)" onkeyup="showHint(this.value)">
                            </textarea>
                        </form>
                    </td>
                </tr>
            </table>

                <p>Rendered content: <span id="txtHint"></span></p>

            <br/><br/><br/>



            <div id='lastDiv' style="padding:10px;">
            	<div style="margin:20px;margin-bottom:80px;">
            		<hr style="width:50%; margin-left:25%; text-shadow:2px 2px 8px black;"/>
        			<div style="display:inline; margin:0px">
        				<img id="mono_footer" src=css/draw_pad_icon.png alt="mono_draw_pad"/>
            		</div>
            		<div style="display:inline; margin:0px">
            			<p class="fontImprove" style="margin:0px"> DrawPad </p>
            			<p class="fontImprove" id="moulding" style="margin:0px"> Moulded formally by Pinkesh Badjatiya. </p>
            		</div>
            	</div>
            </div>

        </div>

        <script id=myScript>

            var Draw = false;
            var DrawTarget = null;
            var OffsetX = 0;
            var OffsetY = 0;
            var svgns = "http://www.w3.org/2000/svg";
            var elem;
            var svg = document.getElementById("mySVG");
            var svgDiv = document.getElementById("svgDiv");

	    // Variables to send
	    var StrokeWidth = 2;
	    var StrokeColor = "#000000";
            ////////////////////////////////////////////////

            var dataToSend = {};

            function startDrawing(evt) {
                var rect = svgDiv.getBoundingClientRect();
                var x = evt.clientX - rect.left;
                var y = evt.clientY - rect.top;
                Draw = true;
                elem = document.createElementNS(svgns, "path")
                elem.setAttribute("fill", "none")
                elem.setAttribute("stroke", StrokeColor)
                elem.setAttribute("stroke-width", StrokeWidth)
                elem.setAttribute("d", "M" + x + " " + y)
                svg.appendChild(elem);
                dataToSend.sw = StrokeWidth;
                dataToSend.sc = StrokeColor;
            }

            function svgCursor(evt) {
                var rect = svgDiv.getBoundingClientRect();
                var x = evt.clientX - rect.left;
                var y = evt.clientY - rect.top;
                if (Draw) {
                    elem.setAttribute("d", elem.getAttribute("d") + " L" + x + " " + y);
                }
            }

            function endDrawing(evt) {
                Draw = false;
                dataToSend.d = elem.getAttribute("d");
                sendDataToServer(dataToSend);
            }

            function sendDataToServer(data) {
                $.post("./save.php", {
                    myData: data
                }, function(r) {
                    console.log(r)
                });
            }

            function reset() {
                $.post("./save.php", { myData: "reset_key_9361" }, function(r) {console.log(r)});
                $('#svgDiv').animo({animation: "fadeOutLeft", duration: 0.5}, function() {
                	document.getElementById('mySVG').innerHTML = "";
                	$('#svgDiv').animo({animation: "fadeInRight", duration: 0.5});

					});
		$('#erase').animate({opacity:'1'},"slow",function(){$('#erase').animate({opacity:'0'});});
            }

	$("#slider").slider({
	    range: "min",
	    value: 2,
	    min: 1,
	    max: 10,
	    //this gets a live reading of the value and prints it on the page
	    slide: function(event, ui) {
	      $("#sliderResult").text(ui.value);
	    },
	    //this updates the value of your hidden field when user stops dragging
	    change: function(event, ui) {
	     	StrokeWidth = ui.value;
	    }
	  });

	$('#erase').css('opacity',0);
	$('#slider').css('opacity',0);
	$('#sliderResult').css('opacity',0);

	$('#colorResult').css('opacity',0);
	$('#hex').css('opacity',0);
	$('#colorResult').css('background-color',StrokeColor);
	$('#hex').text(StrokeColor);

    $('#backgroundResult').css('opacity',0);
    $('#hexBack').css('opacity',0);
    $('#backgroundResult').css('background-color','#ffffff');
    $('#hexBack').text('#ffffff');

	$('#sliderResult').text(StrokeWidth);

	    function stroke_width() {
	    	$('#sliderResult').text(StrokeWidth);
		if (document.getElementById('slider').style.opacity < 1)
			$('#slider').animate({opacity:'1'});
		else
			$('#slider').animate({opacity:'0'});


		if (document.getElementById('sliderResult').style.opacity < 1)
	    		$('#sliderResult').animate({opacity:'1'});
	    	else
	    		$('#sliderResult').animate({opacity:'0'});
	    }

	    function stroke_color() {
	   	if (document.getElementById('colorResult').style.opacity < 1)
	   	{	$('#colorResult').animate({opacity:'1'});
	   		$('#hex').animate({opacity:'1'});
	   	}
	   	else
	   	{	$('#colorResult').animate({opacity:'0'});
	   		$('#hex').animate({opacity:'0'});
	   	}
	   	$('#colorResult').colpick({
			colorScheme:'dark',
			layout:'rgbhex',
			color:'ff8800',
			onChange:function(hsb,hex,rgb,el,bySetColor) {
				$('#colorResult').css('backgroundColor','#'+hex);
				StrokeColor = '#' + hex;
				$('#colorResult').css('backgroundColor',StrokeColor);
				$('#hex').text(StrokeColor);
				if(!bySetColor) $(el).val(hex);
			},
			onSubmit:function(hsb,hex,rgb,el) {
					StrokeColor = '#' + hex;
					$('#colorResult').css('backgroundColor',StrokeColor);
					$('#hex').text(StrokeColor);
					$(el).colpickHide();

			}
		});
	    }

    function background_color() {
        if (document.getElementById('backgroundResult').style.opacity < 1)
        {   $('#backgroundResult').animate({opacity:'1'});
            $('#hexBack').animate({opacity:'1'});
        }
        else
        {   $('#backgroundResult').animate({opacity:'0'});
            $('#hexBack').animate({opacity:'0'});
        }
        $('#backgroundResult').colpick({
            colorScheme:'dark',
            layout:'rgbhex',
            color:'ff8800',
            onChange:function(hsb,hex,rgb,el,bySetColor) {
                $('#backgroundResult').css('backgroundColor','#'+hex);
                $('#hexBack').text('#'+hex);
                if(!bySetColor) $(el).val(hex);
            },
            onSubmit:function(hsb,hex,rgb,el) {
                    var tempColor = '#' + hex;
                    $('#backgroundResult').css('backgroundColor',tempColor);
                    $('#hexBack').text(tempColor);
                    $(el).colpickHide();
                    Transfer(tempColor);
            }
        });
        }



    $('.underLine').css('opacity',0);
    $('#dummy').css('opacity',0);

        $('#headingTable').mouseenter(function() {
                $('.underLine').animate({opacity:'1', height:'3',width:'320'});
                $('#dummy').animate({height:'7'});
                $('#headingTable').animo( { animation: 'tada' } );

                    }).mouseleave(function() {
                        $('.underLine').animate({opacity:'0', height:'10',width:'50'});
                        $('#dummy').animate({height:'0'});
                            });


var tab = document.createElement('table');
tab.setAttribute('id','PLAIN');

var len = 10;
var hei = 5;
var counter = 1;

for (var j = 1; j <= hei; j++) {
    var ro = document.createElement('tr');
    for (var i = 1; i <= len; i++) {
        var s = '<td><div class="card effect__click" id="' + j + '_' + i + '" ><div class="front"></div><div class="back"></div></div></td>'
        ro.innerHTML += s;
    }

    tab.appendChild(ro);
    console.log(ro);
}

function start() {

counter = 1;
for (var j = 1; j <= hei; j++) {
        for (var i = 1; i <= len; i++) {


  // create a closure to preserve the value of "i"
  (function(j,i,counter){

    window.setTimeout(function(){
      var e = document.getElementById(j+'_'+i);
      console.log(e);
            var c = e.classList;
            c.contains('flipped') === true ? c.remove('flipped') : c.add('flipped');
    }, counter * 50);

  }(j,i,counter));

counter++;

        }
    }

}

var flag=1;

function Transfer(futureColor) {

    // Fetch Future Color from user first ....
    // Place future color variable here

    p = document.getElementById('asd');
    d = document.getElementById('svgDiv');
    var COL = document.getElementById('svgDiv').style.backgroundColor;

    var temp = p.removeChild(d);
    p.appendChild(tab);

    if(flag%2)
    {
        flag++;
        var cols = document.getElementsByClassName('front');
        for(i=0; i<cols.length; i++) {
            cols[i].style.backgroundColor = COL;
        }

        cols = document.getElementsByClassName('back');
        for(i=0; i<cols.length; i++) {
            cols[i].style.backgroundColor = futureColor;
        }
    }
    else
    {
        flag++;
        var cols = document.getElementsByClassName('front');
        for(i=0; i<cols.length; i++) {
            cols[i].style.backgroundColor = futureColor;
        }

        cols = document.getElementsByClassName('back');
        for(i=0; i<cols.length; i++) {
            cols[i].style.backgroundColor = COL;
        }
    }
    temp.style.backgroundColor = futureColor;

    start();
    window.setTimeout(function(){
        p.removeChild(tab);
        p.appendChild(temp);
    }, (counter+1) * 50);
}


var USERCODE = (Math.random().toString(36)+'00000000000000000').slice(2, 10);
document.getElementById('usercode').innerHTML = USERCODE;

$('#Form').submit(function () {
 postChat();
 return false;
});

function postChat() {
    var V = $('#chat').val();
    var ARR = new Array(USERCODE,V);
    console.log(ARR);
    var a = 'reset_key_0000';
    $.post("save.php", { myData: a , chatData: ARR }, function(r) {console.log(r)});
    document.getElementById('chat').value = "";
}


    </script>

    </body>

    </html>
