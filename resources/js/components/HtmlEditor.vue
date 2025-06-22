<template>
    <div class="relative" :class="{'quill-focusing':focusing}">
        <div class="hidden">
            <textarea :name="name" :value="htmlValue"></textarea>
            <input type="file" multiple accept=".png, .jpg, .jpeg, .gif" ref="imageUploader" @change="imageHandler"/>
        </div>
        <div ref="container" :style="{ height }" v-bind="$attrs"></div>
        <transition name="fade">
            <div v-if="uploadingImage" class="absolute pin flex justify-center items-center bg-white-70 z-30 m-px">
                <div class="text-center">
                    <span class="loader w-8 h-8"></span>
                    <p class="mt-2">Uploading...</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import Quill from "./mixins/Quill";
const Delta = Quill.import('delta');

export default {
    inheritAttrs: false,
    props: {
        value: String,
        name: String,
        placeholder: String,
        configs: Object, 
        listOnly: Boolean,
        height: String,
    },
    watch: {
        value(newValue) {
            if (newValue != this.htmlValue) {
                this.htmlValue = newValue
                this.setContent(this.htmlValue)
            }
        }
    },
    data() {
        return {
            uploadState: null,
            initialize: false,
            htmlValue: this.value,
            focusing: false,
            quill: null,
            uploadingImage: false,
            imageIndex: null,
            settingContent: false,
            clickedToolbarButton: false,
        }
    },
    computed: {
        quillConfigs() {
            return _.assign({
                theme: 'snow',
                placeholder: this.placeholder,
            }, this.listOnly ? {
                formats: [
                    'list',
                    'indent'
                ],
                modules: {
                    toolbar: [
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean']
                    ]
                },
            } : {
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, 4, 5, 6, false] }, 'bold', 'italic', 'underline', 'divider'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['image'],
                    ],
                },
            }, this.configs || {})
        }
    },
    methods: {
        setContent(html) {
            this.settingContent = true
            this.quill.setContents(this.quill.clipboard.convert(html))
            if (this.initialize) {
                let index = this.quill.getLength()
                if (index > 1) this.quill.insertText(index, '\n')
            }
            this.settingContent = false
        },
        buildQuill() {
            this.quill = new Quill(this.$refs.container, this.quillConfigs)
            this.quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
                if (! this.settingContent) {
                    let ops = []
                    delta.ops.forEach(op => {
                        if (op.insert && typeof op.insert === 'string') {
                            ops.push({ insert: op.insert })
                        }
                    })
                    delta.ops = ops
                }
                return delta
            })
            this.quill.clipboard.addMatcher(Node.TEXT_NODE, function(node, delta) {
                return new Delta().insert(node.data);
            });
            this.quill.scrollingContainer.addEventListener('focus', () => {
                this.focusing = true
                this.$emit('focus', this)
            })
            Array.apply(null, this.$el.querySelectorAll('.ql-toolbar button')).forEach(
                btn => btn.addEventListener('mousedown', () => {
                    if (this.focusing) {
                        this.clickedToolbarButton = true
                    }
                })
            )
            this.quill.scrollingContainer.addEventListener('blur', (e) => {
                if (this.clickedToolbarButton == false) {
                    this.focusing = false
                    this.$emit('blur', this)
                }
                this.clickedToolbarButton = false
            })
            this.quill.root.addEventListener('drop', this.imageDropHandler, false);
            this.quill.on('text-change', () => {
                this.htmlValue = this.quill.scrollingContainer.innerHTML
                if (! this.initialize) this.$emit('input', this.htmlValue)
            })
            this.quill.getModule('toolbar').addHandler('image', () => this.$refs.imageUploader.click())
            this.quill.getModule('toolbar').addHandler('divider', () => {
                let range = this.quill.getSelection(true);
                this.quill.insertText(range.index, '\n', Quill.sources.USER);
                this.quill.insertEmbed(range.index + 1, 'divider', true, Quill.sources.USER);
                this.quill.setSelection(range.index + 2, Quill.sources.SILENT);
            })
        },
        imageHandler(e) {
            let range = this.quill.getSelection(true);
            this.quill.deleteText(range.index, range.length)
            this.imageIndex = _.get(this.quill.getSelection(), 'index', this.quill.getLength())
            this.uploadImage(e.target.files)
        },
        imageDropHandler(e) {
            e.preventDefault()
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                if (document.caretRangeFromPoint) {
                    let selection = document.getSelection(),
                        range = document.caretRangeFromPoint(e.clientX, e.clientY)
                    if (selection && range) {
                        selection.setBaseAndExtent(range.startContainer, range.startOffset, range.startContainer, range.startOffset);
                    }
                }
                this.imageIndex = _.get(this.quill.getSelection(), 'index', this.quill.getLength())
                this.uploadImage(e.dataTransfer.files)
            }
        },
        uploadImage(files) {
            this.uploadingImage = true
            this.quill.root.setAttribute('contenteditable', 'false')
            let lastIndex = this.quill.getLength(),
                requests = Array.from(files)
                .filter(file => file.type.match(/^image\/(gif|jpe?g|a?png)/i))
                .map(file => {
                    let formData = new FormData()
                    formData.append('image', file);
                    return this.$api.post(this.passport_url('shared/private/upload/image'), formData)
                })
            axios.all(requests).then(axios.spread((...results) => {
                results.forEach(({ data: payload }) => {
                    this.quill.insertEmbed(
                        this.imageIndex >= 0 ? this.imageIndex : this.quill.getLength(),
                        'image', payload.data.url, 'user'
                    );
                    ++this.imageIndex
                })
                this.uploadingImage = false
                this.quill.root.setAttribute('contenteditable', 'true')
                if (this.imageIndex >= lastIndex) {
                    this.quill.insertText(this.imageIndex, '\n')
                    this.quill.setSelection(this.imageIndex + 1)
                } else {
                    this.quill.setSelection(this.imageIndex)
                }
            })).catch(axios.spread((...errors) => {
                this.uploadingImage = false
                this.quill.root.setAttribute('contenteditable', 'true')
                this.quill.setSelection(this.imageIndex)
                this.$refs.imageUploader.value = ''
                if(!/safari/i.test(navigator.userAgent)){
                    this.$refs.imageUploader.type = ''
                    this.$refs.imageUploader.type = 'file'
                }
            }))
        }
    },
    mounted() {
        this.initialize = true
        this.buildQuill()
        this.setContent(this.htmlValue)
        this.initialize = false
    }
}
</script>
