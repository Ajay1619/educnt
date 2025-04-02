const app_url = "http://localhost/educnt-svcet-faculty/";
const no_data_html =
  `
<img class="action-image" src="` +
  app_url +
  `global/images/svgs/no_data_icon.svg" alt="">
<p>Sorry! It Looks There Is No Data For Your Filter.</p>
`;

var payment_methods_list = [{
  title: 'Cash',
  value: 1
},
{
  title: 'Cheque',
  value: 2
},
{
  title: 'UPI',
  value: 3
},
{
  title: 'Debit Card',
  value: 4
},
{
  title: 'Credit Card',
  value: 5
},
];


// Function to capitalize the first letter of each word
function capitalizeWords(str) {
  return str.replace(/\b\w/g, function (char) {
    return char.toUpperCase();
  });
}

function showToast(type, message) {
  const toastContainer = document.getElementById("toast-container");

  // Create toast element
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;

  // Create loader element
  const loader = document.createElement("div");
  loader.className = `toast-loader toast-loader-${type}`;

  // Create close button
  const closeButton = document.createElement("button");
  closeButton.className = "toast-close";
  closeButton.innerHTML = "&times;";
  closeButton.addEventListener("click", () => dismissToast(toast));

  // SVG icons
  const icons = {
    info: `<svg width="28" height="27" viewBox="0 0 28 27" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M13.7011 27.0001C16.4011 27.0001 19.0405 26.2083 21.2854 24.7249C23.5304 23.2415 25.2801 21.1331 26.3133 18.6663C27.3466 16.1995 27.6169 13.4851 27.0902 10.8663C26.5634 8.24756 25.2633 5.84208 23.3541 3.95407C21.4449 2.06606 19.0125 0.780307 16.3644 0.259405C13.7163 -0.261496 10.9715 0.00584936 8.477 1.02763C5.98255 2.04942 3.8505 3.77975 2.35047 5.99982C0.850442 8.21988 0.0498047 10.83 0.0498047 13.5C0.0537193 17.0793 1.49324 20.5108 4.05251 23.0417C6.61179 25.5726 10.0818 26.9962 13.7011 27.0001ZM13.7011 5.62502C14.0386 5.62502 14.3686 5.72399 14.6492 5.90941C14.9298 6.09484 15.1485 6.35839 15.2777 6.66674C15.4068 6.97509 15.4406 7.31439 15.3748 7.64173C15.3089 7.96908 15.1464 8.26976 14.9078 8.50576C14.6691 8.74177 14.3651 8.90248 14.034 8.9676C13.703 9.03271 13.3599 8.99929 13.0481 8.87157C12.7363 8.74384 12.4698 8.52755 12.2823 8.25004C12.0948 7.97254 11.9947 7.64627 11.9947 7.31252C11.9947 6.86496 12.1745 6.43574 12.4945 6.11927C12.8145 5.8028 13.2486 5.62502 13.7011 5.62502ZM12.5635 11.25H13.7011C14.3046 11.25 14.8833 11.4871 15.31 11.909C15.7367 12.331 15.9764 12.9033 15.9764 13.5V20.25C15.9764 20.5484 15.8565 20.8346 15.6432 21.0455C15.4298 21.2565 15.1405 21.375 14.8388 21.375C14.537 21.375 14.2477 21.2565 14.0343 21.0455C13.821 20.8346 13.7011 20.5484 13.7011 20.25V13.5H12.5635C12.2618 13.5 11.9725 13.3815 11.7591 13.1705C11.5458 12.9595 11.4259 12.6734 11.4259 12.375C11.4259 12.0767 11.5458 11.7905 11.7591 11.5795C11.9725 11.3686 12.2618 11.25 12.5635 11.25Z" fill="#0C5460"/>
    </svg>`,
    success: `<svg width="25" height="30" viewBox="0 0 25 30" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M20.5859 2.83251L12.8265 0.254639C12.5724 0.170329 12.2978 0.170329 12.0438 0.254639L4.28435 2.83251C3.05065 3.24083 1.97748 4.02613 1.2173 5.07685C0.457126 6.12756 0.0486136 7.39024 0.0498073 8.68549V15C0.0498073 24.3329 11.4443 29.4874 11.9323 29.7021C12.0906 29.7722 12.2619 29.8084 12.4351 29.8084C12.6084 29.8084 12.7797 29.7722 12.938 29.7021C13.426 29.4874 24.8204 24.3329 24.8204 15V8.68549C24.8216 7.39024 24.4131 6.12756 23.653 5.07685C22.8928 4.02613 21.8196 3.24083 20.5859 2.83251ZM18.2785 12.1827L12.9875 17.4545C12.771 17.6716 12.5133 17.8438 12.2295 17.9609C11.9457 18.0781 11.6413 18.1379 11.3341 18.1369H11.2932C10.9797 18.1321 10.6705 18.064 10.3841 17.9367C10.0978 17.8095 9.84037 17.6257 9.62737 17.3965L6.77132 14.4348C6.64763 14.3205 6.54869 14.1821 6.48058 14.0283C6.41246 13.8745 6.37661 13.7085 6.37521 13.5404C6.37382 13.3723 6.40691 13.2057 6.47246 13.0507C6.53802 12.8958 6.63464 12.7559 6.75642 12.6395C6.87819 12.5232 7.02255 12.4328 7.18063 12.374C7.3387 12.3153 7.50717 12.2893 7.67568 12.2977C7.8442 12.3061 8.00921 12.3487 8.1606 12.423C8.31199 12.4972 8.44655 12.6015 8.55604 12.7294L11.3353 15.617L16.5223 10.4341C16.7559 10.2093 17.0687 10.0849 17.3935 10.0877C17.7182 10.0906 18.0288 10.2203 18.2585 10.4491C18.4881 10.6779 18.6184 10.9874 18.6212 11.311C18.624 11.6346 18.4992 11.9463 18.2736 12.179L18.2785 12.1827Z" fill="#155724"/>
    </svg>`,
    error: `<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M22.6955 5.272L19.1168 1.757C17.9633 0.624 16.429 0 14.798 0H9.73706C8.10606 0 6.57177 0.624 5.41724 1.757L1.83861 5.272C0.685101 6.406 0.0498047 7.913 0.0498047 9.515V14.486C0.0498047 16.088 0.685101 17.595 1.83861 18.729L5.41724 22.244C6.57177 23.377 8.10606 24.001 9.73706 24.001H14.798C16.4301 24.001 17.9633 23.377 19.1168 22.244L22.6955 18.73C23.85 17.597 24.4853 16.09 24.4853 14.487V9.516C24.4853 7.913 23.85 6.405 22.6955 5.272ZM16.7874 15.061C17.1855 15.452 17.1855 16.084 16.7874 16.475C16.5889 16.67 16.3283 16.768 16.0676 16.768C15.807 16.768 15.5464 16.67 15.3478 16.475L12.2497 13.432L9.15165 16.475C8.95312 16.67 8.69248 16.768 8.43185 16.768C8.17121 16.768 7.91058 16.67 7.71205 16.475C7.31397 16.084 7.31397 15.452 7.71205 15.061L10.8101 12.018L7.71205 8.975C7.31397 8.584 7.31397 7.952 7.71205 7.561C8.11013 7.17 8.75357 7.17 9.15165 7.561L12.2497 10.604L15.3478 7.561C15.7459 7.17 16.3893 7.17 16.7874 7.561C17.1855 7.952 17.1855 8.584 16.7874 8.975L13.6893 12.018L16.7874 15.061Z" fill="#721C24"/>
    </svg>`,
    warning: `<svg width="28" height="25" viewBox="0 0 28 25" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M26.8957 19.9134L16.2175 1.72685C15.6836 0.807176 14.7408 0.255371 13.6729 0.255371C12.6051 0.255371 11.6623 0.807176 11.1397 1.72685L0.450235 19.9134C-0.083672 20.8561 -0.083672 21.9712 0.450235 22.9024C0.984141 23.8336 1.93836 24.3969 2.99481 24.3969H24.3624C25.4302 24.3969 26.3845 23.8336 26.907 22.9024C27.4296 21.9712 27.4296 20.8561 26.8957 19.9134ZM14.8089 19.7985H12.537V17.4993H14.8089V19.7985ZM14.8089 15.2001H12.537V8.30253H14.8089V15.2001Z" fill="#856404"/>
    </svg>`,
  };

  // Set toast content
  toast.innerHTML = `${icons[type]}<div class="toast-message">${message}</div>`;
  toast.appendChild(loader);
  toast.appendChild(closeButton); // Append the close button

  // Append toast to container
  toastContainer.appendChild(toast);

  // Start loader animation
  loader.classList.add("toast-loader-animation");

  // Timeout variable to track auto-dismiss
  let autoDismissTimeout = setTimeout(() => {
    dismissToast(toast);
  }, 5000);

  // Pause auto-dismiss and loader animation on hover
  toast.addEventListener("mouseenter", () => {
    clearTimeout(autoDismissTimeout);
    loader.style.animationPlayState = "paused";
  });

  // Resume auto-dismiss and loader animation on mouse leave
  toast.addEventListener("mouseleave", () => {
    loader.style.animationPlayState = "running";
    autoDismissTimeout = setTimeout(() => {
      dismissToast(toast);
    }, 5000);
  });
}

