export default class Toast {
  constructor() {
    this.$toast = $('.toast-container');
    this.$closeButton = this.$toast.find('.close-toast');
  }
  init(){
    this.bindEvents();
  }

  bindEvents() {
    this.$closeButton.on('click', (e) => this.hideToast(e));
  }
  hideToast(event) {
    event.preventDefault();
    this.$toast.addClass('hidden');
  }
}
new Toast().init();