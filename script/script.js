//------get time

function getTime(p){
  
  var d = new Date();
  var year = d.getFullYear();
  var month = d.getMonth()+1 < 10 ? '0'+(d.getMonth()+1) : d.getMonth()+1;
  var date = d.getDate() < 10 ? '0'+d.getDate() : d.getDate();
  var hour = d.getHours() < 10 ? '0'+d.getHours() : d.getHours();
  var min = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes();
  var sec = d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds();

  var workDate = $("#date").val();
  var workYear = workDate.substring(0, 4);
  var workMonth = workDate.substring(5,7)-1;
  var workDay = workDate.substring(8);
  


  if(p == 'now'){
    return year+'-'+month+'-'+date+'T'+hour+':'+min+':'+sec; 
  }
  if(p == 'start' || p == 'min'){
    return year+'-'+month+'-'+date;
  }
  if(p == 'workDate'){ 
    workDate = new Date(workYear, workMonth, workDay, hour); //допустим здесь не совсем очевидно, но придумывать названия для новых
                                                             //переменных мне лень, поэтому в workDate сначала кладу значение поля date, 
                                                             //затем кладу в эту переменную дату, для которой мы сейчас редактируем трансляцию
    var workYear = workDate.getFullYear();
    var workMonth = workDate.getMonth()+1 < 10 ? '0'+(workDate.getMonth()+1) : workDate.getMonth()+1;
    var workDay = workDate.getDate() < 10 ? '0'+workDate.getDate() : workDate.getDate();
    var workHour = workDate.getHours() < 10 ? '0'+workDate.getHours() : workDate.getHours();
    var workMin = workDate.getMinutes() < 10 ? '0'+workDate.getMinutes() : workDate.getMinutes();
    var workSec = workDate.getSeconds() < 10 ? '0'+workDate.getSeconds() : workDate.getSeconds();
    //console.log("workDate: "+workYear+"-"+workMonth+"-"+workDay+"T"+workHour+":00:00");
    return workYear+"-"+workMonth+"-"+workDay+"T"+workHour+":00:00";
  }
   if(p == "+2"){ // +2 часа от workDate. Почему +2? Да потому что.

    // ЧЁТ ХРЕНЬ ТУТ КАКАЯ-ТО ПРОИСХОДИТ: ПЕРЕПИСАТЬ.
    workDate = new Date(workYear, workMonth, workDay, hour+1); //допустим здесь не совсем очевидно, но придумывать названия для новых
                                                             //переменных мне лень, поэтому в workDate сначала кладу значение поля date, 
                                                             //затем кладу в эту переменную дату, для которой мы сейчас редактируем трансляцию
    
    var workYear = workDate.getFullYear();
    var workMonth = workDate.getMonth()+1 < 10 ? '0'+(workDate.getMonth()+1) : workDate.getMonth()+1;
    var workDay = workDate.getDate() < 10 ? '0'+workDate.getDate() : workDate.getDate();
    var workHour = workDate.getHours() < 10 ? '0'+workDate.getHours() : workDate.getHours();
    var workMin = workDate.getMinutes() < 10 ? '0'+workDate.getMinutes() : workDate.getMinutes();
    var workSec = workDate.getSeconds() < 10 ? '0'+workDate.getSeconds() : workDate.getSeconds();
    //workDate.setHours(workDate.getHours()+2);
    //console.log("+2 : "+workYear+"-"+workMonth+"-"+workDay+"T"+workHour+":00:00");
    return workYear+"-"+workMonth+"-"+workDay+"T"+workHour+":00:00";
  }


  if(p == 'end'){
    var a = new Date();
    a.setDate(a.getDate()+1);
    year = a.getFullYear();
    month = a.getMonth()+1 < 10 ? '0'+(a.getMonth()+1) : a.getMonth()+1;
    date = a.getDate() < 10 ? '0'+a.getDate() : a.getDate();
    return year+'-'+month+'-'+date;
  }//*/


  return 0;
}




//------timeline script

var container = document.getElementById("timeline");


  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
    /*{id: 1, content: 'A', editable: true,  start: getTime("now"), end:getTime("+2")}*/
  ]);



  // Configuration for the Timeline
  var options = {
    editable: true,
    min: getTime('min'),
    start: getTime('start'),
    end: getTime('end'),
    minHeight: '200px',


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
  console.log("Today is: "+today.format("YYYY-MM-DD"));
  console.log("Tomorrow is: "+tomorrow.format("YYYY-MM-DD"));
  timeline.setWindow(today.format("YYYY-MM-DD"), tomorrow.format("YYYY-MM-DD"));
  uploadSchedule();
});



//------schedule saving/downloading/updating



function saveItem(itemId){
  var item = items.get(itemId);
  item.mon = $("#monSelect").val();
  item.date = $("#date").val();
  var item = JSON.stringify(item);  
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
      console.log("Success: "+respond+", "+textStatus+", "+jqXHR);
    },
    error:function(jqXHR, textStatus, errorThrown){
      console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
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
      console.log("Success: "+respond+", "+textStatus+", "+jqXHR);
      var response = JSON.parse (respond);
      var item;
      items.clear ();
      for(var i =0; i<response.length; i++){
        //console.log("resp"+i+": "+response[i]);
        item = response[i];
        console.log("{id:"+item.itemId+", content:'"+item.itemId+"', start:"+item.startTime+", end:"+item.endTime+"}");
        //items.add("{id:"+item.itemId+", content:'"+item.itemId+"', start:"+item.startTime+", end:"+item.endTime+"}");
        items.add([{id:item.itemId, content:item.itemId, start: item.startTime, end: item.endTime}]);
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

$("#addVideoBtn").click(function(){
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
      console.log("Success: "+respond+", "+textStatus+", "+jqXHR);

      element = $.parseJSON(respond);
      
      $('#overlay').fadeIn(400, // снaчaлa плaвнo пoкaзывaем темную пoдлoжку
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

$('#addItemBtn').click( function(){ // лoвим клик пo крестику или пoдлoжке
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
    console.log("ST: "+start.format("YYYY-MM-DD HH:mm:00"));
    console.log("ST: "+end.format("YYYY-MM-DD HH:mm:00"));
    $(".add-element").before('<div class="element '+addClass(element['type'])+'" data-title="'+element["fileName"]+'"><img src="files/thumbnails/'+element["fileName"]+'.jpg"></div>');
      items.add([
        {id:element["fileId"], content: element["fileName"], editable:true, start: start.format("YYYY-MM-DD HH:mm:00"), end:end.format("YYYY-MM-DD HH:mm:00")}
        ]);

      saveItem(element["fileId"]);
    $('#time-setting')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('#overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
  });

$('#overlay').click( function(){ // лoвим клик пo крестику или пoдлoжке
    
    $('#time-setting')
      .animate({opacity: 0, top: '15%'}, 200,  // плaвнo меняем прoзрaчнoсть нa 0 и oднoвременнo двигaем oкнo вверх
        function(){ // пoсле aнимaции
          $(this).css('display', 'none'); // делaем ему display: none;
          $('#overlay').fadeOut(400); // скрывaем пoдлoжку
        }
      );
  });
