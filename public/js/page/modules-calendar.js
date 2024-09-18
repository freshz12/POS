$(document).ready(function() {
  $("#myEvent").fullCalendar({
      height: 'auto',
      header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay,listWeek'
      },
      editable: true,
      events: [
          {
              title: 'Conference',
              start: '2024-09-09',
              end: '2024-09-11',
              backgroundColor: "#fff",
              borderColor: "#fff",
              textColor: '#000'
          },
          {
              title: "John's Birthday",
              start: '2024-09-14',
              backgroundColor: "#007bff",
              borderColor: "#007bff",
              textColor: '#fff'
          },
          {
              title: 'Reporting',
              start: '2024-09-10T11:30:00',
              backgroundColor: "#f56954",
              borderColor: "#f56954",
              textColor: '#fff'
          },
          {
              title: 'Starting New Project',
              start: '2024-09-11',
              backgroundColor: "#ffc107",
              borderColor: "#ffc107",
              textColor: '#fff'
          },
          {
              title: 'Social Distortion Concert',
              start: '2024-09-13 22:27:54',
              end: '2024-09-28 22:27:54',
              backgroundColor: "#000",
              borderColor: "#000",
              textColor: '#fff'
          },
          {
              title: 'Lunch',
              start: '2024-09-24T13:15:00',
              backgroundColor: "#fff",
              borderColor: "#d4d4d4",
              textColor: '#000',
          },
          {
              title: 'Company Tripar',
              start: '2024-09-28T13:15:00',
              end: '2024-09-30T18:15:00',
              backgroundColor: "#fff",
              borderColor: "#red",
              textColor: '#000',
          },
      ],
      eventRender: function(event, element) {
          // Generate tooltip content
          var tooltipContent = event.title + '<br>' +
                                moment(event.start).format('YYYY-MM-DD HH:mm:ss') + '<br>' +
                                (event.end ? '<strong>End:</strong> ' + moment(event.end).format('YYYY-MM-DD HH:mm:ss') : '');

          // Initialize tooltip
          element.attr('title', tooltipContent);
          element.tooltip({
              track: true,
              content: tooltipContent,
              open: function(event, ui) {
                  ui.tooltip.css({
                      'max-width': '200px'
                  });
              }
          });
      }
  });
});