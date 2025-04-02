const accordion_events_calendar = () => {
    const accordionHeaders = document.querySelectorAll(".accordion-header");

    // Toggle accordion content
    accordionHeaders.forEach((header) => {
        header.addEventListener("click", () => {
            header.classList.toggle("active");
            const content = header.nextElementSibling;
            const isVisible = content.style.display === "block";

            // Close all accordion contents
            document.querySelectorAll(".accordion-content").forEach((item) => {
                item.style.display = "none";
                item.previousElementSibling.classList.remove("active");
            });

            // Toggle current accordion content
            if (!isVisible) content.style.display = "block";

            // Reinitialize drag events after toggling accordion
            initializeDragEvents();
        });
    });

    initializeDragEvents(); // Initialize events at page load
};

const initializeDragEvents = () => {
    const events = document.querySelectorAll(".event");
    events.forEach((event, index) => {
        event.dataset.id = index;

        // Enable drag functionality
        event.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text", event.dataset.id);
        });
    });
};
