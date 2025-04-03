let max = 101
let minWidth = {
  0: '0',
  full: '100%',
  min: 'min-content',
  max: 'max-content',
  200: '200px'
}

for (let i = 0; i < max; i++) {
  minWidth[i] = i * 2 + 'px'
}

module.exports = {
  minWidth
}
