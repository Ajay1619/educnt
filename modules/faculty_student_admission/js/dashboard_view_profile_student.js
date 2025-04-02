document.addEventListener("DOMContentLoaded", function () {
    // Data for Total Number of Students (Donut Chart)
    const studentsData = {
      active: 120,  // Active students count
      inactive: 30  // Inactive students count
    };
  
    // Data for Roles Assigned Students (Numeric Count)
    const rolesAssignedCount = 85;
  
    // Data for Recent Achievements Added (Numeric Count)
    const recentAchievementsCount = 12;
  
    // Render Donut Chart for Total Number of Students
    const studentsChartOptions = {
      chart: {
        type: 'donut',
      },
      series: [studentsData.active, studentsData.inactive],
      labels: ['Active', 'Inactive'],
      colors: ['#00C853', '#FF5252'], // Green for active, red for inactive
      legend: {
        position: 'bottom'
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%'
          }
        }
      }
    };
  
    const studentsChart = new ApexCharts(document.querySelector("#students-chart"), studentsChartOptions);
    studentsChart.render();
  
    // Display Numeric Count for Roles Assigned Students
    document.getElementById("roles-count").textContent = rolesAssignedCount;
  
    // Display Numeric Count for Recent Achievements Added
    document.getElementById("achievements-count").textContent = recentAchievementsCount;
  });
  