export default class Header {
    constructor(el) {
        this.$el = $(el);
        this.$profileButton = this.$el.find('.profile-button'); 
        this.$popup = this.$el.find('.js-popup');
        this.$closeButtons = this.$el.find('.close-popup');
        this.$logoutButton = this.$el.find('.logout-btn');
        
        // Thêm hamburger menu và sidebar
        this.$hamburgerMenu = this.$el.find('.hamburger-menu button');
        this.$sidebar = $('.sidebar');  // Đây là selector cho sidebar của bạn, điều chỉnh nếu cần
        this.$mainContent = $('.main-content'); // Giả sử có một container chứa nội dung chính
        
        // Lưu trạng thái sidebar
        this.sidebarVisible = true;
        
        // Kiểm tra trạng thái ban đầu của sidebar trên mobile
        if (window.innerWidth < 1024) {
            this.sidebarVisible = false;
            $('body').addClass('sidebar-collapsed');
            this.$sidebar.addClass('sidebar-hidden');
            this.$mainContent.addClass('content-expanded');
        }
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
        
        // Toggle sidebar khi click vào hamburger menu
        this.$hamburgerMenu.on('click', () => this.toggleSidebar());
        
        // Xử lý responsive
        // $(window).on('resize', () => this.handleResize());
    }

    toggleProfilePopup(event) {
        event.stopPropagation();
        this.$popup.toggleClass('hidden');
    }

    hideProfilePopup() {
        this.$popup.addClass('hidden');
    }
    
    toggleSidebar() {
        // Toggle class sidebar-collapsed trên body để có thể styling qua CSS
        $('body').toggleClass('sidebar-collapsed');
        
        // Nếu sidebar đang hiển thị, ẩn nó đi
        if (this.sidebarVisible) {
            this.$sidebar.addClass('sidebar-hidden');
            this.$mainContent.addClass('content-expanded');
            
            // Animate icon hamburger menu
            this.$hamburgerMenu.find('span').removeClass('icon-menu-alt-1').addClass('icon-close');
        } else {
            // Nếu sidebar đang ẩn, hiển thị nó
            this.$sidebar.removeClass('sidebar-hidden');
            this.$mainContent.removeClass('content-expanded');
            
            // Animate icon hamburger menu
            this.$hamburgerMenu.find('span').removeClass('icon-close').addClass('icon-menu-alt-1');
        }
        
        // Đảo ngược trạng thái
        this.sidebarVisible = !this.sidebarVisible;
    }
    
    // handleResize() {
    //     const isSmallScreen = window.innerWidth < 1024;
        
    //     // Nếu đang ở màn hình nhỏ và sidebar đang hiển thị, ẩn nó đi
    //     if (isSmallScreen && this.sidebarVisible) {
    //         this.sidebarVisible = false;
    //         $('body').addClass('sidebar-collapsed');
    //         this.$sidebar.addClass('sidebar-hidden');
    //         this.$mainContent.addClass('content-expanded');
    //         this.$hamburgerMenu.find('span').removeClass('icon-menu-alt-1').addClass('icon-menu');
    //     } 
    //     // Nếu đang ở màn hình lớn và sidebar đang ẩn (và không phải do người dùng click), hiển thị nó
    //     else if (!isSmallScreen && !this.sidebarVisible && !$('body').hasClass('user-collapsed')) {
    //         this.sidebarVisible = true;
    //         $('body').removeClass('sidebar-collapsed');
    //         this.$sidebar.removeClass('sidebar-hidden');
    //         this.$mainContent.removeClass('content-expanded');
    //         this.$hamburgerMenu.find('span').removeClass('icon-menu').addClass('icon-menu-alt-1');
    //     }
    // }

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

$(document).ready(function() {
    new Header('.header').init();
});