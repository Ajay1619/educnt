(function() {
  var suggestions = [
    { title: "Class Advisor" },
    { title: "Pulp Fiction" },
    { title: "The Dark Knight" },
    { title: "Schindler's List" },
    { title: "Fight Club" },
  ];

  // Display suggestions in the container
  function showSuggestions(suggestions, container) {
    container.empty(); // Clear previous suggestions

    if (suggestions.length == 0) return;

    suggestions.forEach(function (suggestion) {
      var suggestionElement = $(
        '<div class="autocomplete-suggestion">' +
          '<span class="movie-title">' + suggestion.title + "</span>" +
          (suggestion.id ? '<span class="movie-id">' + suggestion.id + "</span>" : "") +
          "</div>"
      );
      container.append(suggestionElement);

      // Click event for each suggestion
      suggestionElement.on("click", function () {
        container.siblings("input").val(suggestion.title);
        container.empty(); // Clear suggestions after selection
      });
    });
  }

  // Close suggestions when clicking outside the input
  $(document).on("click", function (event) {
    if (!$(event.target).closest(".input-container").length) {
      $(".autocomplete-suggestions").empty();
    }
  });

  // Add role functionality with autocomplete
  $("#add-role-btn").on("click", function () {
    const container = $("#additional-roles-container");
    const newRoleSection = $(`
      <div class="form-section">
        <h1>Additional Role</h1>
        <div class="additional-role-box">
          <div class="row">
            <div class="form-group col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
              <div class="input-container">
                <input type="text" class="additional-role" placeholder=" " autocomplete="off">
                <label for="additional-role" class="input-label">Role:</label>
                <div class="autocomplete-suggestions"></div> <!-- Suggestions container -->
              </div>
            </div>
            <div class="form-group col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
              <div class="input-container">
                <input type="text" class="additional-description" placeholder=" ">
                <label for="additional-description" class="input-label">Description</label>
              </div>
            </div>
          </div>
        </div>
        <hr>
      </div>
    `);

    container.append(newRoleSection);

    // Get the suggestions container inside the new role section
    const suggestionContainer = newRoleSection.find(".autocomplete-suggestions");

    // Add event listeners to the new role input
    newRoleSection
      .find(".additional-role")
      .on("input", function () {
        var inputVal = $(this).val().toLowerCase();
        var filteredSuggestions = suggestions.filter(function (option) {
          return option.title.toLowerCase().includes(inputVal);
        });
        showSuggestions(filteredSuggestions, suggestionContainer);
      })
      .on("blur", function () {
        setTimeout(function() {
          suggestionContainer.empty();
        }, 100); // Delay to allow suggestion click before hiding
      });
  });
})();
