<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - Shopify Calendar App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Event Details</h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($event['title']); ?></h2>
            <p class="mb-2"><strong>Start:</strong> <?php echo $event['start_date']; ?></p>
            <p class="mb-2"><strong>End:</strong> <?php echo $event['end_date']; ?></p>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            
            <h3 class="text-lg font-semibold mb-2">Documents</h3>
            <?php if (empty($documents)): ?>
                <p class="mb-4">No documents uploaded yet.</p>
            <?php else: ?>
                <ul class="list-disc pl-5 mb-4">
                    <?php foreach ($documents as $doc): ?>
                        <li class="mb-2">
                            <a href="/document/download/<?php echo $doc['id']; ?>" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($doc['filename']); ?></a>
                            <a href="/document/delete/<?php echo $doc['id']; ?>" class="text-red-500 hover:underline ml-2" onclick="return confirm('Are you sure you want to delete this document?');">Delete</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="/event/upload-document/<?php echo $event['id']; ?>" method="POST" enctype="multipart/form-data" class="mb-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="document">
                        Upload New Document
                    </label>
                    <input type="file" name="document" id="document" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Upload Document
                </button>
            </form>

            <a href="/event/edit/<?php echo $event['id']; ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Edit Event
            </a>
        </div>
    </div>
</body>
</html>