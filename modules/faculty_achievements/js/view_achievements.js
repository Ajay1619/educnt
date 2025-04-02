$(document).ready(function() {
    // Show modal on edit icon click
    $('.edit-icon').on('click', function() {
        $('#edit-modal').fadeIn();
    });

    // Close modal when 'X' is clicked
    $('.close').on('click', function() {
        $('#edit-modal').fadeOut();
    });

    // Close modal when clicking outside of the modal content
    $(window).on('click', function(event) {
        if ($(event.target).is('#edit-modal')) {
            $('#edit-modal').fadeOut();
        }
    });

    // Additional JS for form validation (already mentioned in your script)
    var allowedExtensions = /(\.pdf|\.doc|\.docx)$/i;
    $('form').on('submit', function(e) {
        var fileInput = $('#file-upload');
        var filePath = fileInput.val();

        if (!allowedExtensions.exec(filePath)) {
            alert('Please upload a file with .pdf, .doc, or .docx extensions.');
            fileInput.val(''); // Clear the input field
            e.preventDefault(); // Prevent form submission
        }
    });
});
