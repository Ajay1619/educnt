// let events = [
//     {
//       event_id: 1,
//       sem_duration_id: "SEM001",
//       event_name: "Orientation",
//       event_description: "Orientation program for new students",
//       event_start_date: "2024-11-10",
//       event_end_date: "2024-11-12",
//       event_type: "Academic",
//     },
//     {
//       event_id: 2,
//       sem_duration_id: "SEM002",
//       event_name: "Tech Fest",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//     {
//       event_id: 3,
//       sem_duration_id: "SEM002",
//       event_name: "Farewell",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//     {
//       event_id: 4,
//       sem_duration_id: "SEM002",
//       event_name: "Farewell",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//     {
//       event_id: 4,
//       sem_duration_id: "SEM002",
//       event_name: "Farewell",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//     {
//       event_id: 4,
//       sem_duration_id: "SEM002",
//       event_name: "Farewell",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//     {
//       event_id: 4,
//       sem_duration_id: "SEM002",
//       event_name: "Farewell",
//       event_description: "Annual technology festival",
//       event_start_date: "2024-12-15",
//       event_end_date: "2024-12-17",
//       event_type: "Cultural",
//     },
//   ];
  
//   const academic_calendar = () => {
//     const daysInMonth = (date) =>
//       new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
//     const getFirstDayOfMonth = (date) =>
//       new Date(date.getFullYear(), date.getMonth(), 1).getDay();
  
//     const currentMonth = document.querySelector(".current-month");
//     const calendarGrid = document.querySelector(".calendar-grid");
//     const prevMonthButton = document.getElementById("prev-month");
//     const nextMonthButton = document.getElementById("next-month");
//     let currentDate = new Date();
  
//     const formatDate = (date) => {
//       const d = new Date(date);
//       const day = String(d.getDate()).padStart(2, "0");
//       const month = String(d.getMonth() + 1).padStart(2, "0");
//       const year = d.getFullYear();
//       return `${day}.${month}.${year}`;
//     };
  
//     const updateCalendar = (date) => {
//       currentMonth.textContent = date.toLocaleDateString("default", {
//         month: "long",
//         year: "numeric",
//       });
//       calendarGrid.innerHTML = "";
  
//       const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
//       daysOfWeek.forEach((day) => {
//         const dayName = document.createElement("div");
//         dayName.classList.add("day-name");
//         dayName.textContent = day;
//         calendarGrid.appendChild(dayName);
//       });
  
//       const totalDays = daysInMonth(date);
//       const firstDayOfMonth = getFirstDayOfMonth(date);
  
//       for (let i = 0; i < firstDayOfMonth; i++) {
//         const emptyCell = document.createElement("div");
//         emptyCell.classList.add("calendar-day", "empty");
//         calendarGrid.appendChild(emptyCell);
//       }
  
//       for (let i = 1; i <= totalDays; i++) {
//         const day = document.createElement("div");
//         day.classList.add("calendar-day");
//         day.textContent = i;
  
//         const currentDay = new Date(date.getFullYear(), date.getMonth(), i);
//         const formattedDate = formatDate(currentDay);
  
//         // Mark Sundays as holidays
//         if (currentDay.getDay() === 0) {
//           day.classList.add("holiday");
//         }
  
//         // Check for events
//         const dayEvents = events.filter((event) => {
//           const startDate = new Date(event.event_start_date);
//           const endDate = new Date(event.event_end_date);
//           return currentDay >= startDate && currentDay <= endDate;
//         });
  
//         if (dayEvents.length > 0) {
//           let eventCount = 0;
//           dayEvents.forEach((event, index) => {
//             if (eventCount < 3) {
//               const eventLabel = document.createElement("div");
//               eventLabel.classList.add("event-label");
//               eventLabel.textContent = event.event_name;
//               day.appendChild(eventLabel);
//               eventCount++;
//             }
//           });
  
//           if (dayEvents.length > 3) {
//             const moreEventsLabel = document.createElement("div");
//             moreEventsLabel.classList.add("more-event-labels", "more-events");
//             moreEventsLabel.textContent = `+${dayEvents.length - 3} events`;
//             day.appendChild(moreEventsLabel);
//           }
  
//           day.classList.add("has-event");
//         }
  
//         calendarGrid.appendChild(day);
//       }
//     };
  
//     prevMonthButton.addEventListener("click", () => {
//       currentDate.setMonth(currentDate.getMonth() - 1);
//       updateCalendar(currentDate);
//     });
  
//     nextMonthButton.addEventListener("click", () => {
//       currentDate.setMonth(currentDate.getMonth() + 1);
//       updateCalendar(currentDate);
//     });
  
//     updateCalendar(currentDate);
//   };
  
//   academic_calendar();
  
  