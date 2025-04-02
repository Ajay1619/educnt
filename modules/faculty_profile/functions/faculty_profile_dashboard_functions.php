<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
  isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
  $_SERVER['REQUEST_METHOD'] == 'GET'
) {
  // Validate CSRF token
  validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

?>

  <script>
    const load_faculty_profile_statistics_card_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'GET',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/faculty_profile_statistics_card_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          success: function(response) {
            $('#statisitcs-card').html(response);
            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }
    const load_faculty_top_row_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'GET',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/faculty_top_row_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          success: function(response) {
            $('#top-row').html(response);
            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }
    const load_faculty_mid_row_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'GET',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/faculty_mid_row_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          success: function(response) {
            $('#mid-row').html(response);
            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }
    const load_faculty_bottom_row_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'GET',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/components/dashboard/faculty_bottom_row_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          success: function(response) {
            $('#bottom-row').html(response);
            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }

    const fetch_faculty_profile_statistics_card_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'POST',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_profile_statistics_card_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          data: {
            'type': 1
          },
          success: function(response) {
            response = JSON.parse(response)
            if (response.code == 200) {
              const data = response.data
              if (data.type == 1) {
                $('#authorities-count').text(data.authorities_count)
                $('#class-advisors-count').text(data.class_advisors_count)
                $('#teaching-faculty-count').text(data.teaching_faculty_count)
                $('#non-teaching-faculty-count').text(data.non_teaching_faculty_count)
              } else if (data.type == 2) {
                $('#total-learners-count').text(data.authorities_count)
                $('#male-learners-count').text(data.class_advisors_count)
                $('#female-learners-count').text(data.teaching_faculty_count)
                $('#drop-outs-count').text(data.non_teaching_faculty_count)
                $('#mentees-count').text(data.non_teaching_faculty_count)
              }
            }
            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }

    const fetch_faculty_profile_top_row_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'POST',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_profile_top_row_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          data: {
            'type': 1
          },
          success: function(response) {
            response = JSON.parse(response)

            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }

    const fetch_faculty_profile_mid_row_dashboard = () => {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'POST',
          url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_profile_mid_row_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
          headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
          },
          data: {
            'type': 1
          },
          success: function(response) {
            response = JSON.parse(response)

            resolve(); // Resolve the promise
          },
          error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            showToast('error', message);
            reject(); // Reject the promise
          }
        });
      });
    }
  </script>
<?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
