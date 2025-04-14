export default class ModEvaluation {
    constructor(el) {
        this.$el = $(el);
        this.$scoreInputs = this.$el.find('.score-input'); // Lấy tất cả các input điểm
        this.$averageScore = this.$el.find('.total-score'); // Phần hiển thị điểm trung bình
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Lắng nghe sự kiện thay đổi trên các input điểm
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

        const average = count > 0 ? (total / count).toFixed(2) : 0; // Tính điểm trung bình
        this.$averageScore.text(average); // Hiển thị điểm trung bình
    }
}

new ModEvaluation('.mod-self-eval').init();