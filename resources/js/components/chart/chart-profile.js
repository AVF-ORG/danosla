import ApexCharts from 'apexcharts';

export const initProfileChart = () => {
    const chartEl = document.querySelector("#chartThirteen");
    if (!chartEl) return;

    const profileChartOptions = {
        series: [{
            data: [120, 180, 140, 200, 160, 190, 150, 240, 180, 220, 170, 280]
        }],
        chart: {
            height: 90,
            type: 'line',
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: true
            }
        },
        colors: ['#ffab00'],
        stroke: {
            curve: 'smooth',
            width: 3,
            lineCap: 'round'
        },
        tooltip: {
            enabled: false
        },
        grid: {
            padding: {
                top: 10,
                bottom: 10
            }
        }
    };

    const chart = new ApexCharts(chartEl, profileChartOptions);
    chart.render();
};

export default initProfileChart;
