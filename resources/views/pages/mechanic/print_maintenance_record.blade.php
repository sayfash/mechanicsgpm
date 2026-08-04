@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="glass-panel w-full max-w-3xl mx-auto p-6 rounded-2xl shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-bold">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-100">Maintenance Record Details</h3>
                    <p class="text-xs text-slate-400 font-mono">Job Code: {{ $record['id'] ?? '' }}</p>
                </div>
            </div>
        </div>
        <!-- Body (same content as modal) -->
        <div class="overflow-y-auto space-y-6 pr-2 custom-scrollbar flex-1 text-xs">
            {!! view('pages.mechanic._record_detail_body', ['record' => $record])->render() !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger browser print dialog automatically when the page loads
        window.print();
    });
</script>
@endpush
