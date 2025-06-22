<script>
window.Laravel = {
    csrfToken: '{{ csrf_token() }}',
    user: @json(auth()->user()),
    appName: '{{ config('app.name') }}',
    appUrl: '{{ config('app.url') }}',
    locale: '{{ app()->getLocale() }}'
};

// Global Vue configuration
if (typeof Vue !== 'undefined') {
    Vue.config.productionTip = false;
}

// Global axios configuration
if (typeof axios !== 'undefined') {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
}
</script> 