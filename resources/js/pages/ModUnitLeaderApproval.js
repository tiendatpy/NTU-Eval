export default class ModUnitLeaderApproval {
    constructor(el) {
        this.$el = $(el);
        this.$buttons = this.$el.find(".confirm-title-nomination");
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        this.$buttons.on("click", (e) => {
            const $button = $(e.currentTarget);
            const nominationId = $button.data("id");

            if (!nominationId) return;

            this.confirmNomination(nominationId, $button);
        });
    }

    confirmNomination(nominationId, $button) {
        $.ajax({
            url: `${window.location.origin}/list-title-nominations?id=${nominationId}`,
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (data) => {
                if (data.success) {
                    $button
                        .prop("disabled", true)
                        .addClass("opacity-50 cursor-not-allowed");
                    alert(data.message);
                } else {
                    alert("Đã xảy ra lỗi. Vui lòng thử lại.");
                }
            },
            error: (xhr, status, error) => {
                console.error("Error confirming nomination:", error);
                alert("Đã xảy ra lỗi. Vui lòng thử lại.");
            },
        });
    }
}

new ModUnitLeaderApproval(".unit-leader-approvals").init();
