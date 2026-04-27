import ApexCharts from 'apexcharts';

export const initGrowthChart = () => {
    const chartEl = document.querySelector("#growthChart");
    if (!chartEl) return;

    const growthChartOptions = {
        series: [62],
        labels: ['Growth'],
        chart: {
            height: 200,
            type: 'radialBar',
            sparkline: {
                enabled: true
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    size: '70%',
                },
                track: {
                    strokeWidth: '80%',
                    background: '#e9ebed'
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        offsetY: 10,
                        color: '#566a7f',
                        fontSize: '22px',
                        fontWeight: '600',
                        formatter: function (val) {
                            return parseInt(val) + '%';
                        }
                    }
                }
            }
        },
        colors: ['#696cff'],
        stroke: {
            lineCap: 'round'
        },
        grid: {
            padding: {
                top: -10,
                bottom: -15
            }
        },
        states: {
            hover: {
                filter: {
                    type: 'none'
                }
            },
            active: {
                filter: {
                    type: 'none'
                }
            }
        }
    };

    const chart = new ApexCharts(chartEl, growthChartOptions);
    chart.render();
};

export default initGrowthChart;