// Function to dismiss a toast with fade-out animation
function dismissToast(toast) {
  toast.classList.add("toast-dismiss");
  setTimeout(() => toast.remove(), 300); // Matches fadeOut duration
}

function showLoading() {
  //settimeinterval

  const overlay = `
  <div class="loading-container">
  <svg class="pencil" viewBox="0 0 200 200" width="200px" height="200px" xmlns="http://www.w3.org/2000/svg">
	<defs>
		<clipPath id="pencil-eraser">
			<rect rx="5" ry="5" width="30" height="30"></rect>
		</clipPath>
	</defs>
	<circle class="pencil__stroke" r="70" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="439.82 439.82" stroke-dashoffset="439.82" stroke-linecap="round" transform="rotate(-113,100,100)" />
	<g class="pencil__rotate" transform="translate(100,100)">
		<g fill="none">
			<circle class="pencil__body1" r="64" stroke="hsl(120,90%,50%)" stroke-width="30" stroke-dasharray="402.12 402.12" stroke-dashoffset="402" transform="rotate(-90)" />
			<circle class="pencil__body2" r="74" stroke="hsl(120,90%,60%)" stroke-width="10" stroke-dasharray="464.96 464.96" stroke-dashoffset="465" transform="rotate(-90)" />
			<circle class="pencil__body3" r="54" stroke="hsl(120,90%,40%)" stroke-width="10" stroke-dasharray="339.29 339.29" stroke-dashoffset="339" transform="rotate(-90)" />
		</g>
		<g class="pencil__eraser" transform="rotate(-90) translate(49,0)">
			<g class="pencil__eraser-skew">
				<rect fill="hsl(120,90%,70%)" rx="5" ry="5" width="30" height="30" />
				<rect fill="hsl(120,90%,60%)" width="5" height="30" clip-path="url(#pencil-eraser)" />
				<rect fill="hsl(120,10%,90%)" width="30" height="20" />
				<rect fill="hsl(120,10%,70%)" width="15" height="20" />
				<rect fill="hsl(120,10%,80%)" width="5" height="20" />
				<rect fill="hsla(120,10%,10%,0.2)" y="6" width="30" height="2" />
				<rect fill="hsla(120,10%,10%,0.2)" y="13" width="30" height="2" />
			</g>
		</g>
		<g class="pencil__point" transform="rotate(-90) translate(49,-30)">
			<polygon fill="hsl(33,90%,70%)" points="15 0,30 30,0 30" />
			<polygon fill="hsl(33,90%,50%)" points="15 0,6 30,0 30" />
			<polygon fill="hsl(223,10%,10%)" points="15 0,20 10,10 10" />
		</g>
	</g>
</svg>

<div class="loading-text">
<span id="funny-text"></span>
</div>
</div>
`;
  // Disable scrolling
  $("body").css("overflow", "hidden");

  $("#Loading").show();
  $("#Loading").html(overlay);
  const path_name = window.location.pathname;
  const lastSegment = path_name.substring(path_name.lastIndexOf("/") + 1);
  const moduleQuotes = {
    "faculty-profile": [
      "Loading... Prepare to be amazed—your profile's about to steal the show!",
      "Loading... This profile has more plot twists than a suspense thriller!",
      "Loading... Like a superhero's origin story, your achievements are about to shine!",
      "Loading... You're not just a profile, you're a legend in the making!",
      "Loading... This profile has more depth than any character arc out there!",
      "Loading... Just like a master storyteller, your profile will captivate everyone!",
      "Loading... The plot is thickening—your journey is about to unfold!",
      "Loading... It's more than a profile—it's a saga waiting to be told!",
      "Loading... Your profile’s about to have everyone saying 'How did we miss this?!'",
      "Loading... Like a climactic final scene, your profile is a showstopper!",
    ],
    "faculty-roles-responsibilities": [
      "Loading... Every great leader knows—'With great power comes great responsibility.'",
      "Loading... You're the captain of this ship—steady as she goes!",
      "Loading... Much like a certain detective, you’ve got the mind to solve any teaching challenge!",
      "Loading... Like the mentor of a rising star, you’re shaping the next generation!",
      "Loading... The classroom is your stage, and you're the star performer!",
      "Loading... Your responsibilities are as legendary as the heroes who bear them!",
      "Loading... You're like a sensei—guiding with wisdom, grace, and a bit of style!",
      "Loading... You're not just a leader—you're the backbone of education!",
      "Loading... A true legend knows, ‘It’s not about the power, it’s about the responsibility!’",
      "Loading... Much like a great strategist, you're laying out the perfect plan!",
    ],
    "faculty-student-admission": [
      "Loading... Choosing the right student is like casting for the perfect role.",
      "Loading... This decision is more crucial than the final pick for a world-changing mission.",
      "Loading... You're the sorting hat, finding the right fit for every student!",
      "Loading... Much like a mentor guiding the next hero, you're shaping the future!",
      "Loading... It’s not just an admission, it’s the beginning of a grand adventure!",
      "Loading... Each student is a story—you're just picking the next blockbuster!",
      "Loading... Just like a director picking the perfect cast, you’re selecting the future stars!",
      "Loading... This is the beginning of a new journey, much like a hero's first step.",
      "Loading... As the chosen one, you're deciding who will change the world!",
      "Loading... Just like in a legendary epic, the future is about to be written!",
    ],
    "faculty-achievements": [
      "Loading... This achievement is about to go down in history—like an epic saga!",
      "Loading... You've achieved more than anyone could've ever predicted!",
      "Loading... Like a great underdog story, you've overcome every obstacle in your path!",
      "Loading... Your achievements are as heroic as any journey you've seen on screen!",
      "Loading... Like a favorite character's comeback, your success story is inspiring!",
      "Loading... This achievement is a 'mission accomplished' moment!",
      "Loading... Your hard work has paid off—you're the hero everyone was waiting for!",
      "Loading... Just like a plot twist, this achievement will leave everyone stunned!",
      "Loading... Like a landmark film, your achievements will be remembered for years!",
      "Loading... You've crossed the finish line—this achievement is as epic as they come!",
    ],
    "faculty-class-schedule": [
      "Loading... The class schedule is like your script—ready to unfold in the most exciting way!",
      "Loading... Your day’s about to take shape—get ready to perform on the classroom stage!",
      "Loading... Just like a perfect plan, your class schedule is about to make everything flow smoothly!",
      "Loading... Your schedule’s more dynamic than a blockbuster sequel—prepare for the adventure ahead!",
      "Loading... Like a well-rehearsed scene, your class schedule will unfold seamlessly!",
      "Loading... Every class is a new chapter—let’s see what happens next in your teaching story!",
      "Loading... Your class schedule is set—time to deliver a performance that’ll impress everyone!",
      "Loading... Much like a great show, your schedule is packed with exciting moments!",
      "Loading... This schedule’s ready to keep you on your toes—get ready for a fantastic ride!",
      "Loading... Your schedule is as well-planned as a perfect plot twist—exciting and impactful!",
    ],
    "faculty-dashboard": [
      "Loading... Your journey begins here—a dashboard as unique as you are!",
      "Loading... Your personal universe is coming into focus—prepare to be amazed!",
      "Loading... This dashboard is your command center—get ready to take charge!",
      "Loading... Your story is the headline today—let’s see what you’ve achieved!",
      "Loading... Like a treasure map, this dashboard holds all the keys to your success!",
      "Loading... Everything you need, right at your fingertips—your personal HQ awaits!",
      "Loading... Your dashboard is like a backstage pass—welcome to the spotlight!",
      "Loading... This page isn’t just a dashboard—it’s your personal hall of fame!",
      "Loading... Much like a masterwork, this dashboard showcases the brilliance of you!",
      "Loading... Ready to dive in? Your journey toward greatness starts here!",
    ],
    "faculty-student-fees": [
      "Loading... Managing student fees—because every great journey has its investments!",
      "Loading... Ensuring smooth transactions—one step closer to hassle-free education!",
      "Loading... Like a well-budgeted adventure, every detail matters in student fees!",
      "Loading... Keeping finances in check—because great education is priceless!",
      "Loading... Just like a balance sheet, accuracy is key to seamless fee management!",
      "Loading... Managing student fees—ensuring every penny supports a brighter future!",
      "Loading... Finances made simple—because learning should be the only challenge!",
      "Loading... Student fees, sorted—because education should always come first!",
      "Loading... Streamlining payments—because financial clarity leads to academic success!",
      "Loading... Like a well-planned strategy, student fees ensure a smooth academic journey!",
    ],
    "faculty-academic-calendar": [
      "Loading... The academic calendar—your roadmap to a successful year!",
      "Loading... Every great journey needs a plan—here’s yours!",
      "Loading... Like a blockbuster timeline, your academic year is perfectly scheduled!",
      "Loading... Dates set, goals aligned—get ready for an incredible academic season!",
      "Loading... Much like a well-written script, your academic year is mapped out!",
      "Loading... This calendar isn't just dates—it's a journey of learning and growth!",
      "Loading... Every chapter of this year is planned—let’s make it count!",
      "Loading... Like a master planner, your academic calendar is setting the stage for success!",
      "Loading... Stay on track, stay ahead—your academic calendar is your best guide!",
      "Loading... The schedule is set—now it’s time to bring it to life!",
    ],
    "faculty-lesson-plan": [
      "Loading... Every great teacher has a plan—here’s yours!",
      "Loading... A lesson well-planned is a lesson well-taught!",
      "Loading... Mapping out success—one lesson at a time!",
      "Loading... Just like a great story, your lesson plan sets the stage for learning!",
      "Loading... A well-crafted lesson plan makes every class a masterpiece!",
      "Loading... Every class is an opportunity—your lesson plan ensures none are wasted!",
      "Loading... Teaching with purpose—because every lesson matters!",
      "Loading... Your plan, your vision—empowering students with knowledge!",
      "Loading... Lessons structured, learning optimized—let’s make it a great session!",
      "Loading... Great lessons don’t just happen—they’re planned to perfection!",
    ],
    "faculty-stock-inventory": [
      "Loading... Keeping inventory in check—because every resource matters!",
      "Loading... Like a treasure chest, a well-managed inventory is key to success!",
      "Loading... Organization is the key—stock inventory at its best!",
      "Loading... Resources secured, efficiency ensured—let’s keep things in order!",
      "Loading... A well-stocked inventory is the backbone of a smooth operation!",
      "Loading... Managing resources like a pro—because every detail counts!",
      "Loading... Smart inventory, smooth workflow—keeping things running seamlessly!",
      "Loading... Like a supply chain mastermind, your stock inventory is in top shape!",
      "Loading... Every item accounted for—because efficiency starts with organization!",
      "Loading... Inventory managed, operations optimized—let’s keep things flowing!",
    ],
    "faculty-admission": [
      "Loading... The gateway to excellence—faculty admissions in progress!",
      "Loading... Like assembling a dream team, admissions shape the future!",
      "Loading... Every great institution starts with the right faculty!",
      "Loading... Admissions open—new beginnings, fresh possibilities!",
      "Loading... Selecting the best—because great education starts with great faculty!",
      "Loading... Every faculty member brings a unique story—let’s find the right fit!",
      "Loading... Admissions: The first step in shaping academic excellence!",
      "Loading... Choosing educators who inspire—because teaching is an art!",
      "Loading... Every application is a story—let’s find the perfect match!",
      "Loading... Much like casting for a great film, the right faculty makes all the difference!",
    ],
    "faculty-student-examination": [
      "Loading... The moment of truth—examinations ahead!",
      "Loading... Like a thrilling challenge, exams test the best in students!",
      "Loading... It’s showtime—students, get ready to shine!",
      "Loading... Just like a final boss battle, exams are where skills are put to the test!",
      "Loading... Every great mind faces a test—this is just another step forward!",
      "Loading... The stage is set, the knowledge is ready—time to ace those exams!",
      "Loading... Examinations: Where preparation meets opportunity!",
      "Loading... Like the climax of a great journey, exams bring out the best!",
      "Loading... Just like a championship, exams separate the best from the rest!",
      "Loading... This is where knowledge turns into achievement—let’s make it count!",
    ],
    "faculty-student-attendance": [
      "Loading... Tracking attendance—because every presence counts!",
      "Loading... Just like a perfect score, attendance matters!",
      "Loading... Every day in class is a step closer to success!",
      "Loading... Marking presence—because learning starts with showing up!",
      "Loading... Attendance: The foundation of discipline and dedication!",
      "Loading... Like a well-prepared team, every student’s presence matters!",
      "Loading... Showing up is the first step towards achieving greatness!",
      "Loading... Attendance check—because consistency is key!",
      "Loading... Every great journey begins by being present in the moment!",
      "Loading... A full class is a powerhouse of learning—let’s make it happen!",
    ],
  };

  // Get quotes for the current module
  const quotes = moduleQuotes[lastSegment] || [
    "Loading... Preparing the best experience for you...",
  ];

  // Set the initial quote
  const funnyText = document.getElementById("funny-text");
  let randomIndex = Math.floor(Math.random() * quotes.length);
  funnyText.textContent = quotes[randomIndex];

  // Change the quote every 2 seconds
  setInterval(() => {
    randomIndex = Math.floor(Math.random() * quotes.length);
    funnyText.textContent = quotes[randomIndex];
  }, 2000); // Changes message every 2 seconds
}
function hideLoading() {
  $("#Loading").fadeOut(1000);
  $("#Loading").hide();
  $("#Loading").html("");
  // enable scrolling
  $("body").css("overflow", "auto");
}

