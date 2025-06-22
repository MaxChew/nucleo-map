<div class="flex items-center gap-2" :aria-label="row.user?.email || row.email || ''" data-balloon-pos="up-right">
    <i class="fad fa-envelope"></i>
    <span v-text="(row.user?.email || row.email || '').length > 10 ? (row.user?.email || row.email || '').substring(0, 10) + '...' : (row.user?.email || row.email || '')"></span>
</div>