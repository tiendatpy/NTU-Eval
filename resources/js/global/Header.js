export default class Header {
    constructor(el) {
        this.$el = $(el);
        this.$profileButton = this.$el.find('.profile-button'); 
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
        this.$logoutButton = this.$el.find('.logout-btn');
    }

    init() {
        this.bindEvents(); 
        this.logout();
    }

    bindEvents() {
        // Mở popup khi click vào nút profile
        this.$profileButton.on('click', (e) => this.toggleProfilePopup(e));

        // Ẩn popup khi click vào overlay
        this.$popup.on('click', (e) => {
            if (!$(e.target).closest('.profile-popup').length && !$(e.target).closest('.profile-button').length) {
                this.hideProfilePopup();
            }
        });

        this.$closeButtons.on('click', () => this.hideProfilePopup());
    }

    toggleProfilePopup(event) {
        event.stopPropagation();
        this.$popup.toggleClass('hidden');
    }

    hideProfilePopup() {
        this.$popup.addClass('hidden');
    }

    logout() {
        this.$logoutButton.closest('form').on('submit', (e) => e.preventDefault());
        
        this.$logoutButton.on('click', (e) => {
            e.preventDefault();
            const form = this.$logoutButton.closest('form');
            
            if (window.confirm('Bạn có chắc chắn muốn đăng xuất không?')) {
                form.off('submit').submit();
            }
        });
    }
}

new Header('.header').init();