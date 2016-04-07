function Time() {
	document.getElementById('time').innerHTML = Date();
	var t = setTimeout(function(){Time()},500);
}

function source() {
	document.getElementById('source_code').innerHTML;
	var html = $("html").html();
}


$(window).load(function() {
	$(".loader").fadeOut("slow");
})

