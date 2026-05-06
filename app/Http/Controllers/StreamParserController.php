<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;

class StreamParserController extends Controller
{
    // Show upload page + data list
    public function index(Request $request)
    {
        $search = $request->search;

        $users = UserData::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('age', 'like', "%$search%");
        })
            ->latest()
            ->paginate(5);

        return view('stream-parser.index', compact('users', 'search'));
    }

    // Upload & parse file
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

            $header = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {

                if (count($row) !== 3)
                    continue;

                $name = trim($row[0]);
                $email = trim($row[1]);
                $age = is_numeric($row[2]) ? (int) $row[2] : null;

                if (empty($name) || empty($email))
                    continue;

                UserData::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'age' => $age,
                    ]
                );
            }

            fclose($handle);
        }

        return redirect()->back()->with('success', 'File uploaded & data stored!');
    }

    // Delete record
    public function destroy($id)
    {
        UserData::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}