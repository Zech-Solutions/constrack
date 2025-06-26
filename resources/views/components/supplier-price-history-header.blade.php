@php
    $columns = $columns ?? [];

    if ($columns instanceof \Closure) {
        $columns = $columns();
    }

    if (!is_array($columns)) {
        $columns = [];
    }
@endphp

@if (!empty($columns))
    <div class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
        Supplier prices history
    </div>

    <div class="grid gap-4 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-t dark:bg-gray-800 dark:text-gray-200"
        style="display: grid; grid-template-columns: repeat({{ count($columns) }}, minmax(0, 1fr));">
        @foreach ($columns as $column)
            <div>{{ $column }}</div>
        @endforeach
    </div>
@endif