function showComponentLoading(text = 1) {
  var load_text = "Loading...";

  if (text == 2) {
    load_text = "Updating...";
  } else if (text == 3) {
    load_text = "Saving...";
  } else if (text == 4) {
    load_text = "Deleting...";
  } else {
    load_text = "Loading...";
  }
  const overlay = `
      <div class="loading-component-toast">
      <span>${load_text}</span>
      <div class="component-loading-container">
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
        <div class="component-dot"></div>
      </div>
    </div>

  `;

  $("#Component-Loading").html(overlay);
  $("#Component-Loading").fadeIn();
  $("#Component-Loading").show();
}

function showDropdownLoading(container) {
  const overlay = `<div class="dropdown-loading-container"><div class="dot" /></div>`;

  container.html(overlay);
  container.fadeIn();
  container.show();
}

async function hideComponentLoading() {
  $("#Component-Loading").fadeOut(500);
  setTimeout(async function () {
    $("#Component-Loading").html("");
    $("#Component-Loading").hide();
  }, 1000);
}

const table_loading = `
<div class="component-loading-container">
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
</div>

<style>
  .container {
    --uib-size: 40px;
    --uib-color: black;
    --uib-speed: .9s;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: var(--uib-size);
    width: var(--uib-size);
  }

  .dot {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 100%;
    width: 100%;
  }

  .dot::before {
    content: '';
    height: 20%;
    width: 20%;
    border-radius: 50%;
    background-color: var(--uib-color);
    transform: scale(0);
    opacity: 0.5;
    animation: pulse calc(var(--uib-speed) * 1.111) ease-in-out infinite;
    transition: background-color 0.3s ease;
  }

  .dot:nth-child(2) {
    transform: rotate(45deg);
  }

  .dot:nth-child(2)::before {
    animation-delay: calc(var(--uib-speed) * -0.875);
  }

  .dot:nth-child(3) {
    transform: rotate(90deg);
  }

  .dot:nth-child(3)::before {
    animation-delay: calc(var(--uib-speed) * -0.75);
  }

  .dot:nth-child(4) {
    transform: rotate(135deg);
  }

  .dot:nth-child(4)::before {
    animation-delay: calc(var(--uib-speed) * -0.625);
  }

  .dot:nth-child(5) {
    transform: rotate(180deg);
  }

  .dot:nth-child(5)::before {
    animation-delay: calc(var(--uib-speed) * -0.5);
  }

  .dot:nth-child(6) {
    transform: rotate(225deg);
  }

  .dot:nth-child(6)::before {
    animation-delay: calc(var(--uib-speed) * -0.375);
  }

  .dot:nth-child(7) {
    transform: rotate(270deg);
  }

  .dot:nth-child(7)::before {
    animation-delay: calc(var(--uib-speed) * -0.25);
  }

  .dot:nth-child(8) {
    transform: rotate(315deg);
  }

  .dot:nth-child(8)::before {
    animation-delay: calc(var(--uib-speed) * -0.125);
  }

  @keyframes pulse {
    0%,
    100% {
      transform: scale(0);
      opacity: 0.5;
    }

    50% {
      transform: scale(1);
      opacity: 1;
    }
  }
</style>



  `;
