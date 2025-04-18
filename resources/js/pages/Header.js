export default class Header {
    constructor(el) {
        this.$el = $(el);
        this.$profileButton = this.$el.find('.profile-button'); 
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
    }

    init() {
        this.bindEvents(); 
    }

    bindEvents() {
        // Mở popup khi click vào nút profile
        this.$profileButton.on('click', (e) => this.toggleProfilePopup(e));

        // Ẩn popup khi click vào popup
        this.$popup.on('click', (e) => {
            if (!$(e.target).closest('.profile-popup').length && !$(e.target).closest('.profile-button').length) {
                this.hideProfilePopup();
            }
        });

        // Ẩn popup khi click nút đóng
        this.$closeButtons.on('click', () => this.hideProfilePopup());
    }

    toggleProfilePopup(event) {
        event.stopPropagation();
        this.$popup.toggleClass('hidden');
    }

    hideProfilePopup() {
        this.$popup.addClass('hidden');
    }
}

// Khởi tạo khi DOM sẵn sàng
new Header('.header').init();