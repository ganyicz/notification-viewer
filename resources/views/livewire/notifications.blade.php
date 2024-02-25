<?php

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

new class extends Component
{
    #[Url]
    public ?string $active = null;

    public function with()
    {
        return [
            'notifications' => collect(File::allFiles(storage_path('notifications')))
                ->keyBy(fn (SplFileInfo $file) => $file->getFilenameWithoutExtension())
                ->map(fn (SplFileInfo $file) => str($file->getFilenameWithoutExtension())->replace('_', ' '))
                ->all(),
        ];
    }
} ?>

<div class="h-screen flex items-stretch bg-white">
    <div class="shrink-0 w-96 border-r divide-y bg-gray-50">
        @foreach($notifications as $key => $name)
            <a
                href="{{ route('notifications.index', ['active' => $key]) }}"
                wire:click.prevent="$set('active', '{{ $key }}')"
                @class([
                    'block py-4 px-4 text-sm hover:bg-gray-100 whitespace-nowrap overflow-hidden text-ellipsis',
                    'font-semibold' => $active === $key,
                ])
            >
                {{ $name }}
            </a>
        @endforeach
    </div>
    <div class="grow flex flex-col">
        <div class="shrink-0 p-4 bg-gray-50 text-sm border-b text-center">
            {{ $notifications[$active] ?? 'Select a notification' }}
        </div>

        @if ($active)
        <iframe class="w-full grow" src="{{ route('notifications.view', $active) }}"></iframe>
        @endif
    </div>
</div>
