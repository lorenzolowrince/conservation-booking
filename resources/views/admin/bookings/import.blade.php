@extends('layouts.admin')

@section('title', 'Import Bookings')
@section('page_title', 'Import Bookings from Excel')

@section('content')

<div class="max-w-3xl">

    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-forest-700 hover:text-forest-900 font-medium">&larr; Back to Bookings</a>
    </div>

    @if(session('import_result'))
        @php($result = session('import_result'))
        <div class="mb-6 rounded-xl border p-4 {{ empty($result['errors']) ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
            <p class="font-semibold {{ empty($result['errors']) ? 'text-green-800' : 'text-yellow-800' }}">
                {{ $result['imported'] }} booking{{ $result['imported'] === 1 ? '' : 's' }} imported successfully.
            </p>
            @if(!empty($result['errors']))
                <p class="text-sm text-yellow-800 mt-2 font-medium">{{ count($result['errors']) }} row(s) were skipped:</p>
                <ul class="mt-1 text-sm text-yellow-800 list-disc list-inside space-y-0.5 max-h-64 overflow-y-auto">
                    @foreach($result['errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="font-display font-semibold text-lg text-gray-900 mb-2">1. Download the template</h2>
        <p class="text-sm text-gray-600 mb-4">
            Start from the template so your column headers match exactly. Fill in one row per booking, then upload it below.
        </p>
        <a href="{{ route('admin.bookings.import.template') }}" class="btn-secondary py-2 px-4 text-sm">
            Download CSV Template
        </a>

        <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs text-gray-600">
            <div><dt class="font-semibold text-gray-800 inline">conservation_area</dt> — required. Area code (e.g. DVCA) or name.</div>
            <div><dt class="font-semibold text-gray-800 inline">contact_name / email / phone / nationality</dt> — required.</div>
            <div><dt class="font-semibold text-gray-800 inline">check_in_date / check_out_date</dt> — required. Format YYYY-MM-DD.</div>
            <div><dt class="font-semibold text-gray-800 inline">subtotal</dt> — required. Number only.</div>
            <div><dt class="font-semibold text-gray-800 inline">package / accommodation_type</dt> — optional. Must match an existing name under that area.</div>
            <div><dt class="font-semibold text-gray-800 inline">booking_ref</dt> — optional. Auto-generated if left blank.</div>
            <div><dt class="font-semibold text-gray-800 inline">num_adults / num_children / tax / total_amount</dt> — optional, sensible defaults apply.</div>
            <div><dt class="font-semibold text-gray-800 inline">status / payment_status</dt> — optional. Defaults to pending / unpaid.</div>
        </dl>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-display font-semibold text-lg text-gray-900 mb-2">2. Upload your file</h2>
        <p class="text-sm text-gray-600 mb-4">
            Accepts .xlsx, .xls, or .csv, up to 5MB. Rows with errors are skipped and listed after upload — the rest still import.
        </p>

        <form method="POST" action="{{ route('admin.bookings.import') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="flex-1 min-w-[16rem]">
                <label class="form-label text-xs">Excel / CSV file</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-input py-2 text-sm">
                @error('file')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-primary py-2 px-4 text-sm">Upload &amp; Import</button>
        </form>
    </div>

</div>

@endsection
