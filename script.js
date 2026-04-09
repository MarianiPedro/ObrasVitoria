const map = L.map('map').setView([-19.9, -40.3], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
    maxZoom: 18
}).addTo(map);

// Ícone customizado
const icone = L.divIcon({
    className: '',
    html: `<div style="
        width: 12px; height: 12px;
        background: #e74c3c;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0,0,0,0.4);
    "></div>`,
    iconSize: [12, 12],
    iconAnchor: [6, 6]
});

obras.forEach(o => {
    const popup = `
        <strong>${o.obra.substring(0, 80)}${o.obra.length > 80 ? '…' : ''}</strong>
        📍 ${o.endereco}<br>
        🏙️ ${o.municipio}
    `;
    L.marker([o.lat, o.lng], { icon: icone })
        .addTo(map)
        .bindPopup(popup);
});

const cores = [
    '#2e86c1','#e74c3c','#2ecc71','#f39c12','#9b59b6',
    '#1abc9c','#e67e22','#34495e','#e91e63','#00bcd4','#95a5a6'
];

const ctx = document.getElementById('grafico').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: municipios,
        datasets: [{
            data: contagem,
            backgroundColor: cores.slice(0, municipios.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { size: 11 },
                    padding: 12,
                    boxWidth: 14
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const pct = ((ctx.parsed / total) * 100).toFixed(1);
                        return ` ${ctx.label}: ${ctx.parsed} obra(s) (${pct}%)`;
                    }
                }
            }
        }
    }
});