//------timeline script


var bodyBlock = $('#timeline');
var slider = $('#inner_wrap');
var rails = $('#outer_wrap');
var startPos = curPos = 0;
var cW = slider.width();
var bW = bodyBlock.width();                          
var sideSpace = cW - bW;
rails.css({
    width: parseInt(cW + sideSpace,10),
    left:  -sideSpace,
    top: 5
});

function softStop(dest){
    slider.animate({left: dest},750, function(){
        if(dest < 0){
            softStop(0);
        }
        if(dest > sideSpace){
            softStop(sideSpace);
        }
    });
    return false;
}

slider.draggable({ 
    containment:rails, 
    axis: 'x',
    cursor: 'pointer',
    drag: function(e,ui){
        curPos = ui.position.left;
    },
    stop: function(e,ui){
        if(curPos > startPos && curPos > 0){
            startPos = curPos + 150;
        } else {
            startPos = curPos - 150;
        }
        softStop(startPos);
    }
});





//------end of timeline script