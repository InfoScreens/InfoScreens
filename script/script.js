

//------timeline script

var container = document.getElementById("timeline");


  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
    /*{id: 1, content: 'A', editable: true,  start: getTime("now"), end:getTime("+2")}*/
  ]);


  // time variables for options
  var today = moment();
  var tomorrow = moment().add(1,'d');


  // Configuration for the Timeline
  var options = {
    editable: true,
    start: today.format('YYYY-MM-DD'),
    end: tomorrow.format('YYYY-MM-DD'),
    minHeight: '200px',
    onRemove: function(item, callback){
      var data = new FormData();
      data.append("itemId", item.id);
      console.log(data);
      $.ajax({
        url:'../action.php?removeItem',
        method:'POST',
        data:data,
        cache:false,
        dataType:'text',
        processData:false,
        contentType:false,
        success:function(respond, textStatus, jqXHR){
          console.log("Success: "+respond+", "+textStatus+", "+jqXHR);
        },
        error:function(jqXHR, textStatus, errorThrown){
          console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
        }
      })
      items.remove(item);
    },
    onMove: function(item, callback){

      var start = moment(item.start);
      var end = moment(item.end);
      var data = new FormData();
      data.append("itemId", item.id);
      data.append("start", start.unix());
      data.append("end", end.unix());
      $.ajax({
        url:'../action.php?updateSchedule',
        method:'POST',
        data:data,
        cache:false,
        dataType:'text',
        processData:false,
        contentType:false,
        success:function(respond, textStatus, jqXHR){
          console.log("Success: "+respond+", "+textStatus+", "+jqXHR);
        },
        error:function(jqXHR, textStatus, errorThrown){
          console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
        }
      });
      //findIntersect(item);

    },

    
    /*
    // always snap to full hours, independent of the scale
    snap: function (date, scale, step) {
      var hour = 60 * 60 * 1000;
      return Math.round(date / hour) * hour;
    }//*/


    // to configure no snapping at all:
    //
    snap: null
    //
    // or let the snap function return the date unchanged:
    //
    //   snap: function (date, scale, step) {
    //     return date;
    //   }
  };

  // Create a Timeline
  var timeline = new vis.Timeline(container, items, options);




//------end of timeline 


