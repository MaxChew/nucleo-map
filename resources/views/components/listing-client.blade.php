@if ($href ?? false)
    <a 
        :href="$route('{{ app('domain') }}.clients.show', [{{ $objname ?? 'row.client' }}.code])" 
        class="flex flex-col text-xs hover:underline cursor-pointer" v-if="{{ $objname ?? 'row.client'}}">
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.client'}}.code" v-html="formatCode({{ $objname ?? 'row.client'}}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.client' }}.user.name" v-html="formatName({{ $objname ?? 'row.client' }}.user.name)">
        </span>
    </a>
@else
    <div class="flex flex-col" v-if="{{ $objname ?? 'row.client'}}" >
        <span class="font-semibold text-xs truncate" :title="{{ $objname ?? 'row.client' }}.code" v-html="formatCode({{ $objname ?? 'row.client' }}.code)">
        </span>
        <span class="text-gray-500 dark:text-slate-400 truncate" :title="{{ $objname ?? 'row.client' }}.user.name" v-html="formatName({{ $objname ?? 'row.client' }}.user.name)">
        </span>
    </div>
@endif