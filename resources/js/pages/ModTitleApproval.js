export default class ModTitleApproval { 
  constructor(el) {
    this.$el = $(el);
    this.$yearSelect = this.$el.find('#year');
    this.$form = this.$el.find('#yearFilterForm');
    this.$approvalForm = this.$el.find('#approvalForm');
  }
  init() {
    this.bindEvents();
  }
  bindEvents() {
    if (this.$yearSelect.length) {
      this.$yearSelect.on('change', () => this.filterByYear());
    }
    
    // Thêm xử lý xác nhận trước khi submit form phê duyệt
    if (this.$approvalForm.length) {
      this.$approvalForm.on('submit', (e) => this.handleApprovalSubmit(e));
    }
  }
  filterByYear() {
    if (this.$form.length) {
      this.$form.submit();
    }
  }
  handleApprovalSubmit(e) {
    if (!confirm('Bạn có chắc chắn muốn phê duyệt xếp loại cho tất cả nhân viên?')) {
      e.preventDefault();
    }
  }
}
new ModTitleApproval('.mod-title-approval').init();