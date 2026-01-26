<div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="space-y-2 text-sm">
        @if($firstExpenseDate)
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">First Expense:</span>
                <span class="text-gray-600 dark:text-gray-400">{{ $firstExpenseDate }}</span>
            </div>
        @else
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">First Expense:</span>
                <span class="text-gray-400 dark:text-gray-500">No expenses yet</span>
            </div>
        @endif

        @if($lastExpenseDate)
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">Last Expense:</span>
                <span class="text-gray-600 dark:text-gray-400">{{ $lastExpenseDate }}</span>
            </div>
        @else
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300">Last Expense:</span>
                <span class="text-gray-400 dark:text-gray-500">No expenses yet</span>
            </div>
        @endif
    </div>
</div>
