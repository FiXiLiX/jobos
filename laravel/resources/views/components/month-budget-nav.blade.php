<div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
    <a href="{{ url('/admin/reports/month-budget-report/' . $prevYear . '/' . $prevMonth) }}" class="fi-btn fi-btn-secondary">&laquo; {{ $prevLabel }}</a>
    <span style="font-weight: bold;">{{ $currentLabel }}</span>
    <a href="{{ url('/admin/reports/month-budget-report/' . $nextYear . '/' . $nextMonth) }}" class="fi-btn fi-btn-secondary">{{ $nextLabel }} &raquo;</a>
</div>