// showSuggestions function
const showSuggestions = (filteredSuggestions, container, hiddentag, input) => {
  container.empty(); // Clear previous suggestions

  if (filteredSuggestions.length === 0) {
    container.hide();
    return;
  }

  let activeIndex = -1; // Track active suggestion index

  // Add suggestions
  filteredSuggestions.forEach(function (suggestion, index) {
    const suggestionElement = $('<div class="suggestion-item"></div>');
    suggestionElement.append(
      '<span class="suggestion-title">' + suggestion.title + "</span>"
    );

    // Append code if it exists
    if (suggestion.code) {
      suggestionElement.append(
        '<span class="suggestion-code">' + suggestion.code + "</span>"
      );
    }

    container.append(suggestionElement);

    // Set event listener for each suggestion
    suggestionElement.on("click", function () {
      input.val(suggestion.title); // Set input value

      if (suggestion.value !== undefined) {
        hiddentag.val(suggestion.value).trigger("change"); // Update and trigger change
      } else {
        hiddentag.val("").trigger("change"); // Clear and trigger change
      }

      container.empty().hide(); // Clear and hide suggestions after selection
    });
  });

  container.show(); // Show suggestions

  // Key navigation for suggestions
  input.off("keydown").on("keydown", function (e) {
    const items = container.find(".suggestion-item");

    if (e.key === "ArrowDown") {
      e.preventDefault();
      activeIndex = (activeIndex + 1) % items.length; // Increment index
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      activeIndex = (activeIndex - 1 + items.length) % items.length; // Decrement index
    } else if (e.key === "Enter" && activeIndex >= 0) {
      e.preventDefault();
      items.eq(activeIndex).trigger("click"); // Select active item
      input.blur(); // Trigger blur after selection
      return;
    }

    // Highlight active suggestion
    items.removeClass("active");
    if (activeIndex >= 0) {
      const activeItem = items.eq(activeIndex);
      activeItem.addClass("active");

      // Smoothly scroll active suggestion into view
      activeItem[0].scrollIntoView({
        behavior: "smooth",
        block: "nearest",
      });
    }
  });

  // Toggle focus on click
  // input.off('click').on('click', function () {
  //   if (container.is(':visible')) {
  //     container.empty().hide(); // Hide if already visible
  //   } else {
  //     input.trigger('focus'); // Trigger focus to show suggestions
  //   }
  // });

  // Click outside to hide suggestions
  $(document).on("click", function (event) {
    if (
      !$(event.target).closest(container).length &&
      !$(event.target).closest(input).length
    ) {
      container.empty().hide();
    }
  });

  // Hide suggestions on blur
  input.on("blur", function () {
    setTimeout(() => container.empty().hide(), 150);
  });

  // Event handler to hide suggestions when any input, dropdown, or date picker is focused
  $("input, select, .datepicker").on("focus", function () {
    container.empty().hide(); // Hide suggestions
  });
};

