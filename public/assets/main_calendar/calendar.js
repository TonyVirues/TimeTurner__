


document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');   
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        selectable: true,
        editable: true,
        events: []
    });

    calendar.render();
});