//------add elements



  //////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////
  //
  // H E L P E R    F U N C T I O N S
  //
  //////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////

  /**
   * Function to check if we clicked inside an element with a particular class
   * name.
   * 
   * @param {Object} e The event
   * @param {String} className The class name to check against
   * @return {Boolean}
   */
  function clickInsideElement( e, className ) {
    var el = e.srcElement || e.target;
    
    if ( el.classList.contains(className) ) {
      return el;
    } else {
      while ( el = el.parentNode ) {
        if ( el.classList && el.classList.contains(className) ) {
          return el;
        }
      }
    }

    return false;
  }

  /**
   * Get's exact position of event.
   * 
   * @param {Object} e The event passed in
   * @return {Object} Returns the x and y position
   */
  function getPosition(e) {
    var posx = 0;
    var posy = 0;

    if (!e) var e = window.event;
    
    if (e.pageX || e.pageY) {
      posx = e.pageX;
      posy = e.pageY;
    } else if (e.clientX || e.clientY) {
      posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
      posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }

    return {
      x: posx,
      y: posy
    }
  }

  //////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////
  //
  // C O R E    F U N C T I O N S
  //
  //////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////
  
  /**
   * Variables.
   */
  var contextMenuClassName = "context-menu";
  var contextMenuItemClassName = "context-menu__item";
  var contextMenuLinkClassName = "context-menu__link";
  var contextMenuActive = "context-menu--active";

  var taskItemClassName = "element";
  var taskItemInContext;

  var clickCoords;
  var clickCoordsX;
  var clickCoordsY;

  var menu = document.querySelector("#context-menu");
  var menuItems = menu.querySelectorAll(".context-menu__item");
  var menuState = 0;
  var menuWidth;
  var menuHeight;
  var menuPosition;
  var menuPositionX;
  var menuPositionY;

  var windowWidth;
  var windowHeight;

  /**
   * Initialise our application's code.
   */
  function initContextMenu() {
    contextListener();
    //clickListener();
    keyupListener();
    resizeListener();
  }

  /**
   * Listens for contextmenu events.
   */
  function contextListener() {
    document.addEventListener( "click", function(e) {
      taskItemInContext = clickInsideElement( e, taskItemClassName );

      if ( taskItemInContext ) {
        e.preventDefault();
        toggleMenuOn();
        positionMenu(e);
      } else {
        taskItemInContext = null;
        toggleMenuOff();
      }
    });
  }

  /**
   * Listens for click events.
   */
  function clickListener() {
    document.addEventListener( "click", function(e) {
      var clickeElIsLink = clickInsideElement( e, contextMenuLinkClassName );

      if ( clickeElIsLink ) {
        e.preventDefault();
        menuItemListener( clickeElIsLink );
      } else {
        var button = e.which || e.button;
        if ( button === 1 ) {
          toggleMenuOff();
        }
      }
    });
  }//*/

  /**
   * Listens for keyup events.
   */
  function keyupListener() {
    window.onkeyup = function(e) {
      if ( e.keyCode === 27 ) {
        toggleMenuOff();
      }
    }
  }

  /**
   * Window resize event listener
   */
  function resizeListener() {
    window.onresize = function(e) {
      toggleMenuOff();
    };
  }

  /**
   * Turns the custom context menu on.
   */
  function toggleMenuOn() {
    if ( menuState !== 1 ) {
      menuState = 1;
      menu.classList.add( contextMenuActive );
    }
  }

  /**
   * Turns the custom context menu off.
   */
  function toggleMenuOff() {
    if ( menuState !== 0 ) {
      menuState = 0;
      menu.classList.remove( contextMenuActive );
    }
  }

  /**
   * Positions the menu properly.
   * 
   * @param {Object} e The event
   */
  function positionMenu(e) {
    clickCoords = getPosition(e);
    clickCoordsX = clickCoords.x;
    clickCoordsY = clickCoords.y;

    menuWidth = menu.offsetWidth + 4;
    menuHeight = menu.offsetHeight + 4;

    windowWidth = window.innerWidth;
    windowHeight = window.innerHeight;

    if ( (windowWidth - clickCoordsX) < menuWidth ) {
      menu.style.left = windowWidth - menuWidth + "px";
    } else {
      menu.style.left = clickCoordsX + "px";
    }

    if ( (windowHeight - clickCoordsY) < menuHeight ) {
      menu.style.top = windowHeight - menuHeight + "px";
    } else {
      menu.style.top = clickCoordsY + "px";
    }
  }

  /**
   * Dummy action function that logs an action when a menu item link is clicked
   * 
   * @param {HTMLElement} link The link that was clicked
   */
  function menuItemListener( link ) {
    console.log( "Task ID - " + taskItemInContext.getAttribute("data-id") + ", Task action - " + link.getAttribute("data-action"));
    toggleMenuOff();
  }

  /**
   * Run the app.
   */
  initContextMenu();


//------end of add elements


//------schedule upload



function uploadSchedule(){
  
}

$("#datetimepicker").on("dp.change", function(e){
  var today= moment($("#date").val(), "YYYY-MM-DD");
  var tomorrow = moment($("#date").val(), "YYYY-MM-DD");  
  tomorrow.add(1, "days");
  timeline.setWindow(today.format("YYYY-MM-DD"), tomorrow.format("YYYY-MM-DD"));
  uploadSchedule();
});



//------schedule saving/downloading/updating

function alex_time_to_unix (time) {
  return moment (time, "YYYY-MM-DD HH:mm:00").unix ();
}

