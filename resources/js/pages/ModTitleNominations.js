export default class ModTitleNomination {
    constructor(el) {
        this.$el = $(el);
        this.$periodDropdown = this.$el.find('.period-after-nomination');
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.$periodDropdown.on('change', () => this.loadNominationByYear());
    }

    loadNominationByYear() {
        const selectedPeriod = this.$periodDropdown.val();

        $.ajax({
            url: `${window.location.origin}/title-nominations?year=${selectedPeriod}`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                if (response.html) {
                    $('#nomination-content').html(response.html);
                    this.reinitializeCKEditor();
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading nominations:', error);
            },
        });
    }

    reinitializeCKEditor() {
        if (typeof CKEDITOR !== 'undefined') {
            for (const instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].destroy(true);
            }
            this.$el.find('textarea.ckeditor').each((index, textarea) => {
                CKEDITOR.replace(textarea);
            });
        }
    }
}

new ModTitleNomination('.mod-title-nomination').init();