<x-filament-panels::page>

    {{-- Search form --}}
    <x-filament::section heading="Lot / Batch Recall Search">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Enter a lot number to find all outbound movements for that batch — including which customers or facilities received it.
        </p>
        <form wire:submit.prevent="search" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Lot Number / Batch Number
                </label>
                <input
                    type="text"
                    wire:model="lotNumber"
                    placeholder="e.g. LOT-2025-001"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                />
                @error('lotNumber')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button
                type="submit"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors"
            >
                Search
            </button>
        </form>
    </x-filament::section>

    {{-- Results --}}
    @if ($searched)
        @php $movements = $this->getMovements(); @endphp

        @if ($movements->isEmpty())
            <x-filament::section>
                <div class="text-center py-8">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No outbound movements found for lot <strong>{{ $lotNumber }}</strong></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This lot either has no recorded removals, or the lot number does not exist.</p>
                </div>
            </x-filament::section>
        @else
            {{-- Summary card --}}
            @php
                $firstMovement = $movements->first();
                $productName   = $firstMovement->product->product_name ?? '—';
                $lotNum        = $firstMovement->batch->lot_number ?? $lotNumber;
                $expiryDate    = $firstMovement->batch?->expiry_date?->format('d M Y') ?? '—';
                $totalQty      = $movements->sum(fn ($m) => abs($m['quantity_change']));
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-filament::section>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Lot Number</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $lotNum }}</p>
                    </div>
                </x-filament::section>
                <x-filament::section>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Product</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $productName }}</p>
                    </div>
                </x-filament::section>
                <x-filament::section>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Total Dispatched</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($totalQty) }} units</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">across {{ $movements->count() }} movement(s)</p>
                    </div>
                </x-filament::section>
            </div>

            {{-- Movement table --}}
            <x-filament::section heading="Dispatch Records">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Customer / Facility</th>
                                <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Qty Dispatched</th>
                                <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Recorded By</th>
                                <th class="py-3 font-semibold text-gray-700 dark:text-gray-300">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movements as $movement)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="py-3 pr-4 text-gray-900 dark:text-white text-xs">
                                        {{ \Carbon\Carbon::parse($movement->created_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        @if ($movement->customer)
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $movement->customer->name }}</span>
                                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ ucfirst($movement->customer->type) }})</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 italic text-xs">Not recorded</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-center font-semibold text-red-600 dark:text-red-400">
                                        {{ abs($movement->quantity_change) }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $movement->user?->name ?? 'System' }}
                                    </td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $movement->note ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    @endif

</x-filament-panels::page>
