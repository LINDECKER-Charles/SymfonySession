document.addEventListener('turbo:load', function () {
    console.log('DOM loaded');
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.warn('#calendar non trouvé');
        return;
    }
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: '/events'
    });
    console.log('Calendrier prêt');
    calendar.render();
});