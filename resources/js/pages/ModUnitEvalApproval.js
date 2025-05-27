export default class UnitEvaluationApproval {
  constructor() {
    this.$modal = $('#approvalModal');
    this.$modalContent = $('#modalContent');
    this.$openModalBtn = $('#openApprovalFormBtn');
    this.$closeModal = $('#closeModal');
    this.$cancelBtn = $('#cancelApproval');
    this.$body = $('body');
  }

  init() {
    this.initEventListeners();
    return this;
  }
  
  initEventListeners() {
    // Modal open button
    this.$openModalBtn.on('click', () => this.showModal());
    
    // Modal close button
    this.$closeModal.on('click', () => this.hideModal());
    
    // Cancel button
    this.$cancelBtn.on('click', () => this.hideModal());
    
    // Click outside modal to close
    this.$modal.on('click', (e) => this.handleOutsideClick(e));
    
    return this;
  }
  
  showModal() {
    this.$modal.removeClass('hidden');
    this.$body.addClass('overflow-hidden'); // Prevent scrolling while modal is open
    
    // Trigger animation after modal is visible
    setTimeout(() => {
      this.$modalContent.removeClass('scale-95 opacity-0');
      this.$modalContent.addClass('scale-100 opacity-100');
    }, 10);
  }
  
  hideModal() {
    // First animate the modal content
    this.$modalContent.removeClass('scale-100 opacity-100');
    this.$modalContent.addClass('scale-95 opacity-0');
    
    // Then hide the modal after animation completes
    setTimeout(() => {
      this.$modal.addClass('hidden');
      this.$body.removeClass('overflow-hidden'); // Re-enable scrolling
    }, 300);
  }
  
  handleOutsideClick(event) {
    // Check if the click is directly on the modal background (not on modal content)
    if (event.target === this.$modal[0]) {
      this.hideModal();
    }
  }
}

// Initialize when document is ready
  new UnitEvaluationApproval().init();