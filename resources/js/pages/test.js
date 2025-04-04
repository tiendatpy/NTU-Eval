export default class Test {
    constructor(el) {
        this.$el = el;
    }
    init() {
        console.log(123);
    }
};

new Test('mod-test').init()