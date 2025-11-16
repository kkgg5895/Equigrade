<!-- AI Insights Section (without buttons) -->
<section class="mt-6 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
  <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
    🤖 AI Evaluation Summary
  </h3>

  <div class="grid md:grid-cols-2 gap-6 items-center">
    <!-- Rubric Chart -->
    <div>
      <canvas id="rubricChart"></canvas>
    </div>

    <!-- Key Insights -->
    <div>
      <ul class="list-disc ml-5 text-gray-700 text-sm space-y-1">
        <li>Overall report structure is strong and consistent.</li>
        <li>Some sections lack clarity and should include more diagrams.</li>
        <li>Writing tone is academic but could use concise phrasing.</li>
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
        data: [88, 75, 70, 80], // Example data; you can pull these dynamically from PHP if needed
        backgroundColor: ['#6366F1','#10B981','#F59E0B','#EF4444'],
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, max: 100 }
      }
    }
  });
</script>
