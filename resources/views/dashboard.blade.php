<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include TailwindCSS if you're using Laravel's Breeze or Jetstream -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Add any additional custom styles here */
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<!-- Navigation Bar (Optional) -->
<nav class="bg-blue-600 p-4 text-white">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold">Dashboard</h1>
    </div>
</nav>

<!-- Main Content Section -->
<div class="container mx-auto mt-8">
    <h2 class="text-xl font-semibold mb-4">Welcome to Your Dashboard!</h2>
    <p class="text-gray-700">
        This is a basic dashboard template. You can customize this page to display user-specific details,
        charts, or other informative sections.
    </p>

    <!-- Example Section -->
    <div class="mt-6 border border-gray-300 p-4 bg-white rounded shadow">
        <h3 class="text-lg font-semibold">User Information</h3>
        <ul class="mt-2 list-disc list-inside text-gray-800">
            <li>Name: {{ auth()->user()->name }}</li>
            <li>Email: {{ auth()->user()->email }}</li>
            <!-- Add more user-specific details here if needed -->
        </ul>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white text-center p-4 mt-8">
    &copy; {{ date('Y') }} My Laravel App. All rights reserved.
</footer>
</body>
</html>
