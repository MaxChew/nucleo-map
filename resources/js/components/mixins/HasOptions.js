export default {
    props: {
        options: {
            type: [Array, Object],
            required: true
        },
        valueKey: {
            type: String,
            default: 'value',
        },
        labelKey: {
            type: String,
            default: 'label',
        },
        disabledKey: {
            type: String,
            default: 'disabled',
        }
    },
    computed: {
        normalizedOptions() {
            return _(this.options).thru((options) => {
                return Array.isArray(options) ?
                    options :
                    _.map(options, (label, value) => ({ value: value, label: label }))
            }).map(option => {
                if (typeof option === 'object') {
                    const values = _.values(option)
                    return {
                        value: option[this.valueKey] || values[0],
                        label: option[this.labelKey] || values[1] || option[this.valueKey] || values[0],
                        disabled: option[this.disabledKey],
                        data: option
                    }
                } else {
                    return { value: option, label: option }
                }
            }).value()
        },
    }
}