const tabs_active = () => {
  const tabs = document.querySelector(".tabs");
  const tabElements = document.querySelectorAll(".tab");

  function updateActiveIndicator(activeTab) {
    const tabRect = activeTab.getBoundingClientRect();
    const tabsRect = tabs.getBoundingClientRect();

    // Calculate the position and width of the active tab relative to the tabs container
    const indicatorPosition = tabRect.left - tabsRect.left;
    const indicatorWidth = tabRect.width;

    // Set CSS variables dynamically
    tabs.style.setProperty("--indicator-position", `${indicatorPosition}px`);
    tabs.style.setProperty("--indicator-width", `${indicatorWidth}px`);
  }

  // Add click event listener to update the indicator when a tab is clicked
  tabElements.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabElements.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");
      updateActiveIndicator(tab);
    });
  });

  // Initialize the indicator position on page load
  const initialActiveTab = document.querySelector(".tab.active");
  if (initialActiveTab) {
    updateActiveIndicator(initialActiveTab);
  }
};

// Function to create a chip
function createChip(input, chipContainer, chip_id) {
  const chipText = input.val().trim();

  // Create chip element
  const chip = $("<div>")
    .addClass("chip")
    .text(chipText)
    .attr("data-chip-id", chip_id); // Add data-id attribute

  // Add close button to chip
  const closeBtn = $("<span>").addClass("chip-close-btn").text("×");
  closeBtn.on("click", function () {
    $(this).parent().remove(); // Remove chip on click
  });

  // Append close button and add chip to container
  chip.append(closeBtn);
  chipContainer.append(chip);
}

