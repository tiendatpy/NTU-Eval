export default class ModEvaluation {
    constructor(el) {
        this.$el = $(el);
        this.$scoreInputs = this.$el.find('.score-input'); 
        this.$averageScore = this.$el.find('.total-score');
        this.$periodDropdown = this.$el.find('.period-after-evaluation'); // Dropdown chọn năm
    }

    init() {
        this.bindEvents();
        this.bindAjaxEvents(); // Gắn sự kiện AJAX
    }

    bindEvents() {
        this.$scoreInputs.on('input', () => this.calculateAverageScore());
    }

    calculateAverageScore() {
        let total = 0;
        let count = 0;

        this.$scoreInputs.each((index, input) => {
            const value = parseFloat($(input).val());
            if (!isNaN(value)) {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : 0; 
        this.$averageScore.text(average);
    }

    bindAjaxEvents() {
        this.$periodDropdown.on('change', () => this.loadEvaluationByYear());
    }

    loadEvaluationByYear() {
        const selectedYear = this.$periodDropdown.val();

        $.ajax({
            url: `${window.location.origin}/self-evaluation?year=${selectedYear}`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                if (response.html) {
                    // Chỉ cập nhật nội dung đánh giá
                    $('#evaluation-content').html(response.html);
                    this.reinitialize(); // Reinitialize elements and events after AJAX content update
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading evaluation:', error);
            },
        });
    }

    reinitialize() {
        // Reinitialize elements and events after AJAX content update
        this.$scoreInputs = this.$el.find('.score-input');
        this.$averageScore = this.$el.find('.total-score');
        this.$periodDropdown = this.$el.find('.period-after-evaluation');

        // Khởi tạo lại CKEditor
        // if (typeof CKEDITOR !== 'undefined') {
        //     // Hủy các instance CKEditor chỉ liên quan đến các textarea hiện tại
        //     this.$el.find('textarea.ckeditor').each((index, textarea) => {
        //         const instance = CKEDITOR.instances[textarea.name];
        //         if (instance) {
        //             instance.destroy(true); // Hủy instance CKEditor cũ
        //         }
        //     });

        //     // Khởi tạo lại CKEditor cho các textarea hiện tại
        //     this.$el.find('textarea.ckeditor').each((index, textarea) => {
        //         if (!CKEDITOR.instances[textarea.name]) {
        //             CKEDITOR.replace(textarea); // Khởi tạo lại CKEditor
        //         }
        //     });
        // }

        this.bindEvents();
        this.bindAjaxEvents();
    }
}

new ModEvaluation('.mod-self-eval').init();