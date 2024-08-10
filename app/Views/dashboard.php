<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Dashboard - Shopify App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/list@5.10.2/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.10.2/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/list@5.10.2/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.10.2/main.min.js'></script>
    <style>
        .fc-dayGridYear-view .fc-daygrid-day-frame {
            min-height: 1.5em;
        }
        .fc-dayGridYear-view .fc-daygrid-day-top {
            flex-direction: row;
        }
        .fc-dayGridYear-view .fc-daygrid-day-number {
            font-size: 0.8em;
        }
        .fc-dayGridYear-view .fc-daygrid-event {
            font-size: 0.7em;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Calendar Dashboard</h1>
        
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-semibold mb-2">Your Calendar</h2>
            
            <div class="mb-4 flex flex-wrap items-center">
                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                    <input type="text" id="searchInput" placeholder="Search events..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                    <select id="categoryFilter" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Categories</option>
                        <!-- Categories will be populated dynamically -->
                    </select>
                </div>
                <div class="w-full md:w-1/3 px-2">
                    <div class="flex">
                        <input type="date" id="startDateFilter" class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2">
                        <input type="date" id="endDateFilter" class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
            </div>
            
            <div id='calendar'></div>
        </div>

        <a href="/shop-calendar-new/logout" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Logout
        </a>
    </div>

    <!-- Event Creation Modal -->
    <div id="eventModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="eventForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label for="eventTitle" class="block text-gray-700 text-sm font-bold mb-2">Event Title</label>
                            <input type="text" id="eventTitle" name="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="eventDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea id="eventDescription" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="eventStart" class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                            <input type="datetime-local" id="eventStart" name="start_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="eventEnd" class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
                            <input type="datetime-local" id="eventEnd" name="end_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="eventCategory" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                            <select id="eventCategory" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select a category</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="eventRecurrence" class="block text-gray-700 text-sm font-bold mb-2">Recurrence</label>
                            <select id="eventRecurrence" name="recurrence" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="none">None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div id="recurrenceEndDate" class="mb-4 hidden">
                            <label for="eventRecurrenceEnd" class="block text-gray-700 text-sm font-bold mb-2">Recurrence End Date</label>
                            <input type="date" id="eventRecurrenceEnd" name="recurrence_end" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Create Event
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Edit Modal -->
    <div id="editEventModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="id">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <label for="editEventTitle" class="block text-gray-700 text-sm font-bold mb-2">Event Title</label>
                            <input type="text" id="editEventTitle" name="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="editEventDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea id="editEventDescription" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="editEventStart" class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                            <input type="datetime-local" id="editEventStart" name="start_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="editEventEnd" class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
                            <input type="datetime-local" id="editEventEnd" name="end_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="editEventCategory" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                            <select id="editEventCategory" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select a category</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="editEventRecurrence" class="block text-gray-700 text-sm font-bold mb-2">Recurrence</label>
                            <select id="editEventRecurrence" name="recurrence" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="none">None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div id="editRecurrenceEndDate" class="mb-4 hidden">
                            <label for="editEventRecurrenceEnd" class="block text-gray-700 text-sm font-bold mb-2">Recurrence End Date</label>
                            <input type="date" id="editEventRecurrenceEnd" name="recurrence_end" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Event
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                        <button type="button" onclick="deleteEvent()" class="mt-3 w-full inline-flex justify-center rounded-md border border-red-300 shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let calendar;
        let currentEventId;
        let categories = [];
        let allEvents = []; // Store all events

        document.addEventListener('DOMContentLoaded', function() {
            fetchCategories();

            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                views: {
                    dayGridYear: {
                        buttonText: 'Year'
                    },
                    dayGridMonth: {
                        buttonText: 'Month'
                    },
                    timeGridWeek: {
                        buttonText: 'Week'
                    },
                    timeGridDay: {
                        buttonText: 'Day'
                    },
                    listWeek: {
                        buttonText: 'Agenda'
                    }
                },
                events: function(info, successCallback, failureCallback) {
                    fetch('/shop-calendar-new/event/get')
                        .then(response => response.json())
                        .then(data => {
                            allEvents = data; // Store all events
                            successCallback(filterEvents(data));
                        })
                        .catch(error => {
                            console.error('There was an error fetching events:', error);
                            failureCallback(error);
                        });
                },
                eventDidMount: function(info) {
                    console.log('Event mounted:', info.event);
                    if (info.event.extendedProps.category_color) {
                        info.el.style.backgroundColor = info.event.extendedProps.category_color;
                    }
                },
                dateClick: function(info) {
                    openModal(info.dateStr);
                },
                eventClick: function(info) {
                    openEditModal(info.event);
                },
                height: 'auto',
                firstDay: 1,
                weekNumbers: true,
                navLinks: true,
                businessHours: true,
                nowIndicator: true,
                dayMaxEvents: true,
            });
            calendar.render();

            document.getElementById('eventForm').addEventListener('submit', function(e) {
                e.preventDefault();
                createEvent();
            });

            document.getElementById('editEventForm').addEventListener('submit', function(e) {
                e.preventDefault();
                updateEvent();
            });

            document.getElementById('eventRecurrence').addEventListener('change', function() {
                toggleRecurrenceEndDate(this, 'recurrenceEndDate');
            });

            document.getElementById('editEventRecurrence').addEventListener('change', function() {
                toggleRecurrenceEndDate(this, 'editRecurrenceEndDate');
            });

            // Add event listeners for search and filter inputs
            document.getElementById('searchInput').addEventListener('input', debounce(updateEvents, 300));
            document.getElementById('categoryFilter').addEventListener('change', updateEvents);
            document.getElementById('startDateFilter').addEventListener('change', updateEvents);
            document.getElementById('endDateFilter').addEventListener('change', updateEvents);
        });

        function updateEvents() {
            calendar.refetchEvents();
        }

        function filterEvents(events) {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const startDateFilter = document.getElementById('startDateFilter').value;
            const endDateFilter = document.getElementById('endDateFilter').value;

            return events.filter(event => {
                // Search filter
                if (searchTerm && !event.title.toLowerCase().includes(searchTerm)) {
                    return false;
                }

                // Category filter
                if (categoryFilter && event.extendedProps.category_id != categoryFilter) {
                    return false;
                }

                // Date range filter
                if (startDateFilter && event.start < startDateFilter) {
                    return false;
                }
                if (endDateFilter && event.end > endDateFilter) {
                    return false;
                }

                return true;
            });
        }

        function toggleRecurrenceEndDate(select, endDateDivId) {
            const endDateDiv = document.getElementById(endDateDivId);
            if (select.value !== 'none') {
                endDateDiv.classList.remove('hidden');
            } else {
                endDateDiv.classList.add('hidden');
            }
        }

        function fetchCategories() {
            fetch('/shop-calendar-new/event/get-categories')
                .then(response => response.json())
                .then(data => {
                    categories = data;
                    populateCategoryDropdowns();
                })
                .catch(error => console.error('Error fetching categories:', error));
        }

        function populateCategoryDropdowns() {
            const eventCategorySelect = document.getElementById('eventCategory');
            const editEventCategorySelect = document.getElementById('editEventCategory');
            const categoryFilterSelect = document.getElementById('categoryFilter');
            
            categories.forEach(category => {
                const option = new Option(category.name, category.id);
                eventCategorySelect.add(option.cloneNode(true));
                editEventCategorySelect.add(option.cloneNode(true));
                categoryFilterSelect.add(option.cloneNode(true));
            });
        }

        function openModal(date) {
            document.getElementById('eventTitle').value = '';
            document.getElementById('eventDescription').value = '';
            document.getElementById('eventStart').value = date + 'T00:00';
            document.getElementById('eventEnd').value = date + 'T23:59';
            document.getElementById('eventCategory').value = '';
            document.getElementById('eventRecurrence').value = 'none';
            document.getElementById('eventRecurrenceEnd').value = '';
            document.getElementById('recurrenceEndDate').classList.add('hidden');
            document.getElementById('eventModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('eventTitle').value = '';
            document.getElementById('eventDescription').value = '';
            document.getElementById('eventStart').value = '';
            document.getElementById('eventEnd').value = '';
            document.getElementById('eventCategory').value = '';
            document.getElementById('eventRecurrence').value = 'none';
            document.getElementById('eventRecurrenceEnd').value = '';
            document.getElementById('recurrenceEndDate').classList.add('hidden');
            document.getElementById('eventModal').classList.add('hidden');
        }

        function createEvent() {
            var form = document.getElementById('eventForm');
            var formData = new FormData(form);

            // Convert dates to ISO format, preserving the local time
            var startDate = new Date(formData.get('start_date'));
            var endDate = new Date(formData.get('end_date'));
            formData.set('start_date', formatDateTimeISO(startDate));
            formData.set('end_date', formatDateTimeISO(endDate));

            // Handle recurrence end date
            if (formData.get('recurrence') !== 'none' && formData.get('recurrence_end')) {
                var recurrenceEndDate = new Date(formData.get('recurrence_end'));
                formData.set('recurrence_end', formatDateISO(recurrenceEndDate));
            }

            fetch('/shop-calendar-new/event/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event created successfully');
                    closeModal();
                    calendar.refetchEvents();
                } else {
                    alert('Failed to create event: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the event. Please check the console for more details.');
            });
        }

        function openEditModal(event) {
            currentEventId = event.id;
            document.getElementById('editEventId').value = event.id;
            document.getElementById('editEventTitle').value = event.title;
            document.getElementById('editEventDescription').value = event.extendedProps.description || '';
            
            // Format the start date
            let startDate = event.start;
            document.getElementById('editEventStart').value = formatDateTimeLocal(startDate, false);
            
            // Format the end date
            let endDate = event.end || event.start; // If there's no end date, use the start date
            document.getElementById('editEventEnd').value = formatDateTimeLocal(endDate, false);
            
            document.getElementById('editEventCategory').value = event.extendedProps.category_id || '';
            document.getElementById('editEventRecurrence').value = event.extendedProps.recurrence || 'none';
            
            if (event.extendedProps.recurrence_end) {
                document.getElementById('editEventRecurrenceEnd').value = formatDateISO(new Date(event.extendedProps.recurrence_end));
                document.getElementById('editRecurrenceEndDate').classList.remove('hidden');
            } else {
                document.getElementById('editEventRecurrenceEnd').value = '';
                document.getElementById('editRecurrenceEndDate').classList.add('hidden');
            }
            
            document.getElementById('editEventModal').classList.remove('hidden');
        }

        function formatDateTimeLocal(date, useUTC = true) {
            const year = useUTC ? date.getUTCFullYear() : date.getFullYear();
            const month = useUTC ? date.getUTCMonth() + 1 : date.getMonth() + 1;
            const day = useUTC ? date.getUTCDate() : date.getDate();
            const hours = useUTC ? date.getUTCHours() : date.getHours();
            const minutes = useUTC ? date.getUTCMinutes() : date.getMinutes();

            return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}T${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        }

        function closeEditModal() {
            document.getElementById('editEventModal').classList.add('hidden');
        }

        function updateEvent() {
            var form = document.getElementById('editEventForm');
            var formData = new FormData(form);

            // Convert dates to ISO format, preserving the local time
            var startDate = new Date(formData.get('start_date'));
            var endDate = new Date(formData.get('end_date'));
            formData.set('start_date', formatDateTimeISO(startDate));
            formData.set('end_date', formatDateTimeISO(endDate));

            // Handle empty category_id
            if (formData.get('category_id') === '') {
                formData.set('category_id', 'null');
            }

            // Handle recurrence end date
            if (formData.get('recurrence') !== 'none' && formData.get('recurrence_end')) {
                var recurrenceEndDate = new Date(formData.get('recurrence_end'));
                formData.set('recurrence_end', formatDateISO(recurrenceEndDate));
            } else {
                formData.set('recurrence_end', 'null'); // Set to 'null' string if empty or recurrence is 'none'
            }

            fetch('/shop-calendar-new/event/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event updated successfully');
                    closeEditModal();
                    calendar.refetchEvents();
                } else {
                    alert('Failed to update event: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the event. Please check the console for more details.');
            });
        }

        function formatDateTimeISO(date) {
            const offset = date.getTimezoneOffset();
            const adjustedDate = new Date(date.getTime() - offset * 60 * 1000);
            return adjustedDate.toISOString().slice(0, 19).replace('T', ' ');
        }

        function formatDateISO(date) {
            return date.toISOString().split('T')[0];
        }

        function deleteEvent() {
            if (confirm('Are you sure you want to delete this event?')) {
                var formData = new FormData();
                formData.append('id', currentEventId);

                fetch('/shop-calendar-new/event/delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Event deleted successfully');
                        closeEditModal();
                        calendar.refetchEvents();
                    } else {
                        alert('Failed to delete event: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the event. Please check the console for more details.');
                });
            }
        }

        // Debounce function to limit how often a function is called
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>