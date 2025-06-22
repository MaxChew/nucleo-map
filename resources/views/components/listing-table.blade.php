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
>
    <div class="flex flex-col flex-grow justify-between">
        <div class="{{ ($outerBorder ?? true) ? 'border-4' : '' }} bg-white shadow-lg flex-grow relative rounded-2xl py-3 px-1">

            @isset($toolbar)
                {!! $toolbar !!}
            @endisset

            <div>
                <table class="table">
                    <thead>
                        <tr class="bg-white font-bold text-black">
                            @if($checkbox ?? false)
                            <th style="width:1px">
                                <label class="checkbox checkbox-circle">
                                    <input type="checkbox" @change="toggleListSelection($event.target.checked)"
                                        :checked="listSelected">
                                    <span></span>
                                </label>
                            </th>
                            @endif
                            @if($rowNumber ?? true)
                            <th style="width:1px">
                                <div class="text-center">#</div>
                            </th>
                            @endif

                            @foreach($columns as $column)
                            <th {!! $column->sortable ? 'class="sortable"' : '' !!}
                                :class="sortStatus['{{ $column->name }}'] ? 'sorting-' + sortStatus['{{ $column->name }}'] : false"
                                @if($column->sortable)
                                    @click.shift.exact="unsetSort('{{ $column->name }}')"
                                    @click.ctrl.exact="setSort('{{ $column->name }}', null, false)"
                                    @click.exact="setSort('{{ $column->name }}')"
                                @endif

                                @if(isset($column->width))
                                    width="{{ $column->width }}"
                                @endif

                                {!! htmlattributes($column->attributes ?? []) !!}
                            >
                                @isset($__data['label:'.$column->name])
                                    {{ $__data['label:'.$column->name] }}
                                @else
                                <div class="text-{{ $column->align }}">
                                    {{ $column->label }}
                                </div>
                                @endisset
                            </th>
                            @endforeach

                            @isset($action)
                            <th width="1px">Action</th>
                            @endisset
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-xs lg:text-base" v-for="(row, index) in collection" :key="row[primaryKey]"
                            {!! htmlattributes($rowAttributes ?? (($rowBorder ?? false) ? ['class' => 'border-t'] : [])) !!} >
                            @if($checkbox ?? false)
                            <td class="align-{{ $valign ?? 'middle' }}">
                                <label class="checkbox checkbox-circle">
                                    <input type="checkbox"
                                        :value="row[primaryKey]"
                                        :checked="isSelected(row[primaryKey])"
                                        @change="toggleSelection(row[primaryKey], $event.target.checked)"
                                    >
                                    <span></span>
                                </label>
                            </td>
                            @endif
                            @if($rowNumber ?? true)
                            <td class="align-{{ $valign ?? 'middle' }}">
                                <div class="text-center" v-text="from + index"></div>
                            </td>
                            @endif

                            @foreach($columns as $column)
                            <td class="align-{{ $valign ?? 'middle' }}"
                                {!! htmlattributes($column->attributes ?? []) !!}
                                data-label="{{ $column->label }}"
                            >
                                <div class="text-{{ $column->align }}">

                                    @isset($__data['column:'.$column->name])
                                        {{ $__data['column:'.$column->name] }}
                                    @else
                                        {!! $column->renderVueValue('$.get(row, "' . $column->name . '")') !!}
                                    @endisset
                                </div>
                            </td>
                            @endforeach

                            @isset($action)
                            <td class="align-{{ $valign ?? 'middle' }} action">
                                <div>
                                    {!! $action !!}
                                </div>
                            </td>
                            @endisset
                        </tr>
                    </tbody>
                </table>
            </div>

            <transition name="fade">
                <div v-if="loading" class="w-full absolute inset-0 flex justify-center items-center bg-white bg-opacity-50 z-30">
                    <div class="text-center">
                        <span class="loader w-8 h-8"></span>
                        <p class="mt-2">Loading</p>
                    </div>
                </div>
            </transition>

            <transition name="fade">
                <div v-if="error" class="w-full absolute inset-0 flex justify-center items-center bg-white bg-opacity-50 z-30">
                    <div class="text-center">
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
        <div class="text-center pt-3">
            <?php $showPerPageOptions  = false ?> <!-- MAX SET OFF ALL THE TIME FIRST -->
            @if($showPerPageOptions ?? true)
            <template v-if="perPageOptions">
                Show
                <select v-model="perPage" class="border" @input="fetchData(true)">
                    <option v-for="(perPageLabel, perPageValue) in perPageOptionsMapped"
                        v-text="perPageLabel" :value="perPageValue"
                    ></option>
                </select>
            </template>
            @endif
            <vue-pagination
                class="inline-block ml-4"
                :current="page"
                :last="lastPage"
                @click-page="setPage($event), $scroll($el)"
                @if($hidePaginationIfOnePage ?? false)
                    v-if="! (page === 1 && page === lastPage)"
                @endif
            ></vue-pagination>
        </div>
        @endempty
    </div>
</vue-listing>