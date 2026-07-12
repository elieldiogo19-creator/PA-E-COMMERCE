/* ============================================
   DASHBOARD.JS - Gráficos do Painel Admin
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ============= CORES =============
    const CORES = {
        dourado:   '#d4af37',
        laranja:   '#e67e22',
        laranjaC:  '#f39c12',
        verde:     '#10b981',
        azul:      '#3b82f6',
        vermelho:  '#ef4444',
        cinza:     '#94a3b8'
    };

    // ============= GRÁFICO 1 – Vendas Mensais =============
    const ctxVendas = document.getElementById('chartVendas');
    if (ctxVendas && typeof Chart !== 'undefined') {

        const labels = vendasMensais.map(v => {
            const [ano, mes] = v.mes.split('-');
            const nomes = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
            return `${nomes[parseInt(mes)-1]}/${ano.slice(2)}`;
        });
        const valores = vendasMensais.map(v => parseFloat(v.total));

        const gradient = ctxVendas.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(212, 175, 55, 0.4)');
        gradient.addColorStop(1, 'rgba(212, 175, 55, 0)');

        new Chart(ctxVendas, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Sem dados'],
                datasets: [{
                    label: 'Vendas (Kz)',
                    data: valores.length ? valores : [0],
                    borderColor: CORES.dourado,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: CORES.dourado,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#d4af37',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => 'Kz ' + ctx.parsed.y.toLocaleString('pt-AO', { minimumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            color: '#7f8c8d',
                            callback: (v) => 'Kz ' + (v/1000).toFixed(0) + 'k'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#7f8c8d' }
                    }
                }
            }
        });
    }

    // ============= GRÁFICO 2 – Pedidos por Status =============
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus && typeof Chart !== 'undefined') {

        const labels = pedidosStatus.map(p => p.status.charAt(0).toUpperCase() + p.status.slice(1));
        const valores = pedidosStatus.map(p => parseInt(p.total));

        const paletaStatus = pedidosStatus.map(p => {
            const s = p.status.toLowerCase();
            if (s === 'confirmado') return CORES.verde;
            if (s === 'pendente')   return CORES.laranjaC;
            if (s === 'cancelado')  return CORES.vermelho;
            if (s === 'enviado')    return CORES.azul;
            return CORES.cinza;
        });

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labels.length ? labels : ['Sem pedidos'],
                datasets: [{
                    data: valores.length ? valores : [1],
                    backgroundColor: paletaStatus.length ? paletaStatus : [CORES.cinza],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#2c3e50',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#d4af37',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });
    }
});