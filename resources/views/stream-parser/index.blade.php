<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stream Parser Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" />
</head>
<body class="bg-gray-50 p-6">

    <div class="max-w-6xl mx-auto">
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-sm rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-8 rounded-xl shadow-sm mb-8 border border-gray-200">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Upload CSV File</h2>
            
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" 
                  class="dropzone border-dashed border-2 border-blue-400 rounded-lg p-10 flex flex-col items-center justify-center cursor-pointer hover:bg-blue-50 transition" 
                  id="myDropzone">
                @csrf
                <div class="dz-message text-gray-500">
                    <p class="text-lg font-semibold">Drag & Drop your CSV file here</p>
                    <p class="text-sm">or click to browse</p>
                </div>
            </form>

            <div class="mt-4 flex justify-end gap-2">
                <button id="submit-all" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 transition font-bold shadow-md">
                    Upload Now
                </button>
                <a href="{{ route('download') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-bold shadow-md">
                    Download CSV
                </a>
            </div>
        </div>

        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm flex items-center gap-2 border border-gray-200">
            <form method="GET" class="w-full flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name, email or age..." 
                    class="border border-gray-300 p-2 rounded-lg w-full focus:ring-2 focus:ring-blue-400 outline-none">
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-black transition">Search</button>
                @if($search ?? false)
                    <a href="{{ route('index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Users Data</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3 text-left border">ID</th>
                            <th class="p-3 text-left border">Name</th>
                            <th class="p-3 text-left border">Email</th>
                            <th class="p-3 text-left border">Age</th>
                            <th class="p-3 text-left border">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 border">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="p-3 border">{{ $user->name }}</td>
                                <td class="p-3 border">{{ $user->email }}</td>
                                <td class="p-3 border">{{ $user->age }}</td>
                                <td class="p-3 border">
                                    <form action="{{ route('delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 font-semibold underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No Data Found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone("#myDropzone", {
            url: "{{ route('upload') }}",
            autoProcessQueue: false,
            maxFilesize: 20,
            acceptedFiles: ".csv,.txt",
            parallelUploads: 1,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            init: function() {
                var dz = this;
                
                document.querySelector("#submit-all").addEventListener("click", function(e) {
                    e.preventDefault();
                    if (dz.getQueuedFiles().length === 0) {
                        alert('Please select a file first');
                        return;
                    }
                    dz.processQueue();
                });

                this.on("success", function(file, response) {
                    if (response.success) {
                        alert(response.message || 'File uploaded successfully!');
                        window.location.reload();
                    } else {
                        alert(response.message || 'Upload failed');
                    }
                });

                this.on("error", function(file, response) {
                    var message = typeof response === 'string' ? response : (response.message || 'Upload failed');
                    alert(message);
                });

                this.on("sending", function(file, xhr, formData) {
                    formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
                });
            }
        });
    </script>
</body>
</html>