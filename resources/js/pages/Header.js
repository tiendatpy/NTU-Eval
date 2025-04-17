export default class Header {
    constructor(el) {
        this.$el = $(el); // Gốc của component
        this.$profileButton = this.$el.find('#profile-button'); // Nút hiển thị thông tin người dùng
        this.$profilePopup = this.$el.find('#profile-popup'); // Popup thông tin người dùng
    }

    init() {
        this.bindEvents(); // Gắn các sự kiện
    }

    bindEvents() {
        // Hiển thị hoặc ẩn popup khi click vào nút profile
        this.$profileButton.on('click', (e) => this.toggleProfilePopup(e));

        // Ẩn popup khi click ra ngoài
        $(document).on('click', (e) => this.hideProfilePopup(e));
    }

    toggleProfilePopup(event) {
        event.stopPropagation(); // Ngăn chặn sự kiện lan ra ngoài
        this.$profilePopup.toggleClass('hidden'); // Thêm hoặc xóa class `hidden` để hiển thị/ẩn popup
    }

    hideProfilePopup(event) {
        // Nếu click không nằm trong popup hoặc nút profile, ẩn popup
        if (!this.$profilePopup.is(event.target) && this.$profilePopup.has(event.target).length === 0 &&
            !this.$profileButton.is(event.target) && this.$profileButton.has(event.target).length === 0) {
            this.$profilePopup.addClass('hidden');
        }
    }
}

// Khởi tạo Header
new Header('.header').init();