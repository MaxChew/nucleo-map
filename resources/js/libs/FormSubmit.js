
const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content

const hiddenInput = (name, value) => {
    const input = document.createElement('input')
    input.setAttribute('type', 'hidden')
    input.setAttribute('name', name)
    input.setAttribute('value', value)
    return input
}

export default class FormSubmit {

    submit(method, url, data = {}, target) {
        method = method.toUpperCase()
        const form = document.createElement('form'),
            spoofMethod = method == 'GET' || method == 'POST' ? method : 'POST',
            appendInput = (data, name) => {
                if (data instanceof Array) {
                    data.forEach((value, index) => appendInput(value, `${name}[${index}]`))
                } else if (data instanceof Object) {
                    _.forIn(data, (value, key) => appendInput(value, `${name}[${key}]`))
                } else {
                    form.appendChild(hiddenInput(String(name), String(data)))
                }
            }

        form.setAttribute('method', spoofMethod)
        form.setAttribute('action', url)
        if (target) form.setAttribute('target', target)

        if (method !== spoofMethod) form.appendChild(hiddenInput('_method', method))
        if (method !== 'GET' && url.match(window.location.host) !== null) form.appendChild(hiddenInput('_token', csrfToken))

        _.forIn(data, appendInput)

        document.body.appendChild(form)
        form.submit()
    }

    get(url, params, target) {
        return this.submit('get', url, params, target)
    }

    post(url, data, target) {
        return this.submit('post', url, data, target)
    }

    patch(url, data, target) {
        return this.submit('patch', url, data, target)
    }

    put(url, data, target) {
        return this.submit('put', url, data, target)
    }

    delete(url, data, target) {
        return this.submit('delete', url, data, target)
    }

}