// Function to retrieve all chip values from a specific container
function getChipsValues(container) {
  return container
    .children(".chip")
    .map(function () {
      return $(this).text().replace("×", "").trim();
    })
    .get();
}

// Function to retrieve all chip values from a specific container
function getChipsId(container) {
  return container
    .find(".chip")
    .map(function () {
      return $(this).data("chip-id"); // Using .data() to get the value of the 'data-chip-id' attribute
    })
    .get(); // .get() returns the values as an array
}

function toggleAccordion(header) {
  const item = header.parentElement;
  const content = item.querySelector(".accordion-content");
  const isOpen = item.classList.contains("open");

  document.querySelectorAll(".accordion-item").forEach((el) => {
    el.classList.remove("open");
    el.querySelector(".accordion-content").style.display = "none";
    el.querySelector(".accordion-header").classList.remove("active");
  });

  if (!isOpen) {
    item.classList.add("open");
    content.style.display = "block";
    header.classList.add("active");
  }
}

const init_dropzones = () => {
  const dropzones = $(".dropzone");
  const fileInputs = $(".file-input");

  // Bind click event for browse text
  $(".browse-text").on("click", function () {
    $(this).parent().siblings(".file-input").click();
  });

  // Bind drag-and-drop events for each dropzone
  dropzones.each(function () {
    const dropzone = $(this);
    const previewContainer = dropzone
      .parent()
      .parent()
      .next(".preview-container"); // Get the corresponding preview container

    dropzone.on("dragover", function (e) {
      e.preventDefault();
      dropzone.addClass("dragover");
    });

    dropzone.on("dragleave", function () {
      dropzone.removeClass("dragover");
    });

    dropzone.on("drop", function (e) {
      e.preventDefault();
      dropzone.removeClass("dragover");
      const files = e.originalEvent.dataTransfer.files;
      handleFiles(files, previewContainer); // Pass the preview container
      // Set the files to the corresponding file inputs
      const fileInput = dropzone.parent().find(".file-input")[0];
      fileInput.files = files;
    });
  });

  // File input change event
  fileInputs.on("change", function (e) {
    const files = e.target.files;
    const previewContainer = $(this)
      .parent()
      .parent()
      .parent()
      .next(".preview-container"); // Find the corresponding preview container
    handleFiles(files, previewContainer); // Pass the preview container
    $(this).siblings(".previous-link").val("");
  });
};

