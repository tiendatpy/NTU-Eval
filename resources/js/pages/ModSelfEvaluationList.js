export default class ModAllQualityRating {
    constructor(el) {
        this.$el = $(el);
        this.$yearSelect = this.$el.find('#year');
        this.$form = this.$el.find('#yearFilterForm');
    }

    init() {
        this.bindEvents();
        return this;
    }

    bindEvents() {
        if (this.$yearSelect.length) {
            this.$yearSelect.on('change', () => this.filterByYear());
        }
    }

    filterByYear() {
        if (this.$form.length) {
            this.$form.submit();
        }
    }
    
}

new ModAllQualityRating('.mod-self-eval-list').init();