<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Shopify Calendar App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <div class="w-3/4 p-4">
            <h1 class="text-2xl font-bold mb-4">Calendar</h1>
            <div id='calendar'></div>
        </div>
        <div class="w-1/4 bg-white p-4 shadow-md">
            <h2 class="text-xl font-bold mb-4">Upcoming Events</h2>
            <div id="upcoming-events"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode(array_map(function($event) {
                    return [
                        'title' => $event['title'],
                        'start' => $event['start_date'],
                        'end' => $event['end_date'],
                        'url' => "/event/edit/{$event['id']}"
                    ];
                }, $events)); ?>,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });
            calendar.render();

            // Display upcoming events in sidebar
            var upcomingEvents = <?php echo json_encode($upcomingEvents); ?>;
            var upcomingEventsHtml = '';
            upcomingEvents.forEach(function(event) {
                var eventDate = new Date(event.start_date);
                var today = new Date();
                var diffDays = Math.ceil((eventDate - today) / (1000 * 60 * 60 * 24));
                
                upcomingEventsHtml += `
                    <div class="mb-4 p-2 bg-blue-100 rounded">
                        <h3 class="font-bold">${event.title}</h3>
                        <p>In ${diffDays} day(s)</p>
                        <p>${eventDate.toLocaleDateString()}</p>
                    </div>
                `;
            });
            document.getElementById('upcoming-events').innerHTML = upcomingEventsHtml;
        });
    </script>
</body>
</html>