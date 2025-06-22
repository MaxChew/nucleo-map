const moment = require('moment-timezone')

export default function momentLocalize(...params) {
    const localized = moment.utc(...params).local()

    localized.formatDate = function (format = 'D MMM YYYY') {
        return this.format(format)
    }

    localized.formatDatetime = function (format = 'D MMM YYYY HH:mm') {
        return this.format(format)
    }

    return localized
}