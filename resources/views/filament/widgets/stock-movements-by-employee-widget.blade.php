<x-filament-widgets::widget>
    <x-filament::section heading="Stock Movements by Employee">
        @php $rows = $this->getEmployeeStats(); @endphp

        @if ($rows->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">No stock movements recorded yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">Employee</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Movements</th>
                            <th class="py-2 pr-4 font-semibold text-green-700 dark:text-green-400 text-center">Total Added</th>
                            <th class="py-2 pr-4 font-semibold text-red-700 dark:text-red-400 text-center">Total Removed</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-center">Net Change</th>
                            <th class="py-2 font-semibold text-gray-700 dark:text-gray-300">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $row->employee_name }}
                                </td>
                                <td class="py-2 pr-4 text-center text-gray-700 dark:text-gray-300">
                                    {{ number_format($row->total_movements) }}
                                </td>
                                <td class="py-2 pr-4 text-center text-green-700 dark:text-green-400 font-medium">
                                    +{{ number_format($row->total_added) }}
                                </td>
                                <td class="py-2 pr-4 text-center text-red-700 dark:text-red-400 font-medium">
                                    -{{ number_format($row->total_removed) }}
                                </td>
                                <td class="py-2 pr-4 text-center font-semibold
                                    {{ $row->net_change > 0 ? 'text-green-700 dark:text-green-400' : ($row->net_change < 0 ? 'text-red-700 dark:text-red-400' : 'text-gray-500') }}">
                                    {{ $row->net_change > 0 ? '+' : '' }}{{ number_format($row->net_change) }}
                                </td>
                                <td class="py-2 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $row->last_movement ? \Carbon\Carbon::parse($row->last_movement)->format('M d, Y H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
