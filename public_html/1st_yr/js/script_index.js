function Time() {
	document.getElementById('time').innerHTML = Date();
	var t = setTimeout(function(){Time()},500);
}

function source() {
	document.getElementById('source_code').innerHTML;
	var html = $("html").html();
}

var slideimages=new Array()
	function slideshowimages()
{
	for (i=0;i<slideshowimages.arguments.length;i++)
	{
		slideimages[i]=new Image()
			slideimages[i].src=slideshowimages.arguments[i]
	}
}


alert("Hello !! \n\n 1) Many pages contains a lot of elements with ':hover' attribute turned on that give a FASCINATING LOOK.\n\n So make sure you check every element by hovering over most of the elements.\n\n2) This also applies to many IMAGES.\n\n 3) Even the HEADER of the page is designed with various functionality.\n\n 4) Finally, i want to confess that \n\n THIS PAGE WAS SOLELY DEVELOPED BY ME AND ALL MY EFFORTS AND EFFECTS ARE ORIGINALLY THOUGHT BY ME THEN IMPLEMENTED THROUGH ONLINE HELP.\n\n NO PART OF IT COPIED FROM ANYWHERE.\n\nYES!! This is the truth.\n ");
