@if ($href ?? false)
    <a 
        :href="$route('{{ app('domain') }}.students.show', [{{ $objname ?? 'row.student' }}.code])" 
        class="flex flex-col text-xs hover:underline cursor-pointer" v-if="{{ $objname ?? 'row.student' }}">
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.student'}}.code" v-html="formatCode({{ $objname ?? 'row.student'}}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.student'}}.user.name" v-html="formatName({{ $objname ?? 'row.student'}}.user.name)">
        </span>
    </a>
@else
    <div class="flex flex-col" v-if="{{ $objname ?? 'row.student' }}" >
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.student'}}.code" v-html="formatCode({{ $objname ?? 'row.student'}}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.student'}}.user.name" v-html="formatName({{ $objname ?? 'row.student'}}.user.name)">
        </span>
    </div>
@endif