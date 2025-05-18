export default class ModUnitEvaluation {
    constructor(el) {
        this.$el = $(el);
        this.$periodDropdown = this.$el.find(".period-unit-evaluation");
        this.$evaluationContent = this.$el.find("#unit-evaluation-content");
    }

    init() {
        this.bindEvents();
        this.initCKEditor();
    }

    bindEvents() {
        // Xử lý thay đổi năm học
        this.$periodDropdown.on("change", () => this.loadEvaluationByYear());
    }

    loadEvaluationByYear() {
        const selectedYear = this.$periodDropdown.val();

        $.ajax({
            url: `${window.location.origin}/unit-evaluation?year=${selectedYear}`,
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            success: (response) => {
                if (response.html) {
                    // Cập nhật nội dung đánh giá đơn vị
                    this.$evaluationContent.html(response.html);
                    this.reinitialize(); // Khởi tạo lại các phần tử và sự kiện sau khi cập nhật nội dung AJAX
                }
            },
            error: (xhr, status, error) => {
                console.error("Error loading unit evaluation:", error);
            },
        });
    }

    initCKEditor() {
        if (typeof CKEDITOR !== "undefined") {
            // Khởi tạo CKEditor cho các textarea hiện tại
            this.$el.find("textarea.ckeditor").each((index, textarea) => {
                if (!CKEDITOR.instances[textarea.id]) {
                    CKEDITOR.replace(textarea.id, {
                        height: 200,
                        toolbar: [
                            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                            { name: 'links', items: ['Link', 'Unlink'] },
                            { name: 'insert', items: ['Table', 'SpecialChar'] },
                            { name: 'tools', items: ['Maximize'] },
                            { name: 'document', items: ['Source'] }
                        ]
                    });
                }
            });
        }
    }

    reinitialize() {
        // Cập nhật lại các selector để trỏ đến các phần tử mới trong DOM
        this.$periodDropdown = this.$el.find(".period-unit-evaluation");

        // Khởi tạo lại CKEditor cho các textarea mới
        if (typeof CKEDITOR !== "undefined") {
            // Hủy các instance CKEditor hiện tại nếu có
            this.$el.find("textarea.ckeditor").each((index, textarea) => {
                const instance = CKEDITOR.instances[textarea.id];
                if (instance) {
                    instance.destroy(true);
                }
            });

            // Khởi tạo lại CKEditor
            this.initCKEditor();
        }
    }
}

new ModUnitEvaluation(".mod-unit-eval").init();