<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo htmlspecialchars($group['name']); ?> - Shopify Calendar App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Manage Users - <?php echo htmlspecialchars($group['name']); ?></h1>
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-semibold mb-2">Current Users</h2>
            <ul class="list-disc pl-5 mb-4">
                <?php foreach ($groupUsers as $user): ?>
                    <li class="mb-2">
                        <?php echo htmlspecialchars($user['username']); ?>
                        <form action="/groups/manage-users/<?php echo $group['id']; ?>" method="POST" class="inline">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="text-red-500 hover:underline ml-2">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h2 class="text-xl font-semibold mb-2">Add User</h2>
            <form action="/groups/manage-users/<?php echo $group['id']; ?>" method="POST" class="mb-4">
                <input type="hidden" name="action" value="add">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">
                        Select User
                    </label>
                    <select name="user_id" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php foreach ($allUsers as $user): ?>
                            <?php if (!in_array($user['id'], array_column($groupUsers, 'id'))): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Add User
                </button>
            </form>

            <a href="/groups" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Back to Groups
            </a>
        </div>
    </div>
</body>
</html>