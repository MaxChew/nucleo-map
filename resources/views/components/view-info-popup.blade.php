<modal :size="'xl'" :show="viewInfoModal" :title="'Status Information'" @close="viewInfoModal = false">
    @if(isset($view_info))
        <div class="p-4 bg-bg rounded-lg shadow-md">
            @foreach ($view_info as $status => $info)
                <div class="border border-gray-300 rounded-lg p-4 mb-4">
                <h2 class="text-{{ App\Enums\Status::CLASSNAME[strtoupper($status )] ?? 'gray-400' }}">
                    {{ $info['label'] }}
                </h2>

                <p class="mt-2 leading-relaxed">
                    <span class="font-semibold text-gray-400">-</span> {{ $info['meaning'] }}
                </p>
            </div>
        @endforeach
    </div>
    @endif
</modal>
