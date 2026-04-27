import './bootstrap';
// import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import Swal from 'sweetalert2';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';



// window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;
window.Swal = Swal;

// Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#profitChart') || document.querySelector('#salesChart')) {
        import('./components/chart/sparklines').then(module => module.initSparklines());
    }
    if (document.querySelector('#orderChart')) {
        import('./components/chart/chart-order').then(module => module.initOrderChart());
    }
    if (document.querySelector('#growthChart')) {
        import('./components/chart/growth-chart').then(module => module.initGrowthChart());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-profile').then(module => module.initProfileChart());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
});
