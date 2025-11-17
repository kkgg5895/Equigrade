<!-- AI Insights Section (without buttons) -->
<section class="mt-6 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
  <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
    🤖 AI Evaluation Summary
  </h3>

  <div class="grid md:grid-cols-2 gap-8 items-center">

    <!-- Rubric Chart -->
    <div class="w-full">
      <canvas id="rubricChart" height="200"></canvas>
    </div>

    <!-- Key Insights -->
    <div>
      <ul class="list-disc ml-5 text-gray-700 text-base space-y-2 leading-relaxed">
        <li>Overall report structure is strong and consistent.</li>
        <li>Some sections lack clarity and require additional diagrams.</li>
        <li>Writing tone is academic but can be more concise.</li>
        <li>Technical justification for certain design choices is missing.</li>
      </ul>
    </div>

  </div>
</section>

<!-- Chart.js script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('rubricChart').getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Content Accuracy', 'Technical Depth', 'Clarity', 'Writing Quality'],
      datasets: [{
        label: 'Score (%)',
        data: [88, 75, 70, 80], // Replace with PHP dynamic values if needed
        backgroundColor: ['#6366F1','#10B981','#F59E0B','#EF4444'],
        borderRadius: 8,
        barThickness: 28
      }]
    },
    options: {
      indexAxis: 'y',  // HORIZONTAL BAR CHART
      responsive: true,
      scales: {
        x: { beginAtZero: true, max: 100 },
        y: { ticks: { font: { size: 14 } } }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
</script>

