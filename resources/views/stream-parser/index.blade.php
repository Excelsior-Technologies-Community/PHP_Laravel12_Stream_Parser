<!-- resources/views/stream-parser/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Stream Parser</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-100 to-purple-200 min-h-screen flex items-center justify-center p-4">

<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md border border-gray-200">
    <h1 class="text-3xl font-extrabold mb-6 text-center text-indigo-700 drop-shadow-md">
        Stream Parser
    </h1>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block text-gray-700 font-medium mb-2">Upload CSV File</label>
            <input 
                type="file" 
                name="file" 
                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition duration-200 cursor-pointer"
            >
            @error('file') 
                <p class="text-red-500 mt-2 text-sm">{{ $message }}</p> 
            @enderror
        </div>

        <button type="submit" 
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg shadow-md transition duration-200 hover:shadow-lg">
            Upload & Parse
        </button>
    </form>

    <div class="mt-6 text-center text-gray-500 text-sm">
        Supported format: <span class="font-medium">CSV or TXT</span> | Max size: 10MB
    </div>
</div>

</body>
</html>