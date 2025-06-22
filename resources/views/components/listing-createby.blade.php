<div class="flex flex-col">
    <span v-text="row.created_at" aria-label="Created Time" data-balloon-pos="up-right"></span>
    <span v-text="row.created_by.name" v-if="row.created_by"></span>
</div>