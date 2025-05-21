// Imports
import Chart from 'chart.js/auto';

export default class ModStatistics {
    constructor() {
        this.charts = {};
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initCharts();
        });
    }

    initCharts() {
        this.initQualityChart();
        this.initTitleChart();
    }

    initQualityChart() {
        const qualityCtx = document.getElementById('qualityChart');
        if (!qualityCtx) return;
        
        try {
            const qualityData = JSON.parse(qualityCtx.dataset.quality || '{}');
            
            this.charts.quality = new Chart(qualityCtx, {
                type: 'doughnut',
                data: {
                    labels: qualityData.labels || [],
                    datasets: [{
                        data: qualityData.data || [],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { font: { size: 12 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => this.formatTooltipLabel(context)
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Lỗi khi tạo biểu đồ phân bố xếp loại:', error);
        }
    }

    initTitleChart() {
        const titleCtx = document.getElementById('titleChart');
        if (!titleCtx) return;
        
        try {
            const titleData = JSON.parse(titleCtx.dataset.title || '{}');
            
            this.charts.title = new Chart(titleCtx, {
                type: 'bar',
                data: {
                    labels: titleData.labels || [],
                    datasets: [{
                        label: 'Số lượng',
                        data: titleData.data || [],
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        } catch (error) {
            console.error('Lỗi khi tạo biểu đồ danh hiệu:', error);
        }
    }
    
    formatTooltipLabel(context) {
        const label = context.label || '';
        const value = context.raw || 0;
        const total = context.dataset.data.reduce((a, b) => a + b, 0);
        const percentage = Math.round((value / total) * 100);
        return `${label}: ${value} (${percentage}%)`;
    }
    
    // Phương thức để cập nhật biểu đồ khi có dữ liệu mới
    updateCharts(qualityData, titleData) {
        if (this.charts.quality && qualityData) {
            this.charts.quality.data.labels = qualityData.labels;
            this.charts.quality.data.datasets[0].data = qualityData.data;
            this.charts.quality.update();
        }
        
        if (this.charts.title && titleData) {
            this.charts.title.data.labels = titleData.labels;
            this.charts.title.data.datasets[0].data = titleData.data;
            this.charts.title.update();
        }
    }
    
    // Phương thức để xử lý sự kiện thay đổi năm
    handleYearChange(yearSelectElement) {
        yearSelectElement.addEventListener('change', () => {
            // Có thể thêm mã xử lý AJAX để tải dữ liệu mới dựa vào năm được chọn
            // và sau đó cập nhật biểu đồ bằng phương thức updateCharts()
        });
    }
}

// Khởi tạo module
const statsModule = new ModStatistics();
statsModule.init();