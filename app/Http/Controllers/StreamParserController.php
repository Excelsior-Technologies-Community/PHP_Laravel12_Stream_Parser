<?php

// app/Http/Controllers/StreamParserController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;

class StreamParserController extends Controller
{
    public function index()
    {
        return view('stream-parser.index');
    }

    public function upload(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');

        // Open file stream
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

            // Skip header row
            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {

                // Skip empty or malformed rows
                if (count($row) < 3) continue;

                // Clean data
                $name  = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $age   = trim($row[2] ?? null);

                // Skip if email is empty
                if (empty($email)) continue;

                // Insert or update record based on email
                UserData::updateOrCreate(
                    ['email' => $email], // unique key
                    [
                        'name' => $name,
                        'age'  => $age,
                    ]
                );
            }

            fclose($handle);
        }

        return redirect()->back()->with('success', 'File parsed and data stored successfully!');
    }
}