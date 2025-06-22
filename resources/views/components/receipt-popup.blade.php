<modal :size="'2xl'" :show="popupVisible" :title="{{ $items[0]->student_name }} [{{ $items[0]->student_code }}, {{ $items[0]->class_name }}]" @close="popupVisible = false">
    <div class="bg-bg rounded-lg">
        <div class="p-4">
        @component('listing-div',
                [
                    'rowNumber' => true,
                    'columns' => Columns::load([
                        'is_active' => ['label' => 'Date/Time', 'sortable' => true,'align' => 'center'],
                        'code' => ['label' => 'Total Conducted Hours', 'sortable' => true],
                        'user.name' => ['label' => 'Tutor Rate(MYR/hour)', 'sortable' => false],
                        'user.email' => ['label' => 'Total Earning(MYR)', 'sortable' => false],
                        'user.mobile' => ['label' => 'Report Card Status', 'sortable' => false],
                    ]),
                    'url' => passport_url('admin/private/tutors'),
                    ':params' => '{ filters:filterParams, status:current_status }',
                    'initialSorts' => ['updated_at:desc'],
                    'ref' => 'listing',
                ])
                @slot('column:is_active')
                    <div class="">
                        <span>28-2-2024</span>
                    </div>
                @endslot
                @slot('column:code')
                    <div class="">
                        <span>1</span>
                    </div>
                @endslot
                @slot('column:user.name')
                    <div class="">
                        <span>30</span>
                    </div>
                @endslot
                @slot('column:user.email')
                    <div class="">
                        <span>30</span>
                    </div>
                @endslot
                @slot('column:user.mobile')
                    <div class="">
                        <span>Done By Tutor & Approved By Client</span>
                    </div>
                @endslot
                @slot('column:updated_at')
                    @include('components.listing-updateby')
                @endslot
                @slot('action')
                    <div class="flex items-center">
                        @include('components.listing-edit-button ', [
                            'name' => 'tutor'])
                        <template v-if="row.events_count == 0">
                            @include('admin.partials.listing-delete-button', [
                                'name' => 'tutor' ])
                        </template>
                    </div>
                @endslot
            @endcomponent
        </div>
    </div>
</modal>