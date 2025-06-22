<div class="flex items-center space-x-4 p-3 w-full bg-secondary-50 dark:bg-secondary-900 rounded-lg">
    <div class="min-w-8 max-w-10">
        <img :src="{{ $avatarUrl ?? '/images/default-profile.jpg' }}" class="rounded-full" alt="Profile Image">
    </div>
    <div class="w-full flex flex-col">
        <h3 class="text-secondary-700 dark:text-secondary-300">
            @if (isset($vTextName))
                <span v-text="{{ $vTextName }}"></span>
            @else
                <span>{{ $name }}</span>
            @endif
        </h3>
        <p class="text-xs text-gray-600 dark:text-gray-400">
            @if (isset($vTextCode))
                <span v-text="{{ $vTextCode }}"></span>
            @else
                <span>{{ $code }}</span>
            @endif
        </p>
        <p class="text-xs text-gray-600 dark:text-gray-400">
            @if (isset($vTextEmail))
                <span v-text="{{ $vTextEmail }}"></span>
            @else
                <span>{{ $email }}</span>
            @endif
        </p>
    </div>
</div>
