$(document).ready(function() {
    var achievementSuggestions = [
        { title: 'Best Performance' },
        { title: 'Most Improved', id: '002' },
        { title: 'Employee of the Month', id: '003' }
    ];

    var topicSuggestions = [
        { title: 'Technology' },
        { title: 'Business', id: '004' },
        { title: 'Marketing', id: '005' }
    ];
    var Venuelocation = [
        { title: 'Pondicherry' },
        { title: 'Chennai', id: '006' },
        { title: 'Trichy', id: '007' }
    ];

    function showSuggestions(suggestions, container, inputField) {
        container.empty(); // Clear previous suggestions

        if (suggestions.length == 0) return;

        suggestions.forEach(function(suggestion) {
            var suggestionElement = $(
                '<div class="autocomplete-suggestion">' +
                    '<span class="movie-title">' + suggestion.title + '</span>' +
                    (suggestion.id ? '<span class="movie-id">' + suggestion.id + '</span>' : '') +
                '</div>'
            );
            container.append(suggestionElement);

            // Click event for each suggestion
            suggestionElement.on('click', function() {
                inputField.val(suggestion.title); // Set the selected value
                container.empty(); // Clear suggestions after selection
            });
        });
    }

    // Show suggestions for Achievement Type on click
    $('#achievement-type').on('click', function() {
        showSuggestions(achievementSuggestions, $('#achievement-type-suggestions'), $('#achievement-type'));
    });

    // Show suggestions for Topic on click
    $('#topic').on('click', function() {
        showSuggestions(topicSuggestions, $('#topic-suggestions'), $('#topic'));
    });
    $('#venue').on('click', function() {
        showSuggestions(Venuelocation, $('#Venue-location-achievement'), $('#venue'));
    });

    // Close suggestions when clicking outside the input
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.input-container').length) {
            $('.autocomplete-suggestions').empty();
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        var fileInput = document.getElementById('file-upload');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.pdf|\.doc|\.docx)$/i;
        
        if (!allowedExtensions.exec(filePath)) {
            alert('Please upload a file with .pdf, .doc, or .docx extensions.');
            fileInput.value = ''; // Clear the input field
            e.preventDefault(); // Prevent form submission
        }
    });
});