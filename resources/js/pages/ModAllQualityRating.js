export default class ModAllQualityRating {
    constructor(el) {
        this.$container = $(el);
        if (this.$container.length === 0) return;
        
        this.$yearSelect = this.$container.find('#year');
        this.$form = this.$container.find('#yearFilterForm');
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

new ModAllQualityRating('.mod-quality-rating').init();