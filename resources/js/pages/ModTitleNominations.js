export default class ModTitleNominations {
    constructor(el) {
        this.$el = $(el);
        this.$tableBody = this.$el.find('#nominations-table tbody');
        this.$addRowButton = this.$el.find('#add-row');
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.$addRowButton.on('click', () => this.addRow());
        this.$el.on('click', '.remove-row', (e) => this.removeRow(e));
    }

    addRow() {
        const rowCount = this.$tableBody.find('tr').length + 1;
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>
                    <select name="titles[]" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                        ${this.getTitleOptions()}
                    </select>
                </td>
                <td>
                    <select name="rewards[]" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                        ${this.getRewardOptions()}
                    </select>
                </td>
                <td>
                    <textarea name="achievements[]" class="border-primary-500 border-1 p-2 w-full rounded-lg" required></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-tertiary remove-row">Xóa</button>
                </td>
            </tr>
        `;
        this.$tableBody.append(newRow);
    }

    removeRow(e) {
        $(e.target).closest('tr').remove();
        this.updateRowNumbers();
    }

    updateRowNumbers() {
        this.$tableBody.find('tr').each((index, row) => {
            $(row).find('td:first-child').text(index + 1);
        });
    }

    getTitleOptions() {
        // Lấy danh sách tùy chọn danh hiệu từ hàng đầu tiên
        return $('#nominations-table select[name="titles[]"]:first').html();
    }

    getRewardOptions() {
        // Lấy danh sách tùy chọn hình thức khen thưởng từ hàng đầu tiên
        return $('#nominations-table select[name="rewards[]"]:first').html();
    }
}

new ModTitleNominations('.mod-title-nomination').init();