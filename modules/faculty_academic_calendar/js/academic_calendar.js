const academic_calendar = () => {
  const daysInMonth = (date) =>
      new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
  const getFirstDayOfMonth = (date) =>
      new Date(date.getFullYear(), date.getMonth(), 1).getDay();

  const currentMonth = document.querySelector(".current-month");
  const calendarGrid = document.querySelector(".calendar-grid");
  const prevMonthButton = document.getElementById("prev-month");
  const nextMonthButton = document.getElementById("next-month");
  let currentDate = new Date();

  const updateCalendar = (date) => {
      currentMonth.textContent = date.toLocaleDateString("default", {
          month: "long",
          year: "numeric",
      });
      calendarGrid.innerHTML = "";

      const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      daysOfWeek.forEach((day) => {
          const dayName = document.createElement("div");
          dayName.classList.add("day-name");
          dayName.textContent = day;
          calendarGrid.appendChild(dayName);
      });

      const totalDays = daysInMonth(date);
      const firstDayOfMonth = getFirstDayOfMonth(date);

      for (let i = 0; i < firstDayOfMonth; i++) {
          const emptyCell = document.createElement("div");
          emptyCell.classList.add("calendar-day", "empty");
          calendarGrid.appendChild(emptyCell);
      }

      for (let i = 1; i <= totalDays; i++) {
          const day = document.createElement("div");
          day.classList.add("calendar-day");

          const dayOfWeek = new Date(
              date.getFullYear(),
              date.getMonth(),
              i
          ).getDay();

          day.dataset.date = `${date.getFullYear()}-${date.getMonth() + 1}-${i}`;
          day.dataset.day = daysOfWeek[dayOfWeek];
          day.dataset.year = date.getFullYear();

          day.textContent = i;

          if (dayOfWeek === 0) day.classList.add("holiday");

          // Drag-and-drop event listeners
          day.addEventListener("dragover", (e) => {
              e.preventDefault();
              day.classList.add("dragover");
          });

          day.addEventListener("dragleave", () => {
              day.classList.remove("dragover");
          });

          day.addEventListener("drop", (e) => {
              handleDropEvent(e, day);
          });

          calendarGrid.appendChild(day);
      }
  };

  // Attach dragstart to draggable events
  const makeEventsDraggable = () => {
      document.querySelectorAll(".event").forEach((event) => {
          event.setAttribute("draggable", true);
          event.addEventListener("dragstart", (e) => {
              e.dataTransfer.setData("text", e.target.dataset.id || e.target.textContent);
          });
      });
  };

  const handleDropEvent = (e, day) => {
      e.preventDefault();
      day.classList.remove("dragover");

      const data = e.dataTransfer.getData("text");
      const originalEvent = document.querySelector(`[data-id="${data}"]`) || 
                            document.querySelector(`.event:contains("${data}")`);
      if (!originalEvent) return;

      const clonedEvent = originalEvent.cloneNode(true);
      clonedEvent.classList.add("cloned_events");

      addCloseButton(clonedEvent);

      appendEventToDay(day, clonedEvent);
  };

  const appendEventToDay = (day, event) => {
      const eventList = day.querySelector(".event-list") || createEventList(day);

      if (eventList.children.length < 3) {
          eventList.appendChild(event);
      } else {
          const extraCount = day.querySelector(".extra-events-count");
          if (!extraCount) {
              const extra = document.createElement("div");
              extra.classList.add("extra-events-count");
              extra.textContent = `+1`;
              day.appendChild(extra);
          } else {
              const count = parseInt(extraCount.textContent.replace("+", ""), 10) + 1;
              extraCount.textContent = `+${count}`;
          }
      }

      day.classList.add("has-event");
  };

  const createEventList = (day) => {
      const list = document.createElement("div");
      list.classList.add("event-list");
      day.appendChild(list);
      return list;
  };

  const addCloseButton = (event) => {
      const closeBtn = document.createElement("button");
      closeBtn.classList.add("close-button");
      closeBtn.textContent = "×";

      closeBtn.addEventListener("click", () => {
          const confirmation = confirm("Are you sure you want to delete this event?");
          if (confirmation) {
              const eventDay = event.closest(".calendar-day");
              event.remove();
              if (eventDay && eventDay.querySelectorAll(".event").length === 0) {
                  eventDay.classList.remove("has-event");
                  eventDay.querySelector(".extra-events-count")?.remove();
              }
          }
      });

      event.appendChild(closeBtn);
  };

  prevMonthButton.addEventListener("click", () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      updateCalendar(currentDate);
  });

  nextMonthButton.addEventListener("click", () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      updateCalendar(currentDate);
  });

  updateCalendar(currentDate);
  makeEventsDraggable();
};

  
  
  // Function to initialize the custom right-click menu
  const initCustomContextMenuForCalendar = () => {
    const menu = document.createElement("div");
    menu.classList.add("custom-context-menu");
    menu.innerHTML = `
        <ul>
            <li id="add-events-popup" data-action="add-event"> Add-events
</li>
            <li data-action="edit-event">Edit Event</li>
            <li data-action="delete-event">Delete Events</li>
        </ul>
    `;
    document.body.appendChild(menu);
  
    let isMenuOpen = false; // Track if the menu is open
    let lastClickedDate = null; // Track the last clicked date
  
    // Add event listener to calendar date cells
    document.querySelectorAll(".calendar-day").forEach((day) => {
        day.addEventListener("contextmenu", (e) => {
            e.preventDefault(); // Disable default right-click menu
  
            const currentDate = day.dataset.date;
  
            // If the menu is open for a different date, close it
            if (isMenuOpen && currentDate !== lastClickedDate) {
                menu.style.display = "none";
            }
  
            // Show the custom menu at the cursor's position
            menu.style.display = "block";
            menu.style.left = `${e.pageX}px`;
            menu.style.top = `${e.pageY}px`;
  
            // Attach the date to the menu for contextual actions
            menu.dataset.date = currentDate;
  
            // Update the tracking variables
            lastClickedDate = currentDate;
            isMenuOpen = true;
        });
    });
  
    // Handle menu actions
  
    // Hide the menu when clicking outside
    document.addEventListener("click", (e) => {
        const target = e.target;
        if (!menu.contains(target) && !document.querySelector(".calendar-day").contains(target)) {
            menu.style.display = "none";
            isMenuOpen = false;
        }
    });
  
    // Optional: Hide the menu on escape key press
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            menu.style.display = "none";
            isMenuOpen = false;
        }
    });
  };
  $(document).on('click', '#add-events-popup', async function () {
    console.log("hello");

    try {
        await load_academic_calendar_add_event_popup();
    } catch (error) {
        console.error('Error loading Add Event popup:', error);
    }
});

  // Call this function after the calendar is initialized
  initCustomContextMenuForCalendar();
  
  const calendar_popup_event = () => {
    // Hide all sub-event input fields initially
    $(".sub-event-name").hide().attr("placeholder", ""); // Clear the placeholder initially

    // Handle checkbox change event
    $(".class-checkbox").change(function () {
        // Define the checkbox and its associated sub-event input field
        const $checkbox = $(this);
        const $subEventInput = $checkbox
            .closest(".col") // Locate the column container
            .find(".sub-event-name"); // Find the corresponding sub-event input

        // Toggle the input field and placeholder visibility based on checkbox state
        if ($checkbox.is(":checked")) {
            $subEventInput
                .show() // Display the input field
                .prop("required", true) // Make it required
                .attr("placeholder", "Enter the Sub-Event Name"); // Set placeholder from label text
        } else {
            $subEventInput
                .hide() // Hide the input field
                .prop("required", false) // Remove required property
                .attr("placeholder", ""); // Clear placeholder
        }
    });
};

const calendar_popup_bulma_time = () => {
    // Attach Bulma Calendar to the inputs
    const fromTimeCalendar = bulmaCalendar.attach('#from-time', {
        type: 'time',         // Time picker
        dateFormat: 'HH:mm',  // 24-hour format
        minuteSteps: 1        // 1-minute increments
    });

    const toTimeCalendar = bulmaCalendar.attach('#to-time', {
        type: 'time',         // Time picker
        dateFormat: 'HH:mm',  // 24-hour format
        minuteSteps: 1        // 1-minute increments
    });

    // Function to synchronize Bulma Calendar selections with input fields
    const updateTimeField = (calendarInstance, fieldSelector) => {
        calendarInstance.forEach(calendar => {
            calendar.on('select', () => {
                const selectedTime = calendar.value;
                document.querySelector(fieldSelector).value = selectedTime; // Vanilla JS approach
            });
        });
    };

    // Bind calendar values to input fields
    if (fromTimeCalendar) updateTimeField(fromTimeCalendar, '#from-time');
    if (toTimeCalendar) updateTimeField(toTimeCalendar, '#to-time');
};

// Initialize the calendar on DOM load
document.addEventListener('DOMContentLoaded', calendar_popup_bulma_time);

