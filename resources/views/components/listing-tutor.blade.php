@if ($href ?? false)
    <a 
        :href="$route('{{ app('domain') }}.tutors.show', [{{ $objname ?? 'row.tutor' }}.code])" 
        class="flex flex-col text-xs hover:underline cursor-pointer" v-if="{{ $objname ?? 'row.tutor' }}">
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.tutor'}}.code" v-html="formatCode({{ $objname ?? 'row.tutor'}}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.tutor'}}.user.name" v-html="formatName({{ $objname ?? 'row.tutor'}}.user.name)">
        </span>
    </a>
@else
    <div class="flex flex-col" v-if="{{ $objname ?? 'row.tutor' }}">
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.tutor'}}.code" v-html="formatCode({{ $objname ?? 'row.tutor'}}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.tutor'}}.user.name" v-html="formatName({{ $objname ?? 'row.tutor'}}.user.name)">
        </span>
    </div>
@endif