<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups - Shopify Calendar App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Groups</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <a href="/groups/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Create New Group</a>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Name</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): ?>
                        <tr>
                            <td class="py-2"><?php echo htmlspecialchars($group['name']); ?></td>
                            <td class="py-2">
                                <a href="/groups/edit/<?php echo $group['id']; ?>" class="text-blue-500 hover:underline mr-2">Edit</a>
                                <a href="/groups/manage-users/<?php echo $group['id']; ?>" class="text-green-500 hover:underline mr-2">Manage Users</a>
                                <a href="/groups/delete/<?php echo $group['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this group?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>