<template>
    <div class="form-control flex items-center relative flex-wrap p-0.5 !rounded-xl" @click="focusNewValue">
        <div class="hidden">
            <input v-if="! nullifyValues" :name="name" value="">
            <input v-else v-for="val in nullifyValues" :key="val.value" :value="val.value" :name="arrayableName">
        </div>
        <div class="bg-primary rounded-lg py-1 px-4 m-1 text-white flex justify-between items-center" v-for="(val, index) in values" :key="val.value">
            <span v-text="val.label"></span>
            <button type="button" class="ml-2 hover:text-primary" @click.stop="remove(index)">
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.21 16.21"><title>Icons - Close</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><polygon points="16.21 0.71 15.5 0 8.1 7.4 0.71 0 0 0.71 7.4 8.1 0 15.5 0.71 16.21 8.1 8.81 15.5 16.21 16.21 15.5 8.81 8.1 16.21 0.71" ></polygon></g></g></svg>
            </button>
        </div>
        <div class="flex-grow min-w-1/4">
            <combo-box :placeholder="placeholder"
                :url="url"
                :options="localOptions || []"
                :value-key="valueKey"
                :label-key="labelKey"
                v-model="newValue"
                @input="appendNewValue"
                @keydown.delete="removeLastValue"
                :disable-add="disableAdd"
                ref="comboBox"
            >
                <template #default="{ option, input, highlight }">
                    <slot :option="option" :input="input">
                        <div v-html="highlight(option.label)"></div>
                    </slot>
                </template>
            </combo-box>
        </div>
    </div>
</template>

<script>
  // Only use Vue 3 Composition API, remove Vue 2 Options API
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import _ from 'lodash';

export default {
    name: 'ComboBoxMultiple',
    props: {
        name: String,
            modelValue: [Array, Object], // Vue 3 uses modelValue
    value: [Array, Object], // Maintain backward compatibility
        placeholder: String,
        options: {
            type: [Array, Object],
            default: () => []
        },
        valueKey: String,
        labelKey: String,
        disableAdd: Boolean,
        url: String,
    },
    setup(props, { emit }) {
        const comboBox = ref(null);
        const values = ref([]);
        const newValue = ref(null);
        const focusing = ref(false);
        const localOptions = ref(props.options || []);

        // Watch for changes in options property
        watch(() => props.options, (newOptions) => {
            localOptions.value = newOptions || [];
        }, { immediate: true });

        // Compatible with Vue 2's value and Vue 3's modelValue
        const actualValue = computed(() => props.modelValue || props.value);

        // Watch for value changes
        watch(() => actualValue.value, (newVal) => {
            if (!_.isEqual(newVal, values.value)) {
                // Create new array and assign to values.value, instead of modifying original array
                values.value = structureValues(newVal);
            }
        }, { immediate: true, deep: true });

        const nullifyValues = computed(() => {
            return (values.value && values.value.length > 0) ? values.value : null;
        });

        const arrayableName = computed(() => {
            if (props.name && !props.name.endsWith('[]')) {
                return props.name + '[]';
            }
            return props.name;
        });

        function structureValues(vals) {
            vals = vals || [];
            
            const result = [];
            
            if (!(vals instanceof Array)) {
                if (vals instanceof Object) {
                    for (const [value, label] of Object.entries(vals)) {
                        result.push({ value, label });
                    }
                } else {
                    result.push(vals);
                }
            } else {
                vals.forEach(value => {
                    if (typeof value !== 'object') {
                        result.push({
                            value: value,
                            label: value
                        });
                    } else {
                        result.push(value);
                    }
                });
            }
            
            return result;
        }

        function focusNewValue() {
            if (comboBox.value && comboBox.value.$refs.inputBox) {
                comboBox.value.$refs.inputBox.focus();
            }
        }

        const appendNewValue = (newVal, newLabel) => {
            
                          // Check if it's an event object, if so, don't process
            if (newVal instanceof Event || newVal === '[object InputEvent]') {
                return;
            }
            
                          // Ensure value is not empty
            newVal = newVal || newLabel;
            if (!newVal) {
                return;
            }

            
                          // Check if the same value already exists
            if (!values.value.find(v => v.value == newVal)) {
                const newValues = [...values.value, { value: newVal, label: newLabel }];
                values.value = newValues;
                valuesChanged();
            } else {
            }
            
            nextTick(() => {
                newValue.value = null;
                if (comboBox.value) {
                    comboBox.value.inputLabel = null;
                    comboBox.value.lastKeyword = '';
                    if (comboBox.value.$refs.inputBox) {
                        comboBox.value.$refs.inputBox.value = '';
                    }
                }
                focusNewValue();
            });
        };

        function remove(index) {
            const newValues = [...values.value];
            newValues.splice(index, 1);
            values.value = newValues;
            valuesChanged();
        }

        function removeLastValue(e) {
            if (e.target.value) return;
            if (values.value.length > 0) {
                const newValues = [...values.value];
                newValues.pop();
                values.value = newValues;
                valuesChanged();
            }
        }

        function valuesChanged() {
                          emit('input', values.value); // Backward compatibility
              emit('update:modelValue', values.value); // Vue 3 standard way
        }

        onMounted(() => {
            if (comboBox.value && comboBox.value.$refs.inputBox) {
                comboBox.value.$refs.inputBox.addEventListener('focus', () => focusing.value = true);
                comboBox.value.$refs.inputBox.addEventListener('blur', () => focusing.value = false);
            }
        });

        return {
            comboBox,
            values,
            newValue,
            focusing,
            nullifyValues,
            arrayableName,
            localOptions,
            focusNewValue,
            appendNewValue,
            remove,
            removeLastValue,
            valuesChanged
        };
    }
}
</script>

<style>
.combo-multiple .form-control {
    padding: 0;
    border: 0;
}
.combo-multiple .relative {
    position:inherit;
}
.combo-multiple .relative ul {
    left: 0;
    margin-top: theme('margin.3');
}
</style> 