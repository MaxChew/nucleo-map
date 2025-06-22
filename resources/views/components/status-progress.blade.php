<div
    class="hidden sm:flex w-full mx-auto rounded-full border-2 border-gray-200 dark:border-gray-600 text-xs bg-gray-100 dark:bg-gray-300 mb-2">
    @foreach ($steps as $index => $step)
        <div @class(['relative flex-1 flex '])>
            <?php
            $extraclass = '';
            
            if ($index == 1) {
                $extraclass .= 'rounded-l-full';
            } elseif ($index == count($steps)) {
                $extraclass .= 'rounded-r-full';
            }
            ?>
            <div @class([
                'flex-1 h-10 flex items-center justify-center',
                'bg-teal-500 dark:bg-slate-800 text-white' => $index <= $currentStep,
                'bg-gray-100 dark:bg-gray-300 text-gray-700' => $index > $currentStep,
                $extraclass,
            ])>
                <span class="relative flex gap-1 items-center z-10 text-sm font-medium px-4">
                    @if ($index <= $currentStep)
                        <i class="fad fa-check-circle"></i>
                    @else
                        <i class="fad fa-times-circle"></i>
                    @endif
                    <span class="block lg:hidden">{{ ucfirst(trim($step['short_label'] ?? '')) }}</span>
                    <span class="hidden lg:block">{{ ucfirst(trim($step['label'] ?? '')) }}</span>
                </span>
            </div>

            {{-- Arrow shape - add to all steps except the last one --}}
            <?php
            $extraclass = '';
            if ($index == $currentStep) {
                $extraclass .= ' bg-gray-100 dark:bg-gray-300';
            } elseif ($index <= $currentStep) {
                $extraclass .= ' bg-teal-500 dark:bg-slate-800';
            }
            ?>
            @if ($index < count($steps))
                <div class="w-10 h-10 overflow-hidden relative flex-shrink-0 {{ $extraclass }}">
                    <div
                        class="absolute inset-0 w-10 h-10 transform rotate-45 origin-center translate-x-[-40%] {{ $index <= $currentStep ? 'bg-teal-500 dark:bg-slate-800' : 'bg-gray-100 dark:bg-gray-300' }}">
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
