let max = 101
let maxHeight = {
  '0': '0',
  '0.5': '1px',
  '200': '400px',
  full: '100%',
  screen: '100vh',
  initial: 'initial',
  'none': 'none',
  '2k': '2000px'
}

for (let i = 0; i < max; i++) {
  maxHeight[i] = i * 2 + 'px'
}

module.exports = {
  maxHeight
}