// error log

const insert_error_log = (error_message) => {
  const error_location_href = window.location.href;
  return new Promise((resolve, reject) => {
    $.ajax({
      type: "POST",
      url: app_url + "/global/ajax/insert_error_log.php",
      data: {
        error_message: error_message,
        location_href: error_location_href,
        error_side: 1,
      },
      success: function (response) {
        response = JSON.parse(response);
        if (response.code !== 200) {
          showToast(response.status, response.message);
        }
        resolve(); // Resolve the promise
      },
      error: function (jqXHR) {
        const message =
          jqXHR.status === 401
            ? "Unauthorized access. Please check your credentials."
            : "An error occurred. Please try again.";
        showToast("error", message);
        reject(); // Reject the promise
      },
    });
  });
};

const load_error_popup = () => {
  const path_name = window.location.pathname;
  const lastSegment = path_name.substring(path_name.lastIndexOf("/") + 1);
  return new Promise((resolve, reject) => {
    $.ajax({
      type: "POST",
      url: app_url + "global/components/error_popup.php",
      data: {
        module_name: lastSegment,
      },
      success: function (response) {
        $("#error-popup").html(response);
        resolve(); // Resolve the promise
      },
      error: function (jqXHR) {
        const message =
          jqXHR.status === 401
            ? "Unauthorized access. Please check your credentials."
            : "An error occurred. Please try again.";
        showToast("error", message);
        reject(); // Reject the promise
      },
    });
  });
};

