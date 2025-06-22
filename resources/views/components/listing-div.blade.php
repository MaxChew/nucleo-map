<vue-listing inline-template class="v-cloak--invisible"
    @isset($url)
    url="{{ $url }}"
    @endisset
    @isset($__data[':url'])
    :url="{{ $__data[':url'] }}"
    @endisset
    @isset($__data[':params'])
    :params="{{ $__data[':params'] }}"
    @endisset
    @empty($alwaysShowAll)
    :initial-per-page-options="[10, 15, 50, 100]"
    @else
    :initial-per-page="0"
    @endempty
    @isset($initialPayload) :initial-payload="{{ json_encode($initialPayload) }}" @endisset
    @isset($initialSorts) :initial-sorts="{{ json_encode($initialSorts) }}" @endisset
    @isset($params) :params="{{ json_encode($params) }}" @endisset
    @isset($ref) ref="{{ $ref }}" @endisset
    @isset($primaryKey) primary-key="{{ $primaryKey }}" @endif
    {!! htmlattributes($attributes ?? []) !!}
    @update:modelValue="selected = $event"
    @update:page="page = $event"
    @update:last-page="lastPage = $event"
    v-model:page="page"
    v-model:last-page="lastPage"
>
    <template #default="{ collection, sortStatus, isSelected, toggleSelection, from, primaryKey, loading, error, errorDetail, dismissError, perPage, perPageOptionsMapped, fetchData, page, lastPage, setPage, toggleListSelection, listSelected }">

        <div class="flex flex-col flex-grow justify-between">
            <div
                class="{{ $outerBorder ?? true ? 'border-0 md:border' : '' }} flex-grow relative rounded-t-xl rounded-b-sm shadow-sm">
        
                @isset($toolbar)
                    {!! $toolbar !!}
                @endisset
                <?php
                $total_percent = 100;
                if (isset($checkbox)) {
                    $total_percent = $total_percent - 3;
                }
                if (isset($rowNumber) && $rowNumber) {
                    $total_percent = $total_percent - 2.5;
                }
                if (isset($action)) {
                    $total_percent = $total_percent - 10;
                }
                $width_percentage = 0;
                if (count($columns) > 0) {
                    $total_columns = count($columns);
                    foreach ($columns as $column) {
                        if (isset($column->width)) {
                            $total_percent = $total_percent - $column->width;
                            $total_columns = $total_columns - 1;
                        }
                    }
                    $width_percentage = $total_percent / $total_columns;
                }
                ?>
                <div>
                    <div class="table w-full rounded-t-lg bg-bg">
                        @if ((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet())
                            <div class="text-sm font-bold flex flex-row py-1 px-2 rounded-t-lg w-full border">
                                @if ($checkbox ?? false)
                                    <div style="width:3%;" class="p-2 text-center">
                                        <label class="checkbox checkbox-circle text-center">
                                            <input type="checkbox" @change="toggleListSelection($event.target.checked)"
                                                :checked="listSelected">
                                            <span></span>
                                        </label>
                                    </div>
                                @endif
                                @if ($rowNumber ?? true)
                                    <div style="width:2.5%;" class="p-2">
                                        <div class="text-center">#</div>
                                    </div>
                                @endif
        
                                @foreach ($columns as $column)
                                    <div style="width: {{ isset($column->width) ? $column->width : $width_percentage }}%"
                                        class="text-sm text-{{ $column->align }} py-1 flex items-center border-0 {{ $column->class ?? '' }} {!! $column->sortable ? 'sortable' : '' !!}"
                                        {!! htmlattributes($column->attributes ?? []) !!}>
                                        @isset($__data['label:' . $column->name])
                                            {{ $__data['label:' . $column->name] }}
                                        @else
                                            <div class="w-full text-{{ $column->align ?? 'left' }}">
                                                {!! $column->label !!}
                                            </div>
                                        @endisset
                                    </div>
                                @endforeach
        
                                @isset($action)
                                    <div style="width: {{ isset($column->width) ? $column->width : $width_percentage }}%"
                                        class="py-2 text-right pr-2">Action</div>
                                @endisset
                            </div>
                        @endif
        
                        <div class="" v-for="(row, index) in collection" :key="row[primaryKey]">
                            <Toggle :initial="false" v-slot="slotProps">
                                <div class="flex flex-col text-xs lg:text-xs" slot-scope="dd">
        
                                    <div class="flex flex-col sm:flex-col md:flex-row lg:flex-row my-2 md:m-0 py-1 px-2 rounded-2xl md:rounded-none w-full  shadow-xs transition-all duration-300 ease-in-out hover:shadow-md {{ (new \Jenssegers\Agent\Agent())->isDesktop() ? 'items-center' : '' }}"
                                        :class="[
                                            slotProps.state ? 'border-0' : 'border-b-[1px]'
                                        ]">
                                        @if ($checkbox ?? false)
                                            <div style="width:3%;" class="align-{{ $valign ?? 'middle' }} text-center">
                                                <label class="checkbox checkbox-circle">
                                                    <input type="checkbox" :value="row[primaryKey]"
                                                        :checked="isSelected(row[primaryKey])"
                                                        @change="toggleSelection(row[primaryKey], $event.target.checked)">
                                                    <span></span>
                                                </label>
                                            </div>
                                        @endif
                                        @if ($rowNumber ?? true)
                                            <div style="width:2.5%;" class="p-2 align-{{ $valign ?? 'middle' }}">
                                                <div class="text-center" v-text="index + 1"></div>
                                            </div>
                                        @endif
        
                                        @foreach ($columns as $column)
                                            @if ((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet())
                                                <div style="width: {{ isset($column->width) ? $column->width : $width_percentage }}%"
                                                    class="p-0 align-{{ $valign ?? 'middle' }} border-0"
                                                    {!! htmlattributes($column->attributes ?? []) !!} data-label="{{ $column->label }}">
                                                    <div class="text-{{ $column->align ?? 'left' }} text-xs">
                                                        @isset($__data['column:' . $column->name])
                                                            {{ $__data['column:' . $column->name] }}
                                                        @else
                                                            {!! $column->renderVueValue(
                                                                'row' .
                                                                    collect(explode('.', $column->name))->map(function ($segment) {
                                                                            return '["' . $segment . '"]';
                                                                        })->implode(''),
                                                            ) !!}
                                                        @endisset
        
                                                        @isset($column->late)
                                                            <div class="w-full bg-red-600 text-white">Late {{ $column->late }}
                                                            </div>
                                                        @endisset
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex flex-row p-2">
                                                    <div class="w-1/2 block md:hidden">
                                                        <span>{{ $column->label }}</span>
                                                    </div>
                                                    <div class="w-1/2 align-{{ $valign ?? 'middle' }}" {!! htmlattributes($column->attributes ?? []) !!}
                                                        data-label="{{ $column->label }}">
                                                        <div class="">
                                                            @isset($__data['column:' . $column->name])
                                                                {{ $__data['column:' . $column->name] }}
                                                            @else
                                                                {!! $column->renderVueValue(
                                                                    'row' .
                                                                        collect(explode('.', $column->name))->map(function ($segment) {
                                                                                return '["' . $segment . '"]';
                                                                            })->implode(''),
                                                                ) !!}
                                                            @endisset
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
        
                                        @isset($action)
                                            <div 
                                                @if ((new \Jenssegers\Agent\Agent())->isDesktop()) 
                                                    style="width: {{ isset($column->width) ? $column->width : $width_percentage }}%" 
                                                @endif
                                                class="p-0 flex {{ (new \Jenssegers\Agent\Agent())->isDesktop() ? 'justify-end' : 'justify-center' }}  align-{{ $valign ?? 'center' }} action pr-2 gap-2"
                                            >
                                                <div class="flex flex-row gap-2 items-center">
                                                    @isset($more)
                                                        <div class="flex items-center">
                                                            <div @click.stop="slotProps.toggle()"
                                                                class="flex items-center justify-between cursor-pointer"
                                                                aria-label="More Details" data-balloon-pos="up-right">
                                                                <div class="text-xs ">
                                                                    <div v-if="slotProps.state" class="flex items-center">
                                                                        <i class="fad fa-sort-circle-down text-lg"></i>
                                                                    </div>
                                                                    <div v-else class="flex items-center">
                                                                        <i class="fad fa-sort-circle-up text-lg"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endisset
                                                    {!! $action !!}
                                                </div>
                                            </div>
                                        @endisset
                                    </div>
        
                                    @isset($more)
                                        <div class="flex flex-row px-2 py-2 w-full border-b-2 border-gray-400 "
                                            v-if="slotProps.state">
                                            <div class="w-full">
                                                {!! $more !!}
                                            </div>
                                        </div>
                                    @endisset
                                </div>
                            </toggle>
                        </div>
                    </div>
                </div>

                <transition name="fade">
                    <div v-if="loading" class="absolute inset-0 flex justify-center items-center bg-white z-10">
                        <div class="text-center border-4 rounded-2xl bg-gray-400 p-4 bg-opacity-75 overflow-y-auto shadow">
                            <span class="loader w-8 h-8"></span>
                            <p class="mt-2">Loading</p>
                        </div>
                    </div>
                </transition>

                <transition name="fade">
                    <div v-if="error"
                        class="absolute inset-0 flex justify-center items-center z-10 bg-white dark:bg-gray-700 p-4">
                        <div class="text-center border-4 rounded-2xl bg-gray-400 p-4 bg-opacity-75 overflow-y-auto shadow">
                            <h4>Error Occurred</h4>
                            <div class="max-w-md overflow-auto max-h-64 mx-auto py-4">
                                <p v-text="error"></p>
                                <pre class="text-left mt-2" v-if="errorDetail" v-html="errorDetail"></pre>
                            </div>
                            <button type="button" @click="dismissError" class="mt-2 btn btn-primary">Dismiss</button>
                        </div>
                    </div>
                </transition>

            </div>
            @empty($alwaysShowAll)
                <div class="text-right pt-3">
                    @if ($showPerPageOptions ?? false)
                        <template v-if="perPageOptions">
                            Show
                            <select v-model="perPage" class="border" @input="fetchData(true)">
                                <option v-for="(perPageLabel, perPageValue) in perPageOptionsMapped" :key="perPageValue"
                                    :value="perPageValue"></option>
                            </select>
                        </template>
                    @endif

                    <vue-pagination class="flex justify-center ml-4" :current="page" :last="lastPage"
                        @update:page="(newPage) => { page.value = newPage; fetchData(false, newPage); }"
                        v-if="!(page === 1 && page === lastPage)"></vue-pagination>
                </div>
            @endempty
        </div>
        
    </template>
</vue-listing>
