export default class ModAllTitleNominations {
    constructor(el) {
        this.$el = $(el);
        this.$editButtons = this.$el.find('.edit-btn');
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
        this.$form = this.$el.find('.edit-review-form');
        this.$reviewInput = this.$el.find('.review-nomination');
        this.$yearFilter = this.$el.find('#year');
        this.$yearFilterForm = this.$el.find('#yearFilterForm');
    }

    init() {
        this.bindEvents();
        this.initCKEditor();
    }

    bindEvents() {
        // Các event xử lý popup sẵn có
        this.$editButtons.on("click", (e) => this.togglePopup(e));

        this.$popup.on("click", (e) => {
            if(!$(e.target).closest('.review-popup').length) {
                this.hidePopup();
            }
        });

        this.$closeButtons.on("click", (e) => {
            e.preventDefault();
            this.hidePopup();
        });

        // Thêm event cho dropdown lọc theo năm
        this.$yearFilter.on("change", () => this.submitYearFilter());
    }

    togglePopup(event) {
        event.stopPropagation();

        // Lấy ID và review từ thuộc tính data của nút chỉnh sửa
        const $button = $(event.currentTarget);
        const nominationId = $button.data('id');
        const review = $button.data('review') || '';
        
        // Điền dữ liệu vào form
        this.$form.attr('action', `/all-title-nominations/${nominationId}`); // Đặt action động
        
        // Cập nhật nội dung trong CKEditor nếu có
        if (CKEDITOR.instances['review']) {
            CKEDITOR.instances['review'].setData(review);
        } else {
            this.$reviewInput.val(review); // Điền giá trị review vào textarea nếu không có CKEditor
        }

        // Hiển thị pop-up
        this.$popup.toggleClass("hidden");
    }

    hidePopup() {
        this.$popup.addClass('hidden');
    }

    submitYearFilter() {
        this.$yearFilterForm.submit();
    }

    initCKEditor() {
        // Kiểm tra xem có textarea với class review-nomination không
        const $textarea = this.$el.find('.review-nomination');
        if ($textarea.length > 0 && typeof CKEDITOR !== 'undefined') {
            // Nếu CKEditor đã được load, khởi tạo nó cho textarea
            if (!CKEDITOR.instances[$textarea.attr('id') || 'review']) {
                CKEDITOR.replace($textarea.attr('id') || 'review', {
                    toolbar: [
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'tools', items: ['Maximize'] }
                    ],
                    height: 200
                });
            }
        }
    }

    // Thêm phương thức để xử lý khi nội dung trang được cập nhật qua AJAX
    reInitialize() {
        // Cập nhật lại các phần tử DOM sau khi trang được cập nhật
        this.$editButtons = this.$el.find('.edit-btn');
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
        this.$reviewInput = this.$el.find('.review-nomination');
        
        // Gán lại các sự kiện
        this.bindEvents();
        
        // Khởi tạo lại CKEditor nếu cần
        this.initCKEditor();
    }
}

new ModAllTitleNominations('.mod-all-title-nominations').init();