const updateUrl = ({ route, action, type, tab, id }) => {
  // Create a new URLSearchParams object from the current URL's search strings
  const urlParams = new URLSearchParams(window.location.search);

  // Add or remove parameters dynamically

  if (action) {
    urlParams.set("action", action);
  } else {
    urlParams.delete("action");
  }
  if (route) {
    urlParams.set("route", route);
  } else {
    urlParams.delete("route");
  }
  if (type) {
    urlParams.set("type", type);
  } else {
    urlParams.delete("type");
  }

  if (tab) {
    urlParams.set("tab", tab);
  } else {
    urlParams.delete("tab");
  }

  if (id) {
    urlParams.set("id", id);
  } else {
    urlParams.delete("id");
  }
  // Construct the new URL
  const newUrl = `${window.location.origin}${window.location.pathname
    }?${urlParams.toString()}`;

  // Update the browser's history without reloading the page
  window.history.pushState(null, "", newUrl);
};

function handleReload() {
  window.location.reload();
}

// Browser back/forward navigation
window.onpopstate = handleReload;

// Custom back button click
document.addEventListener("click", (event) => {
  if (event.target && event.target.id === "bg-card-back-button") {
    window.history.back();
    setTimeout(handleReload, 100); // Reuse the reload function with delay
  }
});
