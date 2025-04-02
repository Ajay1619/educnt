<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
?>

    <table id="userTable">
        <thead>
            <tr>
                <th>SL NO</th>
                <th>Username</th>
                <th>Password</th> <!-- You should remove or mask this column -->
                <th>Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="<?= PACKAGES . '/datatables/datatables.min.js' ?>"></script>
    <script>
        const loadDataTable = () => {
            return new Promise((resolve, reject) => {
                $('#userTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/login_table_backend.php', ENT_QUOTES, 'UTF-8') ?>',
                        type: 'POST',
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>' // Dynamic CSRF token from PHP session
                        },
                        dataSrc: function(response) {
                            try {
                                // Parse the JSON response
                                switch (response.code) {
                                    case 200:
                                        resolve(response.data); // Resolve the promise with the data
                                        return response.data; // Return the data for DataTable
                                    case 300:
                                    case 400:
                                    case 403:
                                    case 500:
                                        console.error(response.message);
                                        reject({
                                            code: response.code,
                                            status: response.status,
                                            message: response.message
                                        });
                                        break;
                                    default:
                                        reject({
                                            code: 500,
                                            status: 'error',
                                            message: 'Unknown error occurred.'
                                        });
                                }
                            } catch (e) {
                                reject({
                                    code: 500,
                                    status: 'error',
                                    message: 'Failed to parse response.'
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error(`Error ${jqXHR.status}: ${jqXHR.responseText}`);
                            reject({
                                code: jqXHR.status,
                                status: 'error',
                                message: 'Failed to load data. Please try again later.'
                            });
                        }
                    },
                    columns: [{
                            data: 'sl_no'
                        },
                        {
                            data: 'username'
                        },
                        {
                            data: 'password',
                            render: function(data) {
                                return '*****'; // Mask password
                            }
                        },
                        {
                            data: 'status'
                        }
                    ],
                    // Optional: Add search and pagination features
                    paging: true,
                    searching: true,
                    ordering: true,
                    pageLength: 5, // Default number of records per page
                    lengthMenu: [5, 10, 25, 50, 100] // Options for number of records per page
                });
            });
        };

        $(document).ready(async function() {
            try {
                await loadDataTable();
            } catch (error) {
                console.error(`Error ${error.code}: ${error.status} - ${error.message}`);
                alert(`Error ${error.code}: ${error.status} - ${error.message}`);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>