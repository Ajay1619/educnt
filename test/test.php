<!DOCTYPE html>
<html>

<head>
    <title>Download Database</title>
</head>

<body>

    <h1>Download Database</h1>

    <button onclick="downloadDatabase()">Download</button>

    <script>
        async function downloadDatabase() {
            try {
                const response = await fetch('download_db.php'); // Replace with your PHP script's URL

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                const link = document.createElement('a');
                link.href = url;
                link.download = 'database_backup.sql'; // Set the desired filename
                document.body.appendChild(link);
                link.click();

                window.URL.revokeObjectURL(url); // Clean up the URL object
            } catch (error) {
                console.error('Error downloading database:', error);
                // Display an error message to the user
            }
        }
    </script>

</body>

</html>