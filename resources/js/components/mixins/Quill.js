import Quill from 'quill';
let Delta = Quill.import('delta')
let BaseImageFormat = Quill.import('formats/image')
let BaseVideoFormat = Quill.import('formats/video')
let Clipboard = Quill.import('modules/clipboard')
let BlockEmbed = Quill.import('blots/block/embed')

class ImageFormat extends BaseImageFormat {
    static create(value) {
        let node = super.create(value);
        node.setAttribute('class', 'ql-image-block')
        return node;
    }
}

class VideoFormat extends BaseVideoFormat {
    static create(value) {
        let iframe = super.create(value);
        let container = document.createElement('div')
        container.classList.add('ql-video-container')
        container.setAttribute('contenteditable', 'false')
        container.appendChild(iframe)
        return container;
    }
}

class PlainTextClipboard extends Clipboard {
    onPaste(e) {
        if (e.defaultPrevented || !this.quill.isEnabled()) return
        let range = this.quill.getSelection()
        let delta = new Delta().retain(range.index)
        if (e && e.clipboardData && e.clipboardData.types && e.clipboardData.getData) {
            let text = (e.originalEvent || e).clipboardData.getData('text/plain')
            let cleanedText = this.convert(text)
            // Stop the data from actually being pasted
            e.stopPropagation()
            e.preventDefault()
            // Process cleaned text
            delta = delta.concat(cleanedText).delete(range.length)
            this.quill.updateContents(delta, Quill.sources.USER)
            // range.length contributes to delta.length()
            this.quill.setSelection(delta.length() - range.length, Quill.sources.SILENT)
            return false
        }
    }
}

class DividerBlot extends BlockEmbed { }
DividerBlot.blotName = 'divider';
DividerBlot.tagName = 'hr';

let icons = Quill.import('ui/icons');
icons['divider'] = '<i class="fal fa-minus" aria-hidden="true"></i>';


Quill.register(ImageFormat, true)
Quill.register(VideoFormat, true)
Quill.register('modules/clipboard', PlainTextClipboard)

Quill.register({ 'formats/hr' : DividerBlot })
export default Quill