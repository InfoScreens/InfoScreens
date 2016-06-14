//------timeline script

var container = document.getElementById("timeline");


  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
    {id: 1, content: 'A', start: '2015-02-09T04:00:00'},
    {id: 2, content: 'B', start: '2015-02-09T14:00:00'},
    {id: 3, content: 'C', start: '2015-02-09T16:00:00'},
    {id: 4, content: 'D', start: '2015-02-09T17:00:00'},
    {id: 5, content: 'E', start: '2015-02-10T03:00:00'}
  ]);

  // Configuration for the Timeline
  var options = {
    editable: true,

    // always snap to full hours, independent of the scale
    snap: function (date, scale, step) {
      var hour = 60 * 60 * 1000;
      return Math.round(date / hour) * hour;
    }

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