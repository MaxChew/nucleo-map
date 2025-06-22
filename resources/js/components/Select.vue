<template>
    <div class="w-full mt-1" :class="cssclass">
        <select class="form-select w-full" :name="name" 
            v-model="currentValue" :multiple="multiple"
            @change="updateValue($event.target.value)"
            v-bind="attrs"
            v-on="attrs"
        >
            <option v-if="!multiple && !noempty" value="" v-text="placeholder"></option>
            <template v-if="groupBy" v-for="(options, groupLabel) in normalizedGroups">
                <optgroup v-if="groupLabel" :label="groupLabel" :key="groupLabel">
                    <option v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                        :disabled="option.disabled"
                        v-text="option.label"
                    ></option>
                </optgroup>
                <option v-else
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                    :disabled="option.disabled"
                    v-text="option.label"
                ></option>
            </template>
            <option v-else
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
                v-text="option.label"
            ></option>
        </select>
    </div>
</template>

<script>
import { ref, computed, watch, nextTick, toRefs } from 'vue';
import _ from 'lodash';
import HasOptions from "./mixins/HasOptions";

export default {
    inheritAttrs: false,
    mixins: [HasOptions],
    props: {
        noempty: Boolean,
        modelValue: [String, Number],
        placeholder: String,
        groupBy: String,
        multiple: Boolean,
        cssclass: String,
        name: String,
        options: {
            type: [Array, Object],
            default: () => []
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
    setup(props, { emit, attrs }) {
        const { modelValue, options, groupBy } = toRefs(props);
        const currentValue = ref(modelValue.value); // 使用 modelValue 初始化 currentValue

        watch(modelValue, (newValue) => {
            currentValue.value = newValue;
        });

        watch(options, () => {
            const currentValueCopy = currentValue.value;
            currentValue.value = null;
            nextTick(() => currentValue.value = currentValueCopy);
        });

        const normalizedOptions = computed(() => {
            return _(options.value).thru((opts) => {
                return Array.isArray(opts) ?
                    opts :
                    _.map(opts, (label, value) => ({ value: value, label: label }));
            }).map(option => {
                if (typeof option === 'object') {
                    const values = _.values(option);
                    return {
                        value: option[props.valueKey] || values[0],
                        label: option[props.labelKey] || values[1] || option[props.valueKey] || values[0],
                        disabled: option[props.disabledKey],
                        data: option
                    };
                } else {
                    return { value: option, label: option };
                }
            }).value();
        });

        const normalizedGroups = computed(() => {
            return _(normalizedOptions.value)
                .groupBy(option => _.get(option.data, groupBy.value, '') || '')
                .value();
        });

        const updateValue = (value) => {
            currentValue.value = value;
            emit('update:modelValue', value);
        };

        return {
            currentValue,
            normalizedGroups,
            updateValue,
            attrs
        };
    }
}
</script>