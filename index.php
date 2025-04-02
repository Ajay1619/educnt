<?php require_once('./config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= GLOBAL_PATH ?>/css/sparrow.css">
</head>

<body>
    <!-- typography -->
    <h1>TYPOGRAPHY</h1>
    <h1>Main Heading</h1>
    <h2>Subheading Level 1</h2>
    <h3>Subheading Level 2</h3>
    <h4>Subheading Level 3</h4>
    <h5>Subheading Level 4</h5>
    <h6>Subheading Level 5</h6>

    <section>
        <p>This is a paragraph. It demonstrates the base typography style with a margin at the bottom.</p>

        <a href="#">This is a link</a>

        <ul>
            <li>Unordered list item 1</li>
            <li>Unordered list item 2</li>
            <li>Unordered list item 3</li>
        </ul>

        <ol>
            <li>Ordered list item 1</li>
            <li>Ordered list item 2</li>
            <li>Ordered list item 3</li>
        </ol>

        <blockquote>
            This is a blockquote. It has a left border and italicized text for emphasis.
        </blockquote>

        <p>
            <code>Inline code example</code>
        </p>

        <pre>
        Preformatted text example
        with multiple lines and      spaces preserved.
        </pre>

        <p class="text-center">This text is centered.</p>
        <p class="text-right">This text is right-aligned.</p>
        <p class="text-left">This text is left-aligned.</p>

        <p class="text-uppercase">This text is uppercase.</p>
        <p class="text-lowercase">This text is lowercase.</p>
        <p class="text-capitalize">This text is capitalized.</p>

        <p class="text-sm">This is small text.</p>
        <p class="text-lg">This is large text.</p>
        <p class="text-xl">This is extra-large text.</p>
        <p class="text-xxl">This is extra-extra-large text.</p>

        <p class="font-weight-normal">This text has normal font weight.</p>
        <p class="font-weight-bold">This text is bold.</p>
    </section>

    <!-- spacing -->
    <h1>SPACING</h1>
    <h1 class="text-center mb-2">Spacing Utilities Example</h1>
    <p class="text-center">This page demonstrates various spacing utilities.</p>

    <main class="p-4">
        <section class="mb-5">
            <h2 class="mb-3">Margin Utilities</h2>
            <div class="bg-light p-3 mb-3">
                <p class="m-0">No margin</p>
                <p class="m-1">Margin 0.25rem</p>
                <p class="m-2">Margin 0.5rem</p>
                <p class="m-3">Margin 1rem</p>
                <p class="m-4">Margin 1.5rem</p>
                <p class="m-5">Margin 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="mt-0">No margin-top</p>
                <p class="mt-1">Margin-top 0.25rem</p>
                <p class="mt-2">Margin-top 0.5rem</p>
                <p class="mt-3">Margin-top 1rem</p>
                <p class="mt-4">Margin-top 1.5rem</p>
                <p class="mt-5">Margin-top 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="mr-0">No margin-right</p>
                <p class="mr-1">Margin-right 0.25rem</p>
                <p class="mr-2">Margin-right 0.5rem</p>
                <p class="mr-3">Margin-right 1rem</p>
                <p class="mr-4">Margin-right 1.5rem</p>
                <p class="mr-5">Margin-right 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="mb-0">No margin-bottom</p>
                <p class="mb-1">Margin-bottom 0.25rem</p>
                <p class="mb-2">Margin-bottom 0.5rem</p>
                <p class="mb-3">Margin-bottom 1rem</p>
                <p class="mb-4">Margin-bottom 1.5rem</p>
                <p class="mb-5">Margin-bottom 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="ml-0">No margin-left</p>
                <p class="ml-1">Margin-left 0.25rem</p>
                <p class="ml-2">Margin-left 0.5rem</p>
                <p class="ml-3">Margin-left 1rem</p>
                <p class="ml-4">Margin-left 1.5rem</p>
                <p class="ml-5">Margin-left 3rem</p>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="mb-3">Padding Utilities</h2>
            <div class="bg-light p-3 mb-3">
                <p class="p-0">No padding</p>
                <p class="p-1">Padding 0.25rem</p>
                <p class="p-2">Padding 0.5rem</p>
                <p class="p-3">Padding 1rem</p>
                <p class="p-4">Padding 1.5rem</p>
                <p class="p-5">Padding 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="pt-0">No padding-top</p>
                <p class="pt-1">Padding-top 0.25rem</p>
                <p class="pt-2">Padding-top 0.5rem</p>
                <p class="pt-3">Padding-top 1rem</p>
                <p class="pt-4">Padding-top 1.5rem</p>
                <p class="pt-5">Padding-top 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="pr-0">No padding-right</p>
                <p class="pr-1">Padding-right 0.25rem</p>
                <p class="pr-2">Padding-right 0.5rem</p>
                <p class="pr-3">Padding-right 1rem</p>
                <p class="pr-4">Padding-right 1.5rem</p>
                <p class="pr-5">Padding-right 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="pb-0">No padding-bottom</p>
                <p class="pb-1">Padding-bottom 0.25rem</p>
                <p class="pb-2">Padding-bottom 0.5rem</p>
                <p class="pb-3">Padding-bottom 1rem</p>
                <p class="pb-4">Padding-bottom 1.5rem</p>
                <p class="pb-5">Padding-bottom 3rem</p>
            </div>
            <div class="bg-light p-3 mb-3">
                <p class="pl-0">No padding-left</p>
                <p class="pl-1">Padding-left 0.25rem</p>
                <p class="pl-2">Padding-left 0.5rem</p>
                <p class="pl-3">Padding-left 1rem</p>
                <p class="pl-4">Padding-left 1.5rem</p>
                <p class="pl-5">Padding-left 3rem</p>
            </div>
        </section>

        <section>
            <h2 class="mb-3">Gap Utilities</h2>
            <div class="d-flex gap-3 mb-3">
                <div class="bg-light p-3">Item 1</div>
                <div class="bg-light p-3">Item 2</div>
                <div class="bg-light p-3">Item 3</div>
            </div>
            <div class="d-flex gap-4 mb-3">
                <div class="bg-light p-3">Item 1</div>
                <div class="bg-light p-3">Item 2</div>
                <div class="bg-light p-3">Item 3</div>
            </div>
            <div class="d-flex gap-5 mb-3">
                <div class="bg-light p-3">Item 1</div>
                <div class="bg-light p-3">Item 2</div>
                <div class="bg-light p-3">Item 3</div>
            </div>
        </section>
    </main>

    <!-- badges -->
    <h1 class="text-center mb-4">Badge Styles Example</h1>

    <!-- Toast Container (optional) -->
    <div id="toast-container"></div>

    <!-- Buttons to trigger toast notifications -->
    <button onclick="showToast('success', 'Operation completed successfully!')">Show Success Toast</button>
    <button onclick="showToast('error', 'Something went wrong!')">Show Error Toast</button>
    <button onclick="showToast('warning', 'This is a warning message.')">Show Warning Toast</button>
    <button onclick="showToast('info', 'Here is some information for you.')">Show Info Toast</button>



    <!-- buttons -->
    <h1 class="text-center mb-4">Button Styles Example</h1>

    <!-- Primary Button -->
    <button class="primary">Primary Button</button>

    <!-- Secondary Button -->
    <button class="secondary">Secondary Button</button>

    <!-- Tertiary Button -->
    <button class="tertiary">Tertiary Button</button>

    <!-- Gradient Buttons -->
    <button class="gradient-primary">Primary Gradient</button>
    <button class="gradient-secondary">Secondary Gradient</button>
    <button class="gradient-tertiary">Tertiary Gradient</button>

    <!-- Icon Button -->
    <button class="icon primary"><span class="material-icons">add</span></button>

    <!-- Circle Button -->
    <button class="circle primary medium">P</button>

    <!-- Navigation Buttons -->
    <button class="nav-back">← Back</button>
    <button class="nav-next">Next →</button>

    <!-- Loading Button -->
    <button class="loading">Loading...</button>

    <!-- Popup Button -->
    <button class="popup">Open Popup</button>


    <!-- cards -->

    <div class="card">
        <div class="card-body">
            This is a basic card.
        </div>
    </div>

    <div class="card">
        <div class="card-header">Card Header</div>
        <div class="card-body">
            This card has a header and footer.
        </div>
        <div class="card-footer">Card Footer</div>
    </div>

    <div class="card primary">
        <div class="card-header">Primary Card</div>
        <div class="card-body">
            This is a card with a primary background.
        </div>
    </div>

    <div class="card secondary">
        <div class="card-header">Secondary Card</div>
        <div class="card-body">
            This is a card with a secondary background.
        </div>
    </div>

    <div class="card tertiary">
        <div class="card-header">Tertiary Card</div>
        <div class="card-body">
            This is a card with a tertiary background.
        </div>
    </div>

    <div class="card gradient-primary">
        <div class="card-header">Gradient Card</div>
        <div class="card-body">
            This card has a gradient background.
        </div>
    </div>

    <div class="card">
        <img src="<?= GLOBAL_PATH . '/images/sample.png' ?>" alt="Card Image">
        <div class="card-body">
            This card has an image.
        </div>
    </div>

    <div class="card alert-success">
        <div class="card-body">
            This is a success alert card.
        </div>
    </div>

    <div class="card alert-error">
        <div class="card-body">
            This is an error alert card.
        </div>
    </div>

    <div class="card alert-warning">
        <div class="card-body">
            This is a warning alert card.
        </div>
    </div>

    <div class="card alert-info">
        <div class="card-body">
            This is an info alert card.
        </div>
    </div>



    <!-- popups -->
    <div class="container">
        
    <div class="popup-overlays">
    <div class="alert-popup">
        <button class="popup-close-btn">
            x
        </button>
        <div class="popup-header">
            Subject And Hour Confirmation
        </div>
        <div class="popup-content">
        <div class="form-section">
            <h2>edit role</h2>

            <form>
            <div class="container">
                    <div class="row">
                        <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="input-container">
                                <input type="text" class="auto" name="primary-role" id="autocomplete-input" placeholder=" ">
                                <label for="autocomplete-input" for="primary-role" class="input-label">Primary Role:</label>
                                <div id="autocomplete-suggestions" class="autocomplete-suggestions"></div>
                            </div>

                        </div>
                        <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="input-container">
                                <input type="text" id="primary-description" name="primary-description" placeholder=" " required aria-required="true" />
                                <label for="primary-description" class="input-label"> Description:</label>
                            </div>
                        </div>
                    </div>
                    <div class="conditional-fields primary-conditional-fields">
                        <div class="row">
                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="primary-batch" name="primary-batch" placeholder=" " required aria-required="true" />
                                    <label for="primary-batch" class="input-label">Batch:</label>
                                </div>
                            </div>
                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="primary-academic-year" name="primary-academic-year" placeholder=" " required aria-required="true" />
                                    <label for="name" class="input-label">Academic Year:</label>
                                </div>
                            </div>
                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="primary-semester" name="primary-semester" placeholder=" " required aria-required="true" />
                                    <label for="name" class="input-label">Semester:</label>
                                </div>
                            </div>
                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="primary-section" name="primary-section" placeholder=" " required aria-required="true" />
                                    <label for="name" class="input-label">Section</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="input-container">
                            <button class="primary" type="button">Submit</button>
                        </div>
                    </div>
                </div>
                 </form>

        </div>
        
        </div>
        
 </div>
</div>
        

    </div>
    <!-- <button class="primary" onclick="openPopup('gradient-primary-popup')">Show Gradient Primary popup</button>
    <div class="popup-overlay" id="gradient-primary-popup-overlay"></div>

    <div class="popup gradient-primary" id="gradient-primary-popup">
        <div class="popup-header">Gradient Primary popup</div>
        <div class="popup-body">This popup uses a primary gradient background.</div>
        <div class="popup-footer">
            <button class="primary-btn" onclick="closePopup('gradient-primary-popup')">OK</button>
        </div>
    </div> -->



    <!-- form -->
    <h1>FORM ELEMENTS</h1>
    <form>
        <!-- Text Inputs -->
        <label for="name">Name:</label>
        <input class="success" type="text" id="name" name="name" placeholder="Enter your name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" placeholder="Enter your age">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <!-- Textarea -->
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" placeholder="Tell us about yourself"></textarea>

        <!-- Select Dropdown -->
        <label for="country">Country:</label>
        <select id="country" name="country">
            <option value="" disabled selected>Select your country</option>
            <option value="usa">United States</option>
            <option value="canada">Canada</option>
            <option value="uk">United Kingdom</option>
            <option value="australia">Australia</option>
        </select>

        <!-- Date Picker -->
        <label for="birthdate">Birthdate:</label>
        <input type="date" id="birthdate" name="birthdate">

        <!-- Range Slider -->
        <label for="volume">Volume:</label>
        <input type="range" id="volume" name="volume" min="0" max="100" value="50">

        <!-- Radio Buttons -->
        <fieldset>
            <legend>Gender:</legend>
            <label>
                <input type="radio" name="gender" value="male"> Male
            </label>
            <label>
                <input type="radio" name="gender" value="female"> Female
            </label>
            <label>
                <input type="radio" name="gender" value="other"> Other
            </label>
        </fieldset>

        <!-- Checkboxes -->
        <fieldset>
            <legend>Interests:</legend>
            <label>
                <input type="checkbox" name="interests" value="sports"> Sports
            </label>
            <label>
                <input type="checkbox" name="interests" value="music"> Music
            </label>
            <label>
                <input type="checkbox" name="interests" value="travel"> Travel
            </label>
        </fieldset>

        <!-- Form Buttons -->
        <button type="submit">Submit</button>
    </form>

    <!-- Success popup-->
    <div class="popup popup-success">
        <div class="popup-content">
            <button class="popup-close-btn" onclick="this.parentElement.parentElement.style.display='none'">✖</button>
            <div class="popup-header">Success!</div>
            <div class="popup-body">
                Your operation was successful.
            </div>
            <div class="popup-footer">
                <button class="primary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">OK</button>
            </div>
        </div>
    </div> 

    <!-- Error popup -->
    <div class="popup popup-error">
        <div class="popup-content">
            <button class="popup-close-btn" onclick="this.parentElement.parentElement.style.display='none'">✖</button>
            <div class="popup-header">Error!</div>
            <div class="popup-body">
                Something went wrong. Please try again.
            </div>
            <div class="popup-footer">
                <button class="primary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Retry</button>
                <button class="secondary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Info popup -->
    <div class="popup popup-info">
        <div class="popup-content">
            <button class="popup-close-btn" onclick="this.parentElement.parentElement.style.display='none'">✖</button>
            <div class="popup-header">Information</div>
            <div class="popup-body">
                This is an informational message.
            </div>
            <div class="popup-footer">
                <button class="primary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Close</button>
            </div>
        </div>
    </div>

    <!-- Warning popup -->
    <div class="popup popup-warning">
        <div class="popup-content">
            <button class="popup-close-btn" onclick="this.parentElement.parentElement.style.display='none'">✖</button>
            <div class="popup-header">Warning!</div>
            <div class="popup-body">
                Proceed with caution.
            </div>
            <div class="popup-footer">
                <button class="tertiary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Proceed</button>
                <button class="secondary-btn" onclick="this.parentElement.parentElement.parentElement.style.display='none'">Cancel</button>
            </div>
        </div>
    </div> 

    <!-- Light Theme Table -->
    <table>
        <thead>
            <tr>
                <th>Header 1</th>
                <th>Header 2</th>
                <th>Header 3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
            </tr>
            <tr>
                <td>Data 4</td>
                <td>Data 5</td>
                <td>Data 6</td>
            </tr>
        </tbody>
    </table>

    <table class="table-bordered">
        <thead>
            <tr>
                <th>Header 1</th>
                <th>Header 2</th>
                <th>Header 3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
            </tr>
            <tr>
                <td>Data 4</td>
                <td>Data 5</td>
                <td>Data 6</td>
            </tr>
        </tbody>
    </table>

    <table class="table-striped">
        <thead>
            <tr>
                <th>Header 1</th>
                <th>Header 2</th>
                <th>Header 3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
            </tr>
            <tr>
                <td>Data 4</td>
                <td>Data 5</td>
                <td>Data 6</td>
            </tr>
        </tbody>
    </table>

    <table class="table-bordered table-striped">
        <thead>
            <tr>
                <th>Header 1</th>
                <th>Header 2</th>
                <th>Header 3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
            </tr>
            <tr>
                <td>Data 4</td>
                <td>Data 5</td>
                <td>Data 6</td>
            </tr>
        </tbody>
    </table>



    <!-- grid -->
    <h1>GRID SYSTEM</h1>
    <div class="container">
        <div class="row">
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">1</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">2</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">3</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">4</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">5</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">6</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">7</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">8</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">9</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">10</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">11</div>
            </div>
            <div class="col col-1 col-xs-12 col-sm-6 col-md-4 col-lg-2">
                <div class="box">12</div>
            </div>
        </div>
    </div>

    <!-- tabs -->
    <h1>Tabs</h1>
    <div class="tabs">
        <div class="tab-headers">
            <div class="tab-header active" onclick="openTab('tab1')">Tab 1</div>
            <div class="tab-header" onclick="openTab('tab2')">Tab 2</div>
            <div class="tab-header" onclick="openTab('tab3')">Tab 3</div>
        </div>
        <div id="tab1" class="tab-content active">
            <p>Content for Tab 1</p>
        </div>
        <div id="tab2" class="tab-content">
            <p>Content for Tab 2</p>
        </div>
        <div id="tab3" class="tab-content">
            <p>Content for Tab 3</p>
        </div>
    </div>

    <!-- accordians -->

    <h1>Accordians</h1>
    <div class="accordion">
        <div class="accordion-header" onclick="toggleAccordion(this)">
            Accordion Header 1
        </div>
        <div class="accordion-content">
            <p>Content for Accordion 1</p>
        </div>
    </div>

    <div class="accordion">
        <div class="accordion-header" onclick="toggleAccordion(this)">
            Accordion Header 2
        </div>
        <div class="accordion-content">
            <p>Content for Accordion 2</p>
        </div>
    </div>
    <h1>Tooltips</h1>
    <!-- Tooltip Top -->
    <div class="tooltip tooltip-top">
        Hover over me
        <span class="tooltiptext">Tooltip on top</span>
    </div>

    <!-- Tooltip Bottom -->
    <div class="tooltip tooltip-bottom">
        Hover over me
        <span class="tooltiptext">Tooltip on bottom</span>
    </div>

    <!-- Tooltip Left -->
    <div class="tooltip tooltip-left">
        Hover over me
        <span class="tooltiptext">Tooltip on left</span>
    </div>

    <!-- Tooltip Right -->
    <div class="tooltip tooltip-right">
        Hover over me
        <span class="tooltiptext">Tooltip on right</span>
    </div>

    <!-- breadcrumbs -->
    <h1>breadcrumbs</h1>
    <!-- Breadcrumbs Container -->
    <nav class="breadcrumbs">
        <span class="breadcrumbs__item">Home</span>
        <span class="breadcrumbs__separator">&gt;</span>
        <span class="breadcrumbs__item">Category</span>
        <span class="breadcrumbs__separator">&gt;</span>
        <span class="breadcrumbs__item breadcrumbs__item--active">Current Page</span>
    </nav>

    <!-- loading -->
    <h1>Loading</h1>
    <!-- Loading Container -->
    <div class="loading-container">
        <div class="spinner"></div>
        <div class="loading-text">Loading...</div>
    </div>

    <script src="<?= GLOBAL_PATH . '/js/showToast.js' ?>"></script>
    <script>
        function toggleAccordion(header) {
            const content = header.nextElementSibling;
            const isActive = content.classList.contains('show');

            // Hide all other accordions
            document.querySelectorAll('.accordion-content').forEach(el => {
                if (el !== content) {
                    el.classList.remove('show');
                }
            });

            // Toggle current accordion
            content.classList.toggle('show', !isActive);
        }

        function openTab(tabId) {
            // Hide all tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tab headers
            const tabHeaders = document.querySelectorAll('.tab-header');
            tabHeaders.forEach(header => header.classList.remove('active'));

            // Show the clicked tab content
            document.getElementById(tabId).classList.add('active');

            // Set the clicked tab header as active
            const activeHeader = Array.from(tabHeaders).find(header => header.textContent.trim() === tabId.replace('tab', 'Tab '));
            if (activeHeader) {
                activeHeader.classList.add('active');
            }
        }


        // function openPopup(popupId) {
        //     document.getElementById(popupId + '-overlay').classList.add('active');
        //     document.getElementById(popupId).classList.add('active');
        // }

        // function closePopup(popupId) {
        //     document.getElementById(popupId + '-overlay').classList.remove('active');
        //     document.getElementById(popupId).classList.remove('active');
        // }

        function confirmAction() {
            // Handle confirmation action
            console.log('Action confirmed');
            closePopup('warning-popup');
        }


        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.classList.add('toast-dismiss');
                setTimeout(() => {
                    toast.remove();
                }, 300); // Match the slide-out animation duration
            }, 5000); // 5 seconds before auto-dismiss
        });
        
    </script>
</body>

</html>