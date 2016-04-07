<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="description" content="Draw Pad">
    <title>Draw Pad</title>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
        <script src="http://github.hubspot.com/pace/pace.js" type="text/javascript"></script>
    <script src="animo/animo.js" type="text/javascript"></script>
    <link href="animo/animate-animo.css" rel="stylesheet" type="text/css">
    

<!--
All thanks to .....
http://www.howopensource.com/2014/12/introduction-to-server-sent-events/ 
-->

    <script>

        var Draw = false;
        var DrawTarget = null;
        var OffsetX = 0;
        var OffsetY = 0;
        var svgns = "http://www.w3.org/2000/svg";


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

            $('#d').css('opacity','0');
            $('#r').css('opacity','0');
            $('#a').css('opacity','0');
            $('#w').css('opacity','0');
            $('#p').css('opacity','0');
            $('#a2').css('opacity','0');
            $('#d2').css('opacity','0');
            document.getElementById("mySVG").innerHTML = generate_svg("[]");
            document.getElementById("txt").innerHTML = "No Content to Display :(";
            $('#svgDiv').mouseenter(function() {$(this).css('cursor','url(css/no.cur),auto');}).mouseleave(function() {$(this).css('cursor','url(css/arrow.cur),auto');});
            $('#txt').mouseenter(function() {$(this).css('cursor','url(css/IBeam.cur),auto');}).mouseleave(function() {$(this).css('cursor','url(css/arrow.cur),auto');});
            
    doMagicIn();
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


            if (!!window.EventSource) {
                var source = new EventSource("update_svg.php");

                source.addEventListener("svg_message", function(e) {
                    document.getElementById("mySVG").innerHTML = generate_svg(e.data);
                    update_status();
                    //console.log(e.data);
                }, false);


                source.addEventListener("txt_message", function(e) {
                    update_txt(e.data.replace(/\$/g,'\n'));
                    //console.log(e.data.replace(/\$/g,'\n'));
                }, false);

                source.addEventListener("open", function(e) {
                    //console.log("Connection was opened.");
                    connection_healthy();
                }, false);

                source.addEventListener("error", function(e) {
                    //alert('ERROR - Connection was Lost');
                    //alert('Retrying in 2 sec ... ');
                    connection_error();
                    //console.log("Error - connection was lost.");
                }, false);

            } else {
                alert("Your browser does not support Server-sent events! Please upgrade it!");
            }

        }


        function update_txt(str) {
            document.getElementById("txt").innerHTML = str;
        }

        function export_to_png() {

            var data = $("#svgDiv").html();
            $.post("svg_to_png.php", {
                svg: data
            }, function(r) {
                console.log(r);
                var img = document.createElement('img');
                img.src = r;
                var head = document.getElementById('saved');
                head.appendChild(img);
                var br = document.createElement('br');
                head.appendChild(br);
            });
        }

        function export_txt() {

            var data = document.getElementById('txt').innerHTML;
            var p = document.createElement('p');
            p.innerHTML = data;
            var head = document.getElementById('saved');
            head.appendChild(p);
            var br = document.createElement('br');
            head.appendChild(br);
        }


        function connection_healthy() {
            var ele = document.getElementById('connection');
            ele.innerHTML = "Live Feed :)";
            ele.style.backgroundColor = "green";
        }


        function connection_error() {

            var ele = document.getElementById('connection');
            ele.innerHTML = "Retrying for Live Feed :(";
            ele.style.backgroundColor = "tomato";
        }


        function connection_unknown() {
            var ele = document.getElementById('connection');
            ele.innerHTML = "Connecting to Server :|";
            ele.style.backgroundColor = "grey";
        }

        function update_status() {
	    $('#status').animate({opacity: "1"});
            $('#statusDiv').animate({opacity: "1"});
            window.setTimeout(function clearBackground() {
                $('#statusDiv').animate({opacity: "0"});
	        $('#status').animate({opacity: "0"});
            }, 500);

        }

        function generate_svg(paths) {

            //console.log(paths);
            if (paths.length < 1) {
                paths = "[]";
            }

            paths = JSON.parse("" + paths + "");
            //console.log('FUNCTION');
            //console.log(paths);
            var svg = '';
            for (var i in paths) {
                svg += '<path d="' + paths[i].d + '" stroke="' + paths[i].sc + '" stroke-width="' + paths[i].sw + '" fill="none" />\n';
            }

            return svg;
        }
    </script>

</head>
<body style='font-family:arial;'>

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
        <div id="svgDiv">
            <svg id="mySVG" style='background-color:white;' width="800px" height="400px" version="1.1" xmlns="http://www.w3.org/2000/svg">
            	<rect class="frame" height="800" width="400"/>
            </svg>
        </div>
        <p id="connection" style="background:grey"> Connecting to Server...</p>
        <div id="statusDiv">
        	<p id="status"> Data Updated </p>
       	</div>
        <br/>
        <button class="btn" onClick='export_to_png(this)'> Save Image in Collection</button>
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
                            <textarea readonly class="txtArea" rows="4" cols="100" id="txt" onkeyup="showHint(this.value)">
                            </textarea>
                        </form>
                    </td>
                </tr>
            </table>
            
            
        
        <br/>
        <button class="btn" onClick='export_txt(this)'> Save Text in Collection</button>
        <br/>

        <div id="saved">
        </div>


        <br/><br/><br/>


            <div id='lastDiv' style="padding:10px;">
                <div style="margin:20px;margin-bottom:80px;">
                    <hr style="width:700px; margin-left:300px; text-shadow:2px 2px 8px black;"/>
                    <div style="display:inline; margin:0px">
                        <img id="mono_footer" src=css/draw_pad_icon.png alt="mono_draw_pad"/>
                    </div>
                    <div style="display:inline; margin:0px">
                        <p class="fontImprove" style="margin:0px"> DrawPad </p>
                        <p class="fontImprove" id="moulding" style="margin:0px"> Moulded formally by Pinkesh Badjatiya. </p>
                    </div>
                </div>
            </div>


</body>
</html>
