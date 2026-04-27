import ApexCharts from 'apexcharts';

export const initChartOne = () => {
    const chartElement = document.querySelector('#chartOne');
    if (!chartElement) return;

    const chartOneOptions = {
        series: [{
            name: "2025",
            data: [18, 7, 15, 29, 18, 12, 9],
        }, {
            name: "2024",
            data: [-13, -18, -9, -14, -5, -17, -15],
        }],
        colors: ["#696cff", "#03c3ec"],
        chart: {
            fontFamily: "Public Sans, sans-serif",
            type: "bar",
            height: 300,
            stacked: true,
            toolbar: {
                show: false,
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
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ["transparent"],
        },
        xaxis: {
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#a1acb8",
                    fontSize: "13px",
                },
            },
        },
        legend: {
            show: false,
        },
        yaxis: {
            show: false,
        },
        grid: {
            show: true,
            borderColor: "#f1f1f1",
            strokeDashArray: 10,
            padding: {
                top: 0,
                bottom: -15,
                left: -10,
                right: 0
            }
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            x: {
                show: false,
            },
            y: {
                formatter: function (val) {
                    return val + 'k';
                },
            },
        },
    };

    const chart = new ApexCharts(chartElement, chartOneOptions);
    chart.render();

    return chart;
};

export default initChartOne;
