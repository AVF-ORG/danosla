import ApexCharts from 'apexcharts';

export const initSparklines = () => {
    const profitOptions = {
        series: [{
            data: [31, 40, 28, 51, 42, 109, 100]
        }],
        chart: {
            type: 'area',
            height: 50,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            opacity: 0.3,
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        colors: ['#71dd37'],
        tooltip: {
            enabled: false
        }
    };

    const salesOptions = {
        series: [{
            data: [15, 75, 45, 38, 97, 27, 81]
        }],
        chart: {
            type: 'area',
            height: 50,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            opacity: 0.3,
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.5,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        colors: ['#696cff'],
        tooltip: {
            enabled: false
        }
    };

    const profitEl = document.querySelector("#profitChart");
    if (profitEl) {
        new ApexCharts(profitEl, profitOptions).render();
    }

    const salesEl = document.querySelector("#salesChart");
    if (salesEl) {
        new ApexCharts(salesEl, salesOptions).render();
    }
}

export default initSparklines;
