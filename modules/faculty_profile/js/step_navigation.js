$(document).ready(function () {
    // Section 1: Navigation between Steps and Tabs
    let currentStep = 0; // Tracks the current step
    let currentTab = 0; // Tracks the current tab within the current step

    const steps = $(".step");
    const stepContents = $(".step-content");
    const tabNav = $(".tab-nav");

    // Tab data for each step
    const tabData = [
        ["Personal Details", "Contact Details", "Address Details"], // Step 0
        ["SSLC Education", "HSC Education", "Diploma Education", "UG Education", "PG Education"], // Step 1
        ["Industry Experience", "Institution Experience"], // Step 2
        ["Skill Knowledge"], // Step 3
        ["Upload Files"], // Step 4
    ];

    // Function to update tabs when navigating between steps
    function updateTabs(step) {
        tabNav.html(""); // Clear existing tabs
        tabData[step].forEach((tabName, index) => {
            tabNav.append(`<button class="tab-btn ${index == currentTab ? 'active' : ''}" data-tab="${index}">${tabName}</button>`);
        });
    }

    // Function to show the current step and reset tabs
    function showStep(step) {
        steps.removeClass("active").eq(step).addClass("active");
        stepContents.removeClass("active").eq(step).addClass("active");
        currentTab = 0; // Reset to first tab of the new step
        updateTabs(step);
        showTab(currentTab);
    }

    // Function to show the current tab within a step
    function showTab(tab) {
        $(".tab-btn").removeClass("active").eq(tab).addClass("active");
        const currentTabContents = stepContents.eq(currentStep).find(".tab-content");
        currentTabContents.removeClass("active").eq(tab).addClass("active");
    }

    // Event listener for the Next button
    $(".next-btn").on("click", function () {
        if (currentTab < tabData[currentStep].length - 1) {
            currentTab++;
            showTab(currentTab);
        } else if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    // Event listener for the Previous button
    $(".prev-btn").on("click", function () {
        if (currentTab > 0) {
            currentTab--;
            showTab(currentTab);
        } else if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
            currentTab = tabData[currentStep].length - 1; // Go to last tab of previous step
            updateTabs(currentStep);
            showTab(currentTab);
        }
    });

    // Event listener for tab navigation
    tabNav.on("click", ".tab-btn", function () {
        currentTab = $(this).data("tab");
        showTab(currentTab);
    });

    // Initialize the first step and tab on page load
    showStep(currentStep);
});
