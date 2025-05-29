export default class ModPeriodManagement {
    constructor(el) {
        this.$el = $(el);
        this.$toggles = this.$el.find('.toggle-checkbox');
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
    }

    init() {
        if (this.$el.length) {
            this.bindEvents();
        }
    }

    bindEvents() {
        this.$toggles.on('change', (e) => this.handleToggleStatus(e));
    }

    handleToggleStatus(event) {
        const $toggle = $(event.currentTarget);
        const periodId = $toggle.data('period-id');
        const isChecked = $toggle.prop('checked');
        const $row = $toggle.closest('tr');
        const $statusBadge = $row.find('.status-badge');
        
        // Disable toggle while processing
        $toggle.prop('disabled', true);
        
        // AJAX request
        $.ajax({
            url: `/periods/toggle-status/${periodId}`,
            method: 'POST',
            data: {
                _token: this.csrfToken  // Thêm CSRF token vào data
            },
            headers: {
                'X-CSRF-TOKEN': this.csrfToken  // Cũng gửi trong header
            },
            dataType: 'json',
            success: (data) => {
                if (data.success) {
                    // Ensure toggle status reflects server state
                    $toggle.prop('checked', data.is_open);
                    
                    // Cập nhật trạng thái hiển thị (badge)
                    if ($statusBadge.length) {
                        if (data.is_open) {
                            $statusBadge.text('Mở');
                            $statusBadge.attr('class', 'status-badge px-2 py-1 rounded-full text-sm bg-green-100 text-green-800');
                        } else {
                            $statusBadge.text('Đóng');
                            $statusBadge.attr('class', 'status-badge px-2 py-1 rounded-full text-sm bg-red-100 text-red-800');
                        }
                    }
                } else {
                    // Revert toggle to original state
                    $toggle.prop('checked', !isChecked);
                }
            },
            error: (error) => {
                console.error('Đã xảy ra lỗi:', error);
                // Revert toggle to original state
                $toggle.prop('checked', !isChecked);
            },
            complete: () => {
                // Re-enable toggle
                $toggle.prop('disabled', false);
            }
        });
    }

    // Phương thức để khởi tạo lại sau khi cập nhật DOM (nếu cần)
    reinitialize() {
        this.$toggles = this.$el.find('.toggle-checkbox');
        this.bindEvents();
    }
}

new ModPeriodManagement('.mod-period-management').init();