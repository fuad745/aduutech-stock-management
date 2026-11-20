<x-filament-panels::page>
    {{-- Date range filter --}}
    <x-filament::section heading="Filter by Date Range">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                />
            </div>
        </div>
    </x-filament::section>

    {{-- Summary stats --}}
    @php $summary = $this->getSummary(); @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['total_movements']) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Movements</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-green-700 dark:text-green-400">+{{ number_format($summary['total_added']) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Added</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-red-700 dark:text-red-400">-{{ number_format($summary['total_removed']) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Removed</p>
            </div>
        </x-filament::section>
    </div>

    {{-- Per-employee table --}}
    @php $rows = $this->getEmployeeStats(); @endphp
    <x-filament::section heading="Movements by Employee">
        @if ($rows->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center">
                No stock movements found for the selected date range.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300">Employee</th>
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-center"># Movements</th>
                            <th class="py-3 pr-4 font-semibold text-green-700 dark:text-green-400 text-center">Added</th>
                            <th class="py-3 pr-4 font-semibold text-red-700 dark:text-red-400 text-center">Removed</th>
                            <th class="py-3 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Net Change</th>
                            <th class="py-3 font-semibold text-gray-700 dark:text-gray-300">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-3 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $row->employee_name }}
                                </td>
                                <td class="py-3 pr-4 text-center text-gray-700 dark:text-gray-300">
                                    {{ number_format($row->total_movements) }}
                                </td>
                                <td class="py-3 pr-4 text-center text-green-700 dark:text-green-400 font-medium">
                                    +{{ number_format($row->total_added) }}
                                </td>
                                <td class="py-3 pr-4 text-center text-red-700 dark:text-red-400 font-medium">
                                    -{{ number_format($row->total_removed) }}
                                </td>
                                <td class="py-3 pr-4 text-center font-semibold
                                    @if ($row->net_change > 0) text-green-700 dark:text-green-400
                                    @elseif ($row->net_change < 0) text-red-700 dark:text-red-400
                                    @else text-gray-500 dark:text-gray-400 @endif">
                                    {{ $row->net_change > 0 ? '+' : '' }}{{ number_format($row->net_change) }}
                                </td>
                                <td class="py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $row->last_movement ? \Carbon\Carbon::parse($row->last_movement)->format('M d, Y H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
