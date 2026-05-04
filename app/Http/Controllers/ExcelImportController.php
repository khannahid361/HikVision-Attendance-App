<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExcelImportController extends Controller
{
    public function showImportForm()
    {
        return view('excel-upload');
    }
    public function attendanceImport(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Handle the file upload and import logic here
        // You can use a package like Maatwebsite Excel to process the Excel file

        return redirect()->back()->with('success', 'Attendance data imported successfully.');
    }
}
