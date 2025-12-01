<!DOCTYPE html>
<html>
<head>
    <title>Debug Import</title>
</head>
<body>
    <h1>Debug File Upload</h1>
    <form method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>File:</label>
            <input type="file" name="file" required>
        </div>
        <div>
            <label>Data Type:</label>
            <select name="data_type" required>
                <option value="users">Users</option>
                <option value="collections">Collections</option>
            </select>
        </div>
        <button type="submit">Test Upload</button>
    </form>
</body>
</html>