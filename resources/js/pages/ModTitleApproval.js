export default class ModTitleApproval { 
  constructor(el) {
    this.$el = $(el);
    this.$yearSelect = this.$el.find('#year');
    this.$form = this.$el.find('#yearFilterForm');
    this.$approvalForm = this.$el.find('#approvalForm');
    
    // Thêm các elements cho chức năng popup feedback
    this.$editButtons = this.$el.find('.edit-btn');
    this.$popup = this.$el.find('.js-popup');
    this.$closeButtons = this.$el.find('.close-popup');
    this.$feedbackForm = this.$el.find('.edit-feedback-form');
    this.$feedbackInput = this.$el.find('#feedback-nomination');
    this.$feedbackPopup = this.$el.find('.feedback-popup');
    
    // Lưu trữ các nhận xét tạm thời
    this.tempFeedbacks = {};
    this.currentNominationId = null;
  }
  
  init() {
    this.bindEvents();
    this.initCKEditor();
  }
  
  bindEvents() {
    if (this.$yearSelect.length) {
      this.$yearSelect.on('change', () => this.filterByYear());
    }
    
    // Thêm xử lý xác nhận trước khi submit form phê duyệt
    if (this.$approvalForm.length) {
      this.$approvalForm.on('submit', (e) => this.handleApprovalSubmit(e));
    }
    
    // Xử lý nút edit feedback
    this.$editButtons.on('click', (e) => this.showFeedbackPopup(e));
    
    // Xử lý đóng popup khi nhấn nút đóng
    this.$closeButtons.on('click', (e) => {
      e.preventDefault();
      this.hidePopup();
    });

    // Kiểm tra nếu click vào overlay (không phải vào popup content)
    this.$popup.on('click', (e) => {
      if (!$(e.target).closest('.feedback-popup').length) {
        e.preventDefault();
        this.hidePopup();
      }
    });
    
    // Xử lý submit form feedback
    this.$feedbackForm.on('submit', (e) => this.handleFeedbackSubmit(e));
  }
  
  initCKEditor() {
    // Kiểm tra xem có textarea feedback-nomination không
    const $textarea = this.$feedbackInput;
    
    if ($textarea.length > 0 && typeof CKEDITOR !== 'undefined') {
      // Quan trọng: Kiểm tra instance đã tồn tại chưa để tránh lỗi "already attached"
      if (!CKEDITOR.instances['feedback-nomination']) {
        CKEDITOR.replace('feedback-nomination', {
          height: 200,
          toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'tools', items: ['Maximize'] }
          ]
        });
      }
    }
  }
  
  filterByYear() {
    if (this.$form.length) {
      this.$form.submit();
    }
  }
  
  handleApprovalSubmit(e) {
    if (!confirm('Bạn có chắc chắn muốn phê duyệt danh hiệu cho tất cả nhân viên?')) {
      e.preventDefault();
      return;
    }
    
    // Thêm tất cả các feedback tạm thời vào form
    for (const nominationId in this.tempFeedbacks) {
      // Tạo input hidden cho mỗi feedback
      const hiddenInput = $('<input>')
        .attr('type', 'hidden')
        .attr('name', `nominations_feedback[${nominationId}]`)
        .val(this.tempFeedbacks[nominationId]);
      
      this.$approvalForm.append(hiddenInput);
    }
  }
  
  showFeedbackPopup(e) {
    const button = $(e.currentTarget);
    const id = button.data('id');
    const feedback = button.attr('data-feedback') || '';
    
    // Lưu id hiện tại để sử dụng trong handleFeedbackSubmit
    this.currentNominationId = id;
    
    // Hiển thị popup trước
    this.$popup.removeClass('hidden');
    
    // Hiển thị nội dung feedback hiện tại hoặc từ bộ nhớ tạm
    const currentFeedback = this.tempFeedbacks[id] || feedback || '';
    
    // Đặt giá trị feedback vào editor - tương tự như ModAllTitleNominations
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['feedback-nomination']) {
      CKEDITOR.instances['feedback-nomination'].setData(currentFeedback);
    } else {
      // Đây là fallback nếu CKEditor chưa load xong
      this.$feedbackInput.val(currentFeedback);
      // Thử khởi tạo CKEditor một lần nữa
      this.initCKEditor();
    }
  }
  
  hidePopup() {
    // KHÔNG hủy instance CKEditor khi đóng popup - đây là điểm khác biệt quan trọng
    // Chỉ ẩn popup đi
    this.$popup.addClass('hidden');
  }
  
  handleFeedbackSubmit(e) {
    e.preventDefault();
    
    // Lấy giá trị từ CKEditor (nếu có)
    let feedbackValue = '';
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['feedback-nomination']) {
      feedbackValue = CKEDITOR.instances['feedback-nomination'].getData();
    } else {
      feedbackValue = this.$feedbackInput.val();
    }
    
    // Lưu feedback vào bộ nhớ tạm
    const nominationId = this.currentNominationId;
    if (nominationId) {
      this.tempFeedbacks[nominationId] = feedbackValue;
      
      // Cập nhật data-feedback cho button tương ứng
      // Sử dụng attr thay vì data để đảm bảo cập nhật đồng thời cả attribute HTML
      $(`.edit-btn[data-id="${nominationId}"]`).attr('data-feedback', feedbackValue);
      
    }
    
    // Đóng popup (không hủy CKEditor)
    this.hidePopup();
  }
}

// Đảm bảo DOM đã sẵn sàng trước khi khởi tạo
$(document).ready(function() {
  const app = new ModTitleApproval('.mod-title-approval');
  app.init();
});