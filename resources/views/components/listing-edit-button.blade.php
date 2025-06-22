@if(isset($permission)) 
    @can($permission)
        <a :href="$route('{{ app('domain') }}.{{ $name }}s.edit', [row.code])" class="hover:text-fifth" aria-label="Edit" data-balloon-pos="up-right" v-if="row.deleted_at == null" target="{{ ($onTarget ?? false) ? '_blank' : '_self' }}">
            <i class="fad fa-pen text-base"></i>
        </a>
    @endcan
@else
    <a :href="$route('{{ app('domain') }}.{{ $name }}s.edit', [row.code])" class="hover:text-fifth" aria-label="Edit" data-balloon-pos="up-right" v-if="row.deleted_at == null" target="{{ ($onTarget ?? false) ? '_blank' : '_self' }}">
        <i class="fad fa-pen text-base"></i>
    </a>
@endif