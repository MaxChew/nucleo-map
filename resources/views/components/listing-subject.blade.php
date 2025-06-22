@if ($href ?? false)
    <a 
        class="flex flex-col text-xs hover:underline cursor-pointer" v-if="{{ $objname ?? 'row.lesson.course.subject' }}">
        <span class="font-semibold text-xs" v-text="{{ $objname ?? 'row.lesson.course.subject' }}.code">
        </span>
        <span class="text-gray-500 dark:text-slate-400" v-text="{{ $objname ?? 'row.lesson.course.subject' }}.name">
        </span>
    </a>
@else
    <div class="flex flex-col" v-if="{{ $objname ?? 'row.lesson.course.subject' }}">
        <span class="font-semibold text-xs" v-text="{{ $objname ?? 'row.lesson.course.subject' }}.code">
        </span>
        <span class="text-gray-500 dark:text-slate-400" v-text="{{ $objname ?? 'row.lesson.course.subject' }}.name">
        </span>
    </div>
@endif
