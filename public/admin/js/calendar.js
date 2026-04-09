document.addEventListener('DOMContentLoaded', function() {
  const monthYearEl = document.getElementById('month-year');
  const daysEl = document.getElementById('days');
  const prevMonthBtn = document.getElementById('prev-month');
  const nextMonthBtn = document.getElementById('next-month');
  const todayBtn = document.getElementById('today-btn');
  const eventDateEl = document.getElementById('event-date');
  const eventListEl = document.getElementById('event-list');
  
  let currentDate = new Date();
  let selectedDate = null;
  
  // Sample events data
  const events = {
    '2025-9-15': [
      { time: '10:00 AM', text: 'Team meeting' },
      { time: '02:30 PM', text: 'Project review' }
    ],
    '2025-9-20': [
      { time: '11:00 AM', text: 'Doctor appointment' }
    ],
    '2025-9-25': [
      { time: '07:00 PM', text: 'Birthday party' },
      { time: '09:00 PM', text: 'Dinner with friends' }
    ],
    '2025-10-2': [
      { time: '03:00 PM', text: 'Conference call' }
    ],
    '2025-10-10': [
      { time: 'All day', text: 'Project deadline' }
    ],
    '2025-10-18': [
      { time: '12:00 PM', text: 'Lunch with client' },
      { time: '04:00 PM', text: 'Product demo' }
    ]
  };
  
  // Render calendar
  function renderCalendar() {
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const prevLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
    const firstDayIndex = firstDay.getDay();
    const lastDayIndex = lastDay.getDay();
    const nextDays = 7 - lastDayIndex - 1;
    
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    monthYearEl.innerHTML = `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    
    let days = "";
    
    // Previous month days
    for (let x = firstDayIndex; x > 0; x--) {
      const prevDate = prevLastDay.getDate() - x + 1;
      days += `<div class="text-gray-400 text-center p-2">${prevDate}</div>`;
    }
    
    // Current month days
    for (let i = 1; i <= lastDay.getDate(); i++) {
      const dateKey = `${currentDate.getFullYear()}-${currentDate.getMonth() + 1}-${i}`;
      const isToday = i === new Date().getDate() &&
                      currentDate.getMonth() === new Date().getMonth() &&
                      currentDate.getFullYear() === new Date().getFullYear();
      const isSelected = selectedDate &&
                         i === selectedDate.getDate() &&
                         currentDate.getMonth() === selectedDate.getMonth() &&
                         currentDate.getFullYear() === selectedDate.getFullYear();
      
      let classes = "cursor-pointer flex items-center justify-center rounded-full p-2 transition";
      if (isToday) classes += " bg-pink-400 text-white font-bold";
      else if (isSelected) classes += " bg-indigo-400 text-white font-bold";
      else classes += " hover:bg-blue-100";
      
      // Event indicator
      if (events[dateKey]) {
        classes += " relative";
        days += `<div class="${classes}" data-date="${dateKey}">${i}<span class="absolute bottom-1 w-1.5 h-1.5 bg-teal-400 rounded-full"></span></div>`;
      } else {
        days += `<div class="${classes}" data-date="${dateKey}">${i}</div>`;
      }
    }
    
    // Next month days
    for (let j = 1; j <= nextDays; j++) {
      days += `<div class="text-gray-400 text-center p-2">${j}</div>`;
    }
    
    daysEl.innerHTML = days;
    
    // Add click event to days
    document.querySelectorAll('#days div[data-date]').forEach(day => {
      day.addEventListener('click', () => {
        const dateStr = day.getAttribute('data-date');
        const [year, month, dayNum] = dateStr.split('-').map(Number);
        selectedDate = new Date(year, month - 1, dayNum);
        renderCalendar();
        showEvents(dateStr);
      });
    });
  }
  
  // Show events for selected date
  function showEvents(dateStr) {
    const [year, month, day] = dateStr.split('-').map(Number);
    const dateObj = new Date(year, month - 1, day);
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    eventDateEl.textContent = `${dayNames[dateObj.getDay()]}, ${months[dateObj.getMonth()]} ${day}, ${year}`;
    
    eventListEl.innerHTML = '';
    if (events[dateStr]) {
      events[dateStr].forEach(event => {
        eventListEl.innerHTML += `
          <div class="flex items-center gap-3 bg-gray-100 rounded-lg p-3 mb-2 shadow-sm hover:translate-x-1 transition">
            <div class="w-3 h-3 rounded-full bg-teal-400"></div>
            <div class="font-semibold text-blue-600 min-w-[80px]">${event.time}</div>
            <div class="flex-1">${event.text}</div>
          </div>
        `;
      });
    } else {
      eventListEl.innerHTML = '<div class="text-center text-gray-500 italic p-8">No reservations scheduled for this day</div>';
    }
  }
  
  // Navigation
  prevMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    eventDateEl.textContent = 'Select a date';
    eventListEl.innerHTML = '<div class="text-center text-gray-500 italic p-8">Select a date with reservations to view them here</div>';
  });
  
  nextMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    eventDateEl.textContent = 'Select a date';
    eventListEl.innerHTML = '<div class="text-center text-gray-500 italic p-8">Select a date with reservations to view them here</div>';
  });
  
  todayBtn.addEventListener('click', () => {
    currentDate = new Date();
    selectedDate = new Date();
    renderCalendar();
    const dateStr = `${currentDate.getFullYear()}-${currentDate.getMonth() + 1}-${currentDate.getDate()}`;
    showEvents(dateStr);
  });
  
  // Initialize
  renderCalendar();
});