function saveItem(itemId){
  var item = items.get(itemId);
  item.mon = $("#monSelect").val();
  item.date = $("#date").val();

  // convert time from string to unix timestamp
  var item_tweak = JSON.parse (JSON.stringify (item)); // hack, copy `item`
  item_tweak.start = alex_time_to_unix (item_tweak.start);
  item_tweak.end = alex_time_to_unix (item_tweak.end);
  item = item_tweak;

  item = JSON.stringify(item);  
  var data = new FormData();
  data.append("data", item);
  $.ajax({
    url:'../action.php?saveItem',
    method:'POST',
    data:data,
    cache:false,
    dataType:'text',
    processData:false,
    contentType:false,
    success:function(respond, textStatus, jqXHR){
      //console.log("saveItem success: "+respond+", "+textStatus+", "+jqXHR);
    },
    error:function(jqXHR, textStatus, errorThrown){
      console.log("saveItem fail: "+jqXHR+", "+textStatus+", "+errorThrown);
    }

  })//*/

}

function openSchedule(){
  $(".element:not('.add-element')").remove();
  var data = new FormData();
  data.append("mon", $("#monSelect").val());
  data.append("date", $("#date").val());

  $.ajax({
    url:'../action.php?openSchedule',
    method:'POST',
    data:data,
    cache:false,
    dataType:'text',
    processData:false,
    contentType:false,
    success:function(respond, textStatus, jqXHR){
      //console.log("Success: "+respond+", "+textStatus+", "+jqXHR);
      var response = JSON.parse (respond);
      var item;
      items.clear ();
      for(var i =0; i<response.length; i++){
        item = response[i];
        console.log("{id:"+item.itemId+", content:'"+item.itemId+"', start:"+item.startTime+", end:"+item.endTime+", filename:"+item.fileName+"}");
        items.add([{id:item.itemId, content:item.itemId, start: moment (1000*item.startTime).format("YYYY-MM-DD HH:mm:00"), end: moment(1000*item.endTime).format("YYYY-MM-DD HH:mm:00")}]);
      }

    },
    error:function(jqXHR, textStatus, errorThrown){
      console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
    }

  })
}




function updateItem(){

}
//------end of schedule upload



//------add files

function addClass(type){
  switch(type){
    case "image/png": return "png";
    break;
    case "image/jpeg": return "jpeg";
    break;
    case "video/mp4": return "video";
    break;
    case "application/pdf": return "pdf";
    break;
    case "video/quicktime": return "video";
    break;
    case "text/plain": return "text";
    break;

    default: return "unknownType";
  }
}

$("#addFileBtn").click(function(){
  $("#addFile").trigger("click");
})




var files, element;
$("#addFiles").change(function(e){
  var files = this.files;
  e.stopPropagation();
  e.preventDefault();
  var form = document.getElementById("addFiles");
  var data = new FormData(form);
  data.append("mon", $("#monSelect").val());
  data.append("date", $("#date").val());

  $.ajax({
    url:'../action.php?uploadfiles',
    method:'POST',
    data:data,
    cache:false,
    dataType:'text',
    processData:false,
    contentType:false,
    success:function(respond, textStatus, jqXHR){
      //console.log("Success: "+respond+", "+textStatus+", "+jqXHR);

      element = $.parseJSON(respond);
      
      $('.overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
      function(){ // пoсле выпoлнения предъидущей aнимaции
        $('#time-setting') 
          .css('display', 'block') // убирaем у мoдaльнoгo oкнa display: none;
          .animate({opacity: 1, top: '20%'}, 200); // плaвнo прибaвляем прoзрaчнoсть oднoвременнo сo съезжaнием вниз
      });
      
      
      
      
    },
    error:function(jqXHR, textStatus, errorThrown){
      console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
    }
  })

})




//------end of add files


//------window with time setting


  /* Зaкрытие мoдaльнoгo oкнa, тут делaем тo же сaмoе нo в oбрaтнoм пoрядке */

