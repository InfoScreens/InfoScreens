//------get time

function getTime(p){
  
  var d = new Date();
  var year = d.getFullYear();
  var month = d.getMonth()+1 < 10 ? '0'+(d.getMonth()+1) : d.getMonth()+1;
  var date = d.getDate() < 10 ? '0'+d.getDate() : d.getDate();
  var hour = d.getHours() < 10 ? '0'+d.getHours() : d.getHours();
  var min = d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes();
  var sec = d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds();
  if(p == 'now'){
    return year+'-'+month+'-'+date+'T'+hour+':'+min+':'+sec; 
  }
  if(p == 'start' || p == 'min'){
    return year+'-'+month+'-'+date;
  }
  if(p == 'workDate'){
    var workDate = $("#date").val();
    var workDay = workDate.substring(0,2);
    var workMonth = workDate.substring(3,5);
    var workYear = workDate.substring(6);
    return workYear+'-'+workMonth+'-'+workDay+'T12:00:00';
  }
   if(p == "+3"){
    return workYear+'-'+workMonth+'-'+workDay+'T15:00:00';
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


//------schedule saving

function updateSchedule(){
  items.getIds();
  console.log("update schedule");
  var ids = items.Ids();

}

function addItems(){
  console.log("add items");
}



//------timeline script

var container = document.getElementById("timeline");


  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
    //{id: 1, content: 'A', editable: true,  start: '2016-07-07', end:'2016-07-08'}
  ]);



  // Configuration for the Timeline
  var options = {
    editable: true,
    min: getTime('min'),
    start: getTime('start'),
    end: getTime('end'),
    minHeight: '200px',

    onAdd: function(item, callback){
      console.log("adding");
    },

    onMove: function(item, callback){
      console.log("moved");
    }, 

    onMoving: function(item, callback){
      console.log("moving");
    }, 

    onUpdate: function(item, callback){
      console.log("update");
    }

    /*
    // always snap to full hours, independent of the scale
    snap: function (date, scale, step) {
      var hour = 60 * 60 * 1000;
      return Math.round(date / hour) * hour;
    }*/


    // to configure no snapping at all:
    //
    //   snap: null
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

function uploadSchedule(lol){
  alert("ok");
}




$("#date").on("edit", function(e){
  //uploadSchedule($("#date").val());
console.log("change");
  alert("date was changed");
})  //*/







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

var files;
$("#addFiles").change(function(e){
  var files = this.files;

  e.stopPropagation();
  e.preventDefault();
  var form = document.getElementById("addFiles");
  var data = new FormData(form);
  data.append("mon", $("#monSelect").val());
  data.append("date", $("#date").val());

  //console.log(data);
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
      
      var element = $.parseJSON(respond);
      //console.log(element);
      $(".add-element").before('<div class="element '+addClass(element['type'])+'" data-title="'+element["fileName"]+'"><img src="files/thumbnails/'+element["fileName"]+'.jpg"></div>');
      items.add([
        {id:element["fileId"], content: element["fileName"], editable:true, start: getTime("workDate")}
        ]);
      
      
      //var ids = timeline.getIds();
      //console.log(ids);
      //*/
      
    },
    error:function(jqXHR, textStatus, errorThrown){
      console.log("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
    }
  })

})




//------end of add files