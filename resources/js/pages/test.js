export default class Test {
    constructor(el) {
        this.$el = $(el);
    }
    init() {
        console.log(this.$el);
    }
};

new Test('.mod-test').init()