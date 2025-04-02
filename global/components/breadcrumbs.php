<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>
    <nav class="breadcrumb">
        <!-- Home icon (static) -->
        <a href="<?= BASEPATH . '/dashboard' ?>"><img src="<?= GLOBAL_PATH . '/images/svgs/breadcrumbs-home.svg' ?>" alt="Home"></a>
        <!-- The rest of the breadcrumb items will be added here using jQuery -->
        <div id="dynamic-breadcrumb"></div>
    </nav>


    <script>
        $(document).ready(function() {
            // Get the current URL path and origin (base URL)
            var path = window.location.pathname;
            var origin = window.location.origin;
            var params = new URLSearchParams(window.location.search);

            // Get the last segment from the URL path
            var segments = path.split("/").filter(function(segment) {
                return segment.length > 0; // Filter out empty segments
            });

            // Extract the last segment if it exists
            var lastSegment = segments.length > 0 ? segments[segments.length - 1] : null;

            // Process the last segment (e.g., faculty-profile becomes Profile)
            if (lastSegment) {
                lastSegment = lastSegment.split('-').slice(1).join('-') || lastSegment; // Remove part before hyphen if present
                lastSegment = capitalizeWords(lastSegment.replace(/-/g, ' ')); // Replace hyphens with spaces and capitalize
            }

            // Function to capitalize the first letter of each word
            function capitalizeWords(str) {
                return str.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }

            // Initialize breadcrumb HTML
            var breadcrumbHtml = '';
            var currentUrl = origin + path; // Base URL for the current page

            // Add the processed last segment to the breadcrumb (if valid)
            if (lastSegment && lastSegment.toLowerCase() !== 'home') {
                breadcrumbHtml += '<span>></span><a href="' + currentUrl + '">' + decodeURIComponent(lastSegment) + '</a>';
            }

            // Combine type, route, and action if present
            var type = params.get('type');
            var route = params.get('route');
            var action = params.get('action');

            if (type || route || action) {
                var combinedSegment = [type, route, action]
                    .filter(function(param) {
                        return param !== null; // Only include non-null values
                    })
                    .map(function(param) {
                        return capitalizeWords(decodeURIComponent(param)); // Capitalize each word
                    })
                    .join(' '); // Join with a space

                // Add the combined segment to the breadcrumb as active
                breadcrumbHtml += '<span>></span><a href="' + currentUrl + '" class="active">' + combinedSegment + '</a>';
            }

            // Append generated breadcrumb HTML to the breadcrumb container
            $('#dynamic-breadcrumb').html(breadcrumbHtml);
        });
    </script>





<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>