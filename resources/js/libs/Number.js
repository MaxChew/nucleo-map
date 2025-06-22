import NumAbbr from 'number-abbreviate'
const accounting = require('accounting')

export default class Number {

    constructor() {
        this.numAbbr = new NumAbbr(['K', 'M', 'B', 'T'])
    }

    abbreviate(number, rounding) {
        return this.numAbbr.abbreviate(number, rounding)
    }

    humanSize(size) {
        let i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
        return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    }

    format(number, precision) {
        if (! number && number !== 0) return number

        number = parseFloat(number)
        if (precision === undefined) {
            let floating = (number+'').split('.')[1] || ''
            precision = floating.length
            if (precision % 2 === 1) {
                ++precision
            }
        }

        return accounting.formatNumber(number, precision)
    }

}
