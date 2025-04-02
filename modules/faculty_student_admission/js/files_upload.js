// $(document).ready(function () {
//     // Section 3: File Upload Handling
//     // Function to handle file uploads and display corresponding icons
//     function handleFileUpload(inputId, iconId) {
//         const fileInput = document.getElementById(inputId);
//         const fileIcon = document.getElementById(iconId);

//         // Ensure the file input and icon exist before adding event listeners
//         if (!fileInput || !fileIcon) {
//             console.error(`Error: Missing file input or icon (inputId: ${inputId}, iconId: ${iconId})`);
//             return;
//         }

//         fileInput.addEventListener('change', function (e) {
//             const file = e.target.files[0];
//             if (file) {
//                 const fileName = file.name.toLowerCase();
//                 if (fileName.endsWith(".pdf")) {
//                     fileIcon.src = "../svg/application_icons/pdf.svg"; // Set PDF icon
//                 } else if (fileName.endsWith(".doc") || fileName.endsWith(".docx")) {
//                     fileIcon.src = "../svg/application_icons/doc.svg"; // Set DOC icon
//                 } else {
//                     fileIcon.style.display = "none"; // Hide the icon for unsupported file types
//                     return;
//                 }
//                 fileIcon.style.display = "block"; // Show the icon
//             } else {
//                 fileIcon.style.display = "none"; // Hide the icon if no file is uploaded
//             }
//         });
//     }

//     // Initialize file upload functionality for different file inputs
//     const fileInputs = [
//         { inputId: 'resume_upload', iconId: 'resume_icon' },
//         { inputId: 'sslc_upload', iconId: 'sslc_icon' },
//         { inputId: 'hsc_upload', iconId: 'hsc_icon' },
//         { inputId: 'ug_upload', iconId: 'ug_icon' },
//         { inputId: 'pg_upload', iconId: 'pg_icon' },
//         { inputId: 'diploma_upload', iconId: 'diploma_icon' },
//     ];

//     fileInputs.forEach(({ inputId, iconId }) => handleFileUpload(inputId, iconId));
// });
