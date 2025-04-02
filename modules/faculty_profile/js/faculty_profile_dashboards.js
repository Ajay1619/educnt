const generateRandomColor = () => {
  return `#${Math.floor(Math.random() * 16777215)
    .toString(16)
    .padStart(6, "0")}`;
};


const faculty_dept_count = () => {
  // Sample Data
  const departments = ["CSE", "MECH", "ECE", "EEE", "BME", "MBA", "S&H"];
  const teachingFaculty = [20, 15, 18, 12, 10, 8, 5];
  const nonTeachingCounts = [15, 20, 12, 18, 10, 8, 5];

  // Function to generate random colors


  // Random colors for the chart
  const randomColors = [
    generateRandomColor(),
    generateRandomColor(),
  ];

  // Chart Options
  const options = {
    chart: {
      type: "bar",
      height: 350,
      toolbar: {
        show: true,
      },
      zoom: {
        enabled: true,
      },
    },
    series: [
      {
        name: "Teaching Faculty",
        data: teachingFaculty,
      },
      {
        name: "Non - Teaching Faculty",
        data: nonTeachingCounts,
      },
    ],
    xaxis: {
      categories: departments,
      title: {
        text: "Academic Departments",
      },
    },
    yaxis: {
      title: {
        text: "Faculty Strength",
      },
    },
    legend: {
      position: "top",
      horizontalAlign: "center",
    },
    fill: {
      opacity: 1,
    },
    colors: randomColors, // Use dynamically generated colors
    dataLabels: {
      enabled: false,
    },
    tooltip: {
      shared: true,
      intersect: false,
    },
  };

  $("#faculty-strength-chart").empty()
  // Render the Chart
  const chart = new ApexCharts(
    document.querySelector("#faculty-strength-chart"),
    options
  );
  chart.render();
};

const faculty_designation_chart = () => {
  var options = {
    series: [14, 23, 21, 17, 25], // Sample data
    chart: {
      type: 'polarArea',
      width: '100%',
      height: 500,
    },
    labels: [
      'Professor',
      'Associate Professor',
      'Assistant Professor',
      'Lecturer',
      'Trainee',
    ], // Labels for the chart
    stroke: {
      colors: ['#fff']
    },
    fill: {
      opacity: 0.8
    },
    legend: {
      position: 'bottom', // Move legend to bottom
      horizontalAlign: 'center', // Center-align the legend
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 375,
          height: 500
        },
        legend: {
          position: 'bottom' // Keep legend at bottom for responsive view
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#faculty-designation-chart"), options);
  chart.render();
};

// piechart achievements

const faculty_dept_achievements_history = () => {
  const randomColors = generateRandomColor();
  // Sample Data for Departmental Achievements
  var options = {
    series: [
      {
        name: "Achievements",
        data: [10, 41, 35, 51, 49, 62, 69], // Replace with actual achievement counts
      }
    ],
    chart: {
      height: 350,
      type: 'line',
      zoom: {
        enabled: false,
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      curve: 'straight', // Smooth line for better visual appeal
    },
    grid: {
      row: {
        colors: ['#f3f3f3', 'transparent'], // Alternating grid background
        opacity: 0.5,
      },
    },
    xaxis: {
      categories: ['2018', '2019', '2020', '2021', '2022', '2023', '2024'],
      title: {
        text: 'Year',
        style: {
          fontSize: '12px',
          fontWeight: 'bold',
        }
      },
    },
    yaxis: {
      title: {
        text: 'Achievement Count',
        style: {
          fontSize: '12px',
          fontWeight: 'bold',
        }
      },
    },
    tooltip: {
      enabled: true,
      shared: true,
      intersect: false,
      x: {
        format: 'yyyy' // Format for year display in tooltip
      }
    },
    colors: [randomColors], // Primary line color
    markers: {
      size: 5,
      colors: [randomColors],
    },
    legend: {
      position: 'top',
    },
  };
  $("#faculty-dept-achievements-history").empty()
  var chart = new ApexCharts(document.querySelector("#faculty-dept-achievements-history"), options);
  chart.render();
};


const faculty_experience_faculty = () => {
  var options = {
    series: [
      {
        name: '0-1 Year Experience',
        data: [44, 55, 41, 67, 22, 43, 30], // Example data for 7 departments
      },
      {
        name: '1-5 Years Experience',
        data: [13, 23, 20, 8, 13, 27, 18],
      },
      {
        name: '5-10 Years Experience',
        data: [11, 17, 15, 15, 21, 14, 12],
      },
      {
        name: '10+ Years Experience',
        data: [21, 7, 25, 13, 22, 8, 14],
      }
    ],
    chart: {
      type: 'bar',
      height: 350,
      stacked: true,
      toolbar: {
        show: true,
      },
      zoom: {
        enabled: true,
      },
    },
    responsive: [{
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0,
        },
      },
    }],
    plotOptions: {
      bar: {
        horizontal: false,
        borderRadius: 10,
        borderRadiusApplication: 'end',
        borderRadiusWhenStacked: 'last',
        dataLabels: {
          total: {
            enabled: true,
            style: {
              fontSize: '13px',
              fontWeight: 900,
            },
          },
        },
      },
    },
    xaxis: {
      type: 'category',
      categories: [
        'Dept A',
        'Dept B',
        'Dept C',
        'Dept D',
        'Dept E',
        'Dept F',
        'Dept G',
      ], // Replace with actual department names
      title: {
        text: 'Departments',
        style: {
          fontSize: '12px',
          fontWeight: 'bold',
        },
      },
    },
    yaxis: {
      title: {
        text: 'Number of Faculty',
        style: {
          fontSize: '12px',
          fontWeight: 'bold',
        },
      },
    },
    legend: {
      position: 'bottom', // Move legend to bottom for better readability
      horizontalAlign: 'center',
      offsetY: 10,
    },
    fill: {
      opacity: 1,
    },
    tooltip: {
      shared: true,
      intersect: false,
    },
    colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560'], // Colors for the experience levels
  };

  var chart = new ApexCharts(
    document.querySelector("#faculty-experience-chart"),
    options
  );
  chart.render();
};


