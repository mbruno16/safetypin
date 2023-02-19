var arr_buttonAnimationFrames = [
"IMG_CURSOR_02_FRAME01.png", 
"IMG_CURSOR_02_FRAME02.png", 
"IMG_CURSOR_02_FRAME03.png", 
"IMG_CURSOR_02_FRAME04.png", 
"IMG_CURSOR_02_FRAME05.png", 
"IMG_CURSOR_02_FRAME06.png", 
"IMG_CURSOR_02_FRAME07.png", 
"IMG_CURSOR_02_FRAME08.png"
];

var str_cursorDirectory = "CURSOR/"; //directory path (with cursor images)
var cursorAnimationInt;
var cursorAnimationFrame = 0;

function animateCursor(){
    animatedCursorForElement('button', arr_buttonAnimationFrames);
    
    if($('button').is(':disabled')) {
        cursorAnimationFrame += 1;
    	if(cursorAnimationFrame > arr_buttonAnimationFrames.length-1){
    		cursorAnimationFrame = 0;
    	}
    } else {
        cursorAnimationFrame = 0;
    }
}

function animatedCursorForElement(str_tagName, arr){
	var _element = document.getElementsByTagName(str_tagName);
	for (var i=0; i<_element.length; ++i){
		_element[i].style.cursor = 'url(' + str_cursorDirectory + arr[cursorAnimationFrame] + '), pointer';
	}
}

function animatedCursor(){
	cursorAnimationInt = setInterval(animateCursor, 70);
}