<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📤 Upload Attendance (CSV)</h5>
        </div>

        <div class="card-body">

            {{-- Success Message --}}
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            {{-- Error List --}}
            @if(session('import_errors'))
            <div class="alert alert-danger">
                <strong>Import Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Validation Errors --}}
            {{-- Validation Errors --}}
            @if ($errors instanceof \Illuminate\Support\MessageBag && $errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Upload Form --}}
            <form action="{{ route('attendance.import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">

                    {{-- Date --}}
                    <div class="col-md-4">
                        <label class="form-label">📅 Attendance Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    {{-- File --}}
                    <div class="col-md-6">
                        <label class="form-label">📁 CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>

                    {{-- Submit --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-success w-100">
                            Upload
                        </button>
                    </div>

                </div>
            </form>

            {{-- Helper --}}
            <div class="mt-4 p-3 bg-light rounded border">
                <h6>📌 CSV Format Guide</h6>
                <small>
                    <pre class="mb-0">employee_id,name,department,times
507,John Doe,Company,"09:40
09:40
18:47
18:47"</pre>
                </small>
            </div>

        </div>
    </div>

</div>