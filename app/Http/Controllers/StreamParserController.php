<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use App\Jobs\ProcessStreamJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StreamParserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $users = UserData::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('age', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('stream-parser.index', compact('users', 'search'));
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:20480'
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('imports', $fileName, 'local');

            $fullPath = Storage::disk('local')->path($path);

            ProcessStreamJob::dispatch($fullPath);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully! Processing in background.'
            ]);

        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function download()
    {
        $users = UserData::all();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'No data to download');
        }

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Name', 'Email', 'Age']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->age
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_data_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function destroy($id)
    {
        try {
            $user = UserData::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User not found');
        }
    }
}