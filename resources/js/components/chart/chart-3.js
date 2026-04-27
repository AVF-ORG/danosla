import ApexCharts from 'apexcharts';

export const initChartThree = () => {
    const chartElement = document.querySelector('#chartThree');

    if (chartElement) {
        const chartThreeOptions = {
            series: [{
                name: "Income",
                data: [180, 200, 150, 220, 190, 250, 210]
            }],
            chart: {
                height: 200,
                type: 'area',
                toolbar: {
                    show: false
                },
                sparkline: {
                    enabled: false
                },
                fontFamily: 'Public Sans, sans-serif'
            },
            colors: ['#696cff'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                show: true,
                borderColor: '#f1f1f1',
                strokeDashArray: 10,
                padding: {
                    top: 0,
                    bottom: 0,
                    left: -10,
                    right: 0
                }
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#a1acb8',
                        fontSize: '13px'
                    }
                }
            },
            yaxis: {
                show: false
            },
            tooltip: {
                x: {
                    show: false
                }
            }
        };

        const chart = new ApexCharts(chartElement, chartThreeOptions);
        chart.render();
        return chart;
    }
}

export default initChartThree;
