export default class ModEvaluation {
    constructor(el) {
        this.$el = $(el);
        this.$scoreInputs = this.$el.find('.score-input'); 
        this.$averageScore = this.$el.find('.total-score');
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.$scoreInputs.on('input', () => this.calculateAverageScore());
    }

    calculateAverageScore() {
        let total = 0;
        let count = 0;

        this.$scoreInputs.each((index, input) => {
            const value = parseFloat($(input).val());
            if (!isNaN(value)) {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : 0; 
        this.$averageScore.text(average);
    }
}

new ModEvaluation('.mod-self-eval').init();