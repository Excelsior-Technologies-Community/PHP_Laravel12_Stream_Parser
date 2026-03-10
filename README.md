# PHP_Laravel12_Stream_Parser

## Introduction

PHP_Laravel12_Stream_Parser is a demonstration project built with Laravel 12 that showcases how to efficiently process and store large datasets using stream parsing. Traditional file processing loads the entire file into memory, which can lead to performance issues for large files. This project solves that by reading files line by line, enabling smooth handling of massive CSV or text files without exhausting system resources.

---

## Project Overview

This project allows users to:

- Upload large CSV or TXT files containing user data.

- Parse files efficiently using a streaming approach.

- Insert or update records in the database row by row.

- Prevent duplicates using email as a unique key.

- Handle malformed or incomplete rows gracefully.

- Provide immediate feedback after successful upload.

---

## System Requirements

PHP >= 8.1

Laravel 12

Composer

MySQL or any supported database

Node.js & NPM (for frontend assets)

---

# Project Setup

## Step 1: Create Laravel Project

Open your terminal and run:

```bash
composer create-project laravel/laravel PHP_Laravel12_Stream_Parser "12.*"
cd PHP_Laravel12_Stream_Parser
```

This will create a fresh Laravel 12 project.

---

## Step 2: Configure .env

Update your .env file for database connection:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stream_parser_db
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate
```

---

## Step 3: Create Database Table

We’ll create a users_data table to store parsed file data:

```bash
php artisan make:migration create_users_data_table --create=users_data
```

File: database/migrations/xxxx_xx_xx_create_users_data_table.php

Update the migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_users_data_table.php
    public function up(): void
    {
        Schema::create('users_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('age')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_data');
    }
};
```

Run the migration:

```bash
php artisan migrate
```

---

## Step 4: Create Model

```bash
php artisan make:model UserData
```

File: app/Models/UserData.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    // Explicitly define the correct table name
    protected $table = 'users_data';

    protected $fillable = [
        'name', 'email', 'age'
    ];
}
```

---

## Step 5: Create Controller

```bash
php artisan make:controller StreamParserController
```

File: app/Http/Controllers/StreamParserController.php

```php
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
```

---

## Step 6: Create Routes

File: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamParserController;

Route::get('/', [StreamParserController::class, 'index']);
Route::post('/upload', [StreamParserController::class, 'upload'])->name('upload');
```

---

## Step 7: Create View

File: resources/views/stream-parser/index.blade.php 

```blade
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
```

---

## Step 8: Test Project

Start Laravel server:

```bash
php artisan serve
```
Visit: 

```bash
http://127.0.0.1:8000
```

Open NoteBook and Save this txt file:

```
name,email,age
John Doe,john@example.com,28
Jane Smith,jane@example.com,32
Alice Johnson,alice@example.com,25
Bob Brown,bob@example.com,40
Emma Wilson,emma@example.com,29
Liam Miller,liam@example.com,35
Olivia Davis,olivia@example.com,27
Noah Taylor,noah@example.com,31
Sophia Anderson,sophia@example.com,26
James Thomas,james@example.com,38
```

Now Upload a sample txt file

Check your users_data table in the database — data should be stored correctly.

---

## Output

<img width="1918" height="1030" alt="Screenshot 2026-03-10 153443" src="https://github.com/user-attachments/assets/99f8e9ed-1dfc-4a64-adb3-23eb1931a8b0" />

<img width="1919" height="1032" alt="Screenshot 2026-03-10 153456" src="https://github.com/user-attachments/assets/8a074e22-7035-4f5d-88a7-31b4c313476b" />

---

## Project Structure

```
PHP_Laravel12_Stream_Parser/
├─ app/
│  ├─ Http/Controllers/StreamParserController.php
│  └─ Models/UserData.php
├─ database/
│  ├─ migrations/
│  │  └─ create_users_data_table.php
├─ resources/views/stream-parser/index.blade.php
├─ routes/web.php
├─ public/
├─ composer.json
└─ .env
```

---

Your PHP_Laravel12_Stream_Parser Project is now ready!
