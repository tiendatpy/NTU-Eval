export default class Toast {
  constructor() {
    this.$toast = $('.toast-container');
    // this.$closeButton = this.$toast.find('.close-toast');
  }
  init(){
    // this.bindEvents();
  }

  bindEvents() {
    this.$toast.forEach((toast) => {
      setTimeout(() => {
          toast.remove();
      }, 3000); // 4 giây
  });
  }
}