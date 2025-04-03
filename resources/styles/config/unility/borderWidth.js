let max = 11
let BorderWidth = {
  DEFAULT: '1px',
  0: '0px',
  1: '1px',
  2: '2px',
  4: '4px',
  8: '8px'
}

for (let i = 0; i < max; i++) {
  BorderWidth[i] = i + 'px'
}

module.exports = {
  BorderWidth
}

