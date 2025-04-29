export default class ModTitleNominations {
    constructor(el) {
        this.$el = $(el);
        this.$tableBody = this.$el.find('#nominations-table tbody');
        this.$addRowButton = this.$el.find('#add-row');

        // Lưu trữ dữ liệu mẫu của các select
        this.titleOptions = $('#nominations-table select[name="titles[]"]:first').html();
        this.rewardOptions = $('#nominations-table select[name="rewards[]"]:first').html();
    }

    init() {
        this.bindEvents();
        this.initializeCKEditor();
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
                    <textarea name="achievements[${rowCount - 1}]" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" required></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-tertiary remove-row">Xóa</button>
                </td>
            </tr>
        `;
        this.$tableBody.append(newRow);

        // Khởi tạo CKEditor cho các textarea mới
        this.initializeCKEditor();
    }

    removeRow(e) {
        const $row = $(e.target).closest('tr');
        const $textarea = $row.find('textarea.ckeditor');

        if ($textarea.length && CKEDITOR.instances[$textarea.attr('name')]) {
            CKEDITOR.instances[$textarea.attr('name')].destroy();
        }

        $row.remove();
        this.updateRowNumbers();
    }

    updateRowNumbers() {
        this.$tableBody.find('tr').each((index, row) => {
            $(row).find('td:first-child').text(index + 1);
        });
    }

    getTitleOptions() {
        return this.titleOptions; // Trả về dữ liệu mẫu đã lưu
    }

    getRewardOptions() {
        return this.rewardOptions; // Trả về dữ liệu mẫu đã lưu
    }

    initializeCKEditor() {
        this.$tableBody.find('textarea.ckeditor').each(function() {
            if (!CKEDITOR.instances[this.name]) {
                CKEDITOR.replace(this);
            }
        });
    }
}

new ModTitleNominations('.mod-title-nomination').init();