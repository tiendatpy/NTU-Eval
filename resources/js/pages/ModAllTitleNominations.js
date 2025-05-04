export default class ModAllTitleNominations {
    constructor(el) {
        this.$el = $(el);
        this.$editButtons = this.$el.find('.edit-btn');
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
        this.$form = this.$el.find('.edit-review-form');
        this.$reviewInput = this.$el.find('.review-nomination'); 
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.$editButtons.on("click", (e) => this.togglePopup(e));

        this.$popup.on("click", (e) => {
            if(!$(e.target).closest('.review-popup').length) {
                this.hidePopup();
            }
        });

        this.$closeButtons.on("click", (e) => {
            e.preventDefault();
            this.hidePopup()
        })
    }

    togglePopup(event) {
        event.stopPropagation();

        // Lấy ID và review từ thuộc tính data của nút chỉnh sửa
        const $button = $(event.currentTarget);
        const nominationId = $button.data('id');
        const review = $button.data('review');
        // Điền dữ liệu vào form
        this.$form.attr('action', `/title-nominations/${nominationId}`); // Đặt action động
        this.$reviewInput.val(review); // Điền giá trị review vào textarea

        if (CKEDITOR.instances['review']) {
            CKEDITOR.instances['review'].setData(review);
        }

        // Hiển thị pop-up
        this.$popup.toggleClass("hidden");
    }

    hidePopup() {
        this.$popup.addClass('hidden');
    }
}
new ModAllTitleNominations('.all-title-nominations').init();
