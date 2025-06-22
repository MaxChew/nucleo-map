<div class="flex flex-col text-xs">
    <span v-if="row.updated_by" v-text="row.updated_at" :aria-label="'Updated By ' + row.updated_by.name" data-balloon-pos="up-right"></span>
    <span v-else v-text="row.updated_at" aria-label="Last Updated Time" data-balloon-pos="up-right"></span>
</div>