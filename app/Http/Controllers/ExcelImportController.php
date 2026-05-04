<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExcelImportController extends Controller
{
    public function showImportForm()
    {
        return view('excel-upload');
    }
    public function importCsv(Request $request)
    {
        // ✅ Validate file
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'Unable to read file.');
        }

        // skip header
        fgetcsv($handle);

        $date = $request->date; // 🔁 you can pass this from form if needed

        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                $employeeId = trim($row[0] ?? '');
                $name       = trim($row[1] ?? '');
                $rawTimes   = $row[3] ?? '';

                // 🚫 Skip invalid rows
                if (!$employeeId || strtolower($name) === 'unknown') {
                    continue;
                }

                // 🚫 Prevent Excel corruption IDs
                if (!is_numeric($employeeId) || strlen($employeeId) > 10) {
                    continue;
                }

                // 🚫 Optional: check employee exists
                // if (!Employee::where('id', $employeeId)->exists()) {
                //     throw new \Exception("Employee not found (ID: {$employeeId})");
                // }

                // 🔥 Split multiline times
                $times = preg_split('/\r\n|\r|\n/', $rawTimes);

                $times = array_filter(array_map('trim', $times));

                if (empty($times)) {
                    throw new \Exception("No time data");
                }

                // store raw punches
                $rawJson = json_encode(array_values($times));

                // determine check-in / check-out
                sort($times);

                $checkIn  = $times[0] ?? null;
                $checkOut = count($times) > 1 ? end($times) : null;

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date'        => $date,
                    ],
                    [
                        'check_in'  => $checkIn,
                        'check_out' => $checkOut,
                        'raw_punch' => $rawJson,
                    ]
                );
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return back()->with([
            'success' => 'CSV uploaded successfully!',
            'import_errors' => $errors // ✅ renamed
        ]);
    }
}
