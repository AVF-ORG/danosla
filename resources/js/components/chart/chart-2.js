import ApexCharts from 'apexcharts';

export const initChartTwo = () => {
    const chartElement = document.querySelector('#chartTwo');

    if (chartElement) {
        const chartTwoOptions = {
            series: [85, 45, 40, 50],
            labels: ['Electronic', 'Fashion', 'Decor', 'Sports'],
            colors: ["#696cff", "#03c3ec", "#71dd37", "#8592a3"],
            chart: {
                fontFamily: "Public Sans, sans-serif",
                type: "donut",
                height: 165,
                sparkline: {
                    enabled: false,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "33%",
                    borderRadius: 5,
                    borderRadiusApplication: "end",
                    borderRadiusWhenStacked: "last",
                },
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            value: {
                                fontSize: '1.2rem',
                                fontWeight: '600',
                                color: '#566a7f',
                                offsetY: -10,
                                formatter: function (val) {
                                    return val + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Weekly',
                                color: '#a1acb8',
                                formatter: function (w) {
                                    return '38%';
                                }
                            }
                        }
                    }
                }
            },
            stroke: {
                width: 5,
                colors: ["#ffffff"],
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
        };

        const chart = new ApexCharts(chartElement, chartTwoOptions);
        chart.render();
        return chart;
    }
}

export default initChartTwo;
