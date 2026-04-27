import ApexCharts from 'apexcharts';

export const initOrderChart = () => {
    const chartEl = document.querySelector("#orderChart");
    if (!chartEl) return;

    const orderChartOptions = {
        series: [{
            data: [180, 110, 140, 145, 200, 170, 190, 160, 230]
        }],
        chart: {
            height: 80,
            type: 'area',
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: true
            }
        },
        colors: ['#71dd37'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.5,
                opacityTo: 0.1,
                stops: [0, 80, 100]
            }
        },
        tooltip: {
            enabled: false
        }
    };

    const chart = new ApexCharts(chartEl, orderChartOptions);
    chart.render();
};

export default initOrderChart;