$('#addItemBtn').click( function(e){ // лoвим клик пo кнопке
    e.preventDefault();
    e.stopPropagation();
    var a1 = $("#startHour").val();
    var a2 = $("#startMinutes").val();
    var b1 = $("#endHour").val();
    var b2 = $("#endMinutes").val();
    if((a1>23 || b1>23) || (a1<0 || b1<0) || (a2>59 || b2>59)||(a2<0 || b2<0)){
      console.log("Invalid value");
      return 0;
    } 
    var today = $("#date").val();
    var start = moment(today, "YYYY-MM-DD")
    var end = moment(today, "YYYY-MM-DD")
    start.set({'hour':a1, 'minute':a2});
    end.set({'hour':b1, 'minute':b2});
    $(".add-element").before('<div class="element '+addClass(element['type'])+'" data-title="'+element["fileName"]+'"><img src="files/thumbnails/'+element["fileName"]+'.jpg"></div>');
      items.add([
        //{id:element["fileId"], content: element["fileName"], editable:true, start: start.unix (), end: end.unix ()}
         {id:element["fileId"], content: element["fileName"], editable:true, start: start.format("YYYY-MM-DD HH:mm:00"), end:end.format("YYYY-MM-DD HH:mm:00"),
x1: $("#x1").val (),
y1: $("#y1").val (),
x2: $("#x2").val (),
y2: $("#y2").val ()
        }
        ]);

      saveItem(element["fileId"]);
    $('#time-setting')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('.overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
  });

$('.overlay').click( function(){ // лoвим клик пo пoдлoжке
  $('#time-setting, .textEditor, #tickerEditor')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('.overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
});




//------- add text notes and ticker

$("#addAdvertBtn").click(function(){
  $(".overlay").fadeIn(400);
  $("#advertEditor")
                    
                    .css("display", "block")
                    .animate({opacity: 1});

})

$("#addTickerBtn").click(function(){
  $(".overlay").fadeIn(400);
  $("#tickerEditor")
                    
                    .css("display", "block")
                    .animate({opacity: 1});

})



$("#saveAdvert").click(function(){
  //var advert = $("#textEditor").val();
  var advert = CKEDITOR.instances.textEditor.getData();
  console.log(advert);
  $('#time-setting, .textEditor, #tickerEditor')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('.overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
  var now = moment();
  var end = moment();
  end.add(2, 'hour');
  items.add({ content:advert, start: now.format('YYYY-MM-DD HH:mm:ss'), end: end.format('YYYY-MM-DD HH:mm:ss')});
})



$("#saveTicker").click(function(){
  //var advert = $("#textEditor").val();
  var advert = $("#tickerEditorArea").val();

  console.log(advert);
  $('#time-setting, .textEditor, #tickerEditor')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('.overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
  var now = moment();
  var end = moment();
  end.add(2, 'hour');
  items.add({ content:advert, start: now.format('YYYY-MM-DD HH:mm:ss'), end: end.format('YYYY-MM-DD HH:mm:ss')});
})

var a1 = moment();
var a2 = moment();
var b1 = moment();
var b2 = moment();
a1.set('hour', 1);
a2.set('hour', 12);
b1.set('hour', 5);
b2.set('hour', 20);

//items.add({ content:"Date Range A", start: a1.format('YYYY-MM-DD HH:mm:ss'), end: a2.format('YYYY-MM-DD HH:mm:ss')});
//items.add({ content:"Date Range B", start: b1.format('YYYY-MM-DD HH:mm:ss'), end: b2.format('YYYY-MM-DD HH:mm:ss')});


function findIntersect(item){
  var a1, a2, b1, b2 = moment();
  
  var arr = items.get();
  var l, r;
  var a1 = moment(item.start);
  var a2 = moment(item.end);
  console.log("Start: "+a1+" end: "+a2);
  var b1 = moment();
  var b2 = moment();

  for(var i = 0; i<arr.length; i++){
    if(i == arr.id) continue;
    
    
  }
  

  
  console.log(a1.format('YYYY-MM-DD HH:mm:ss'));
  console.log(a2.format('YYYY-MM-DD HH:mm:ss'));
  console.log(b1.format('YYYY-MM-DD HH:mm:ss'));
  console.log(b2.format('YYYY-MM-DD HH:mm:ss'));

  //var l = (a1<b1) && (b1<a2);
  var l, r;

  if((a1<b1) && (b1<a2)){
    l = b1;
  }else if((a1>b1) && (a1<b2)){
    l = a1;
  }

  if((b2>a1) && (b2<a2)){
    r = b2;
  }else if((a2>b1) && (a2<b2)){
    r = a2;
  }
  //items.add({id:"A", content:"LOL", start:l, end:r, type:"background"});
  //*/
}



