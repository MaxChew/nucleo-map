@if(isset($permission)) 
    @can($permission)
        <a :href="$ziggyRoute('{{ $route }}', [row.id])" class="{{ $class ?? 'text-secondary-500 hover:text-secondary-600 transition-colors' }}" aria-label="{{ $ariaLabel ?? 'View' }}" data-balloon-pos="up-right" target="_blank" v-if="row.deleted_at == null">
            <i class="fad fa-eye text-base"></i>
        </a>
    @endcan
@else
    <a :href="$ziggyRoute('{{ $route }}', [row.id])" class="{{ $class ?? 'text-secondary-500 hover:text-secondary-600 transition-colors' }}" aria-label="{{ $ariaLabel ?? 'View' }}" data-balloon-pos="up-right" target="_self" v-if="row.deleted_at == null">
        <i class="fad fa-eye text-base"></i>
    </a>
@endif