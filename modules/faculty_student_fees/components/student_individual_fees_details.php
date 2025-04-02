<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="card">
        <div class="fee-report-container" id="individual-student-fees-popup">

            <!-- Student Details -->
            <div class="student-details">
                <div class='row'>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-left'>Student Name :
                            <span class='text-light' id='student-name'>Shiyam</span>
                        </div>
                    </div>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-right'>Academic Year :
                            <span class='text-light' id='student-academic-year-of-study'>2024-2025</span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-left'>Year :
                            <span class='text-light' id='student-year-of-study'>1st Year</span>
                        </div>
                    </div>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-right'>Section :
                            <span class='text-light' id='student-section'>A</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Tables for Each Year -->
            <div class="fee-tables">
                <?php for ($year = 1; $year <= 4; $year++): ?>
                    <div class="year-fee-section">
                        <h3><?php echo $year . ($year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th'))) ?> Year Fees</h3>
                        <table class="portal-table portal-table-border" id="fee-table-year-<?php echo $year; ?>">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Fee Category</th>
                                    <th>Fee Amount</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table content will be populated by JavaScript -->
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td id="total-amount-year-<?php echo $year; ?>">0</td>
                                    <td id="total-paid-year-<?php echo $year; ?>">0</td>
                                    <td id="total-pending-year-<?php echo $year; ?>">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        // Sample fee data (replace with actual data from your backend)
        const feeData = {
            1: { // Student ID
                name: "John Doe",
                year: "1st Year",
                academicYear: "2024-2025",
                section: "A",
                fees: {
                    1: [
                        { category: "Tuition Fee", amount: 50000, paid: 30000, pending: 20000 },
                        { category: "Hostel Fee", amount: 20000, paid: 20000, pending: 0 }
                    ],
                    2: [
                        { category: "Tuition Fee", amount: 50000, paid: 0, pending: 50000 },
                        { category: "Hostel Fee", amount: 20000, paid: 0, pending: 20000 }
                    ],
                    3: [
                        { category: "Tuition Fee", amount: 50000, paid: 0, pending: 50000 },
                        { category: "Hostel Fee", amount: 20000, paid: 0, pending: 20000 }
                    ],
                    4: [
                        { category: "Tuition Fee", amount: 50000, paid: 0, pending: 50000 },
                        { category: "Hostel Fee", amount: 20000, paid: 0, pending: 20000 }
                    ]
                }
            }
        };

        // Function to populate the fee report
        function populateFeeReport(studentId) {
            const studentData = feeData[studentId] || {};

            // Update student details using the correct IDs
            document.getElementById('student-name').textContent = studentData.name || 'N/A';
            document.getElementById('student-year-of-study').textContent = studentData.year || 'N/A';
            document.getElementById('student-academic-year-of-study').textContent = studentData.academicYear || 'N/A';
            document.getElementById('student-section').textContent = studentData.section || 'N/A';

            // Populate fee tables for each year
            for (let year = 1; year <= 4; year++) {
                const tbody = document.querySelector(`#fee-table-year-${year} tbody`);
                tbody.innerHTML = ''; // Clear existing content

                const yearFees = studentData.fees?.[year] || [];
                let totalAmount = 0;
                let totalPaid = 0;
                let totalPending = 0;

                if (yearFees.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="5">No fee records available</td>';
                    tbody.appendChild(row);
                } else {
                    yearFees.forEach((fee, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${fee.category}</td>
                            <td>${fee.amount}</td>
                            <td>${fee.paid}</td>
                            <td>${fee.pending}</td>
                        `;
                        tbody.appendChild(row);

                        totalAmount += fee.amount;
                        totalPaid += fee.paid;
                        totalPending += fee.pending;
                    });
                }

                // Update total calculations in tfoot
                document.getElementById(`total-amount-year-${year}`).textContent = totalAmount;
                document.getElementById(`total-paid-year-${year}`).textContent = totalPaid;
                document.getElementById(`total-pending-year-${year}`).textContent = totalPending;
            }
        }

        // Event handler to load the report
        $(document).ready(function() {
            // Example: Load report for a student (replace with dynamic studentId from previous page)
            const studentId = 1; // This should come from the previous page's click event
            populateFeeReport(studentId);
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>