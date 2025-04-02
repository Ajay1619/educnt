
$(document).ready(function () {
    // Section 2: Chip Input Handling
    // Helper function to add a chip
    function addChip(input, container) {
        const chipText = input.value.trim();
        if (chipText) {
            const chip = document.createElement('div');
            chip.classList.add('chip');
            chip.innerHTML = `<span>${chipText}</span><i class="fas fa-times"></i><input type="hidden" name="">`;
            container.appendChild(chip);
            input.value = ''; // Clear the input
        }
    }

    // Function to initialize chip functionality for inputs
    function initChips(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);

        // Ensure the input and container exist before adding event listeners
        if (!input || !container) {
            console.error(`Error: Missing element for input or container (inputId: ${inputId}, containerId: ${containerId})`);
            return;
        }

        // Add event listener for adding a chip on 'Enter' key
        input.addEventListener('keydown', (e) => {
            if (e.key == 'Enter') {
                addChip(input, container);
            }
        });

        // Add event listener for removing a chip
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('fa-times')) {
                e.target.parentElement.remove(); // Remove the chip
            }
        });
    }

    // Initialize chip functionality for different input fields
    // initChips('skills_input', 'skills-chips');
    // initChips('software_skills_input', 'software-skills-chips');
    // initChips('interest_input', 'interest-chips');
    // initChips('languages_input', 'languages-chips');
});
