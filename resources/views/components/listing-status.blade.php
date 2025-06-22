@vuedata([
    'statusColors' => \App\Enums\Status::CLASSNAME,
])

<div class="flex justify-center w-full">
    <div class="text-xs text-center rounded-lg w-4/5 p-2" :class="[
        'bg-' + (statusColors[row.status] || 'gray-500')
    ]">
        <span class="!text-white" v-if="row.status_label"> @{{ row.status_label }}</span>
        <span class="!text-white" v-else> @{{ row.status }}</span>
    </div>
</div>