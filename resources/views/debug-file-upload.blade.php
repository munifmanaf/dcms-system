<!DOCTYPE html>
<html>
<head>
    <title>Debug File Upload</title>
</head>
<body>
    <h1>Debug File Upload</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" id="file" required>
        <button type="submit">Test Upload</button>
    </form>
    <div id="result"></div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            
            try {
                const response = await fetch('/debug-file-upload', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                console.log('Response:', data);
            } catch (error) {
                resultDiv.innerHTML = 'Error: ' + error.message;
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>