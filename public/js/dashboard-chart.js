document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('omzetChart');
    
    if (canvas) {
        // Ambil data mentah dari atribut HTML
        const rawLabels = canvas.getAttribute('data-labels') || '[]';
        const rawValues = canvas.getAttribute('data-values') || '[]';
        
        // Parse JSON ke array
        const chartLabels = JSON.parse(rawLabels);
        const parsedValues = JSON.parse(rawValues);
        
        // Bagi setiap nilai dengan 1 juta agar sesuai dengan format "Rp ... jt" di layar
        const chartData = parsedValues.map(value => value / 1000000);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: chartData,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    pointBackgroundColor: '#22c55e',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            callback: function(value) { 
                                return 'Rp ' + value + ' jt'; 
                            } 
                        }
                    },
                    x: { 
                        grid: { display: false } 
                    }
                }
            }
        });
    }
});