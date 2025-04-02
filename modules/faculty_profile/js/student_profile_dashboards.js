const generateRandomColor = () => {
  return `#${Math.floor(Math.random() * 16777215)
    .toString(16)
    .padStart(6, "0")}`;
};


const student_dept_count = () => {
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
        name: "Male Learners",
        data: teachingFaculty,
      },
      {
        name: "Female Learners",
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

  // Render the Chart
  const chart = new ApexCharts(
    document.querySelector("#faculty-strength-chart"),
    options
  );
  chart.render();
};

const student_committees_chart = () => {
  var options = {
    series: [14, 23, 21, 17, 25], // Sample data
    chart: {
      type: 'polarArea',
      width: '100%',
      height: 500,
    },
    labels: [
      'Program Committee',
      'Science Committee',
      'Sports Committee',
      'Research Committee',
      'Development Committee',
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

  var chart = new ApexCharts(document.querySelector("#student-committees-chart"), options);
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

  var chart = new ApexCharts(document.querySelector("#faculty-dept-achievements-history"), options);
  chart.render();
};


const student_activities_dept_faculty = () => {
  var options = {
    series: [{
      name: 'Activities Conducted',
      data: [15, 20, 10, 25, 18, 30, 12] // Data representing the number of activities per department
    }],
    annotations: {
      points: [{
        x: 'CSE',
        seriesIndex: 0,
        label: {
          borderColor: '#775DD0',
          offsetY: 0,
          style: {
            color: '#fff',
            background: '#775DD0',
          },
          text: 'Most active department'
        }
      }]
    },
    chart: {
      height: 350,
      type: 'bar',
    },
    plotOptions: {
      bar: {
        borderRadius: 10,
        columnWidth: '50%',
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      width: 0
    },
    grid: {
      row: {
        colors: ['#fff', '#f2f2f2']
      }
    },
    xaxis: {
      labels: {
        rotate: -45
      },
      categories: [
        'CSE',
        'ECE',
        'MECH',
        'BME',
        'EEE',
        'S&H',
        'MBA'
      ], // 7 Departments
      tickPlacement: 'on'
    },
    yaxis: {
      title: {
        text: 'Number of Activities'
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: "horizontal",
        shadeIntensity: 0.25,
        gradientToColors: undefined,
        inverseColors: true,
        opacityFrom: 0.85,
        opacityTo: 0.85,
        stops: [50, 0, 100]
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#student-activities-dept-chart"), options);
  chart.render();
};
