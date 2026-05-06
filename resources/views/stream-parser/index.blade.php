<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stream Parser</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto">

        <!-- SUCCESS ALERT -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- UPLOAD CARD -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Upload File</h2>

            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="border p-2 rounded w-full mb-3">
                @error('file') <p class="text-red-500">{{ $message }}</p> @enderror

                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Upload
                </button>
            </form>
        </div>

        <!-- SEARCH -->
        <div class="mb-4">
            <form method="GET">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email or age..."
                    class="border p-2 rounded w-1/3">
                <button class="bg-gray-800 text-white px-4 py-2 rounded">
                    Search
                </button>
            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Users Data</h2>

            <table class="w-full border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Age</th>
                        <th class="p-2 border">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr class="text-center">
                            <td class="p-2 border">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="p-2 border">{{ $user->name }}</td>
                            <td class="p-2 border">{{ $user->email }}</td>
                            <td class="p-2 border">{{ $user->age }}</td>
                            <td class="p-2 border">
                                <form action="{{ route('delete', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-3 text-center">No Data Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- PAGINATION -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

    </div>

</body>

</html>