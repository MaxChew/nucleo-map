<script>
    window.app = <?php echo json_encode(
        array_merge(
            [
                'now' => Carbon\Carbon::now()->toIso8601String(),
                'errors' => (object) (isset($errors) ? $errors->getBags() : []),
                'vuedata' => (object) (app('vuedata') ?? []),
                'mixins' => [],
                'accessToken' => $accessToken ?? null,
                'user' => Auth::check() ? Auth::user() : null,
                'locale' => App::getLocale(),
                'passport_base_url' => passport_url('/'),
                'currency' => 'RM',
                'baseCurrency' => 'MYR',
                'key' => config('pusher.key'),
                'cluster' => config('pusher.cluster'),
                'authEndpoint' => config('pusher.authEndpoint'),
                'app_name' => config('defaults.app_name'),
                'url' => config('app.url'),
                'domain' => app('domain') ?? 'user',
            ],
            isset($exception)
                ? [
                    'exception' => get_class($exception),
                ]
                : [],
        ),
    ); ?>
</script> 