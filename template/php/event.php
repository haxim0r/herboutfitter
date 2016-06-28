<script src='/js/moment.min.js'></script>
<script src='/js/fullcalendar.js'></script>
    
<div id="hotspot_right"></div>

<script>
$('#hotspot_right').fullCalendar({
    
    // put your options and callbacks here
    events: '?ajax=fullcalendar',

    eventClick: function(calEvent, jsEvent, view) {

        console.log('Event: ' + calEvent.title);
        console.log('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
        console.log('View: ' + view.name);

        // change the border color just for fun
        $(this).css('border-color', 'red');

    }
});
</script>
