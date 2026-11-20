<x-filament-panels::page>

    {{-- Header actions --}}
    <div class="flex items-center justify-between mb-2">
        <div></div>
        <button
            wire:click="$set('showNewSessionForm', true)"
            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors"
        >
            + Start New Count
        </button>
    </div>

    {{-- New session form --}}
    @if ($showNewSessionForm)
        <x-filament::section heading="New Stock Count Session">
            <form wire:submit.prevent="startNewSession" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Session Name
                    </label>
                    <input
                        type="text"
                        wire:model="newSessionName"
                        placeholder="e.g. November 2026 Count"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    />
                    @error('newSessionName')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Start Count
                    </button>
                    <button type="button" wire:click="$set('showNewSessionForm', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </x-filament::section>
    @endif

    {{-- Active session count sheet --}}
    @php $activeSession = $this->getActiveSession(); @endphp

    @if ($activeSession)
        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $activeSession->name }}</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $activeSession->items->count() }} batches to count
                        · {{ $activeSession->items->whereNotNull('counted_quantity')->count() }} counted
                    </span>
                </div>
                <div class="flex gap-2">
                    @if ($activeSession->status !== 'completed')
                        <button
                            wire:click="completeSession({{ $activeSession->id }})"
                            wire:confirm="Complete this count? All discrepancies will create adjustment movements."
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            Complete Count
                        </button>
                    @endif
                    <button wire:click="$set('activeSessionId', null)" class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Close
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300">Product</th>
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300">Lot</th>
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300">Expiry</th>
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300 text-center">System Qty</th>
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300 text-center">Counted Qty</th>
                            <th class="py-3 pr-3 font-semibold text-gray-700 dark:text-gray-300 text-center">Discrepancy</th>
                            <th class="py-3 font-semibold text-gray-700 dark:text-gray-300">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeSession->items as $item)
                            @php
                                $status = $item->status;
                                $discColor = match($status) {
                                    'match'     => 'text-green-600 dark:text-green-400',
                                    'short'     => 'text-red-600 dark:text-red-400',
                                    'over'      => 'text-blue-600 dark:text-blue-400',
                                    default     => 'text-gray-400 dark:text-gray-500',
                                };
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 pr-3 text-gray-900 dark:text-white text-xs font-medium">
                                    {{ $item->batch?->product?->product_name ?? '—' }}
                                </td>
                                <td class="py-2 pr-3 text-xs text-gray-600 dark:text-gray-300 font-mono">
                                    {{ $item->batch?->lot_number ?? '—' }}
                                </td>
                                <td class="py-2 pr-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item->batch?->expiry_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="py-2 pr-3 text-center font-semibold text-gray-900 dark:text-white text-xs">
                                    {{ $item->system_quantity }}
                                </td>
                                <td class="py-2 pr-3 text-center">
                                    @if ($activeSession->status !== 'completed')
                                        <input
                                            type="number"
                                            min="0"
                                            value="{{ $item->counted_quantity ?? '' }}"
                                            placeholder="—"
                                            class="w-20 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-xs text-center bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:border-transparent"
                                            wire:change="saveCount({{ $item->id }}, $event.target.value === '' ? null : parseInt($event.target.value), null)"
                                        />
                                    @else
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $item->counted_quantity ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-3 text-center text-xs font-bold {{ $discColor }}">
                                    @if ($item->discrepancy !== null)
                                        {{ $item->discrepancy > 0 ? '+' : '' }}{{ $item->discrepancy }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item->note ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    {{-- Past sessions list --}}
    <x-filament::section heading="Count Sessions">
        @php $sessions = $this->getSessions(); @endphp

        @if ($sessions->isEmpty())
            <div class="text-center py-8">
                <div class="text-4xl mb-3">📋</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">No stock count sessions yet. Start one above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Session</th>
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Created By</th>
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                            <th class="py-3 font-semibold text-gray-700 dark:text-gray-300"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            @php
                                $badgeClass = match($session->status) {
                                    'completed'   => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'in_progress' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                    default       => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-3 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $session->name }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $session->creator?->name ?? 'System' }}
                                </td>
                                <td class="py-3 pr-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $session->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3">
                                    <button
                                        wire:click="openSession({{ $session->id }})"
                                        class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-medium"
                                    >
                                        {{ $session->status === 'completed' ? 'View' : 'Open' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>
