export default class ModEvaluation {
    constructor(el) {
        this.$el = $(el);
        this.$scoreInputs = this.$el.find(".rating");
        this.$averageScore = this.$el.find(".total-score");
        this.$periodDropdown = this.$el.find(".period-after-evaluation");
    }

    init() {
        this.bindEvents();
        this.bindAjaxEvents();
        // Tính điểm ban đầu
        this.calculateAverageScore();
    }

    bindEvents() {
        // Sử dụng event delegation để đảm bảo các phần tử mới thêm vào vẫn được gắn sự kiện
        this.$el.on("change", ".rating", () => this.calculateAverageScore());
    }

    calculateAverageScore() {
        let total = 0;
        let count = 0;

        // Tìm lại các input trong DOM hiện tại
        const $currentInputs = this.$el.find(".rating");

        $currentInputs.each((index, input) => {
            const value = parseFloat($(input).val());
            if (!isNaN(value)) {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : 0;

        // Tìm lại phần tử hiển thị điểm trong DOM hiện tại
        const $currentAverageScore = this.$el.find(".total-score");
        if ($currentAverageScore.length > 0) {
            $currentAverageScore.text(average);
        }
    }

    bindAjaxEvents() {
        // Gỡ bỏ sự kiện cũ trước khi gắn sự kiện mới
        this.$periodDropdown.off("change");

        // Gắn sự kiện mới
        this.$periodDropdown.on("change", () => this.loadEvaluationByYear());
    }

    loadEvaluationByYear() {
        const selectedYear = this.$periodDropdown.val();

        $.ajax({
            url: `${window.location.origin}/self-evaluation?year=${selectedYear}`,
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            success: (response) => {
                if (response.html) {
                    // Chỉ cập nhật nội dung đánh giá
                    $("#evaluation-content").html(response.html);
                    this.reinitialize(); // Khởi tạo lại các phần tử và sự kiện sau khi cập nhật nội dung AJAX
                }
            },
            error: (xhr, status, error) => {
                console.error("Error loading evaluation:", error);
            },
        });
    }

    reinitialize() {
        // Cập nhật lại các selector để trỏ đến các phần tử mới trong DOM
        this.$scoreInputs = this.$el.find(".rating");
        this.$averageScore = this.$el.find(".total-score");
        this.$periodDropdown = this.$el.find(".period-after-evaluation");

        // Khởi tạo lại CKEditor
        if (typeof CKEDITOR !== "undefined") {
            // Hủy các instance CKEditor chỉ liên quan đến các textarea hiện tại
            this.$el.find("textarea.ckeditor").each((index, textarea) => {
                const instance = CKEDITOR.instances[textarea.name];
                if (instance) {
                    instance.destroy(true); // Hủy instance CKEditor cũ
                }
            });

            // Khởi tạo lại CKEditor cho các textarea hiện tại
            this.$el.find("textarea.ckeditor").each((index, textarea) => {
                if (!CKEDITOR.instances[textarea.name]) {
                    CKEDITOR.replace(textarea); // Khởi tạo lại CKEditor
                }
            });
        }

        // Tính toán lại điểm trung bình sau khi nội dung đã được cập nhật
        this.calculateAverageScore();

        this.bindEvents();
        this.bindAjaxEvents();
    }
}

new ModEvaluation(".mod-self-eval").init();
