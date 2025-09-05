<?php
// Configuration
$uploadDir = 'uploads/';
$maxFileSize = 10 * 1024 * 1024; // 10MB
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx', 'zip'];

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload
$uploadMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = basename($_FILES['file']['name']);
    $fileSize = $_FILES['file']['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $targetPath = $uploadDir . $fileName;
    
    // Validate file
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $uploadMessage = "Upload error: " . $_FILES['file']['error'];
    } elseif ($fileSize > $maxFileSize) {
        $uploadMessage = "File too large. Maximum size is " . ($maxFileSize / 1024 / 1024) . "MB.";
    } elseif (!in_array($fileExtension, $allowedTypes)) {
        $uploadMessage = "File type not allowed. Allowed types: " . implode(', ', $allowedTypes);
    } elseif (file_exists($targetPath)) {
        $uploadMessage = "File already exists.";
    } else {
        // Secure file name
        $safeFileName = preg_replace("/[^A-Za-z0-9._-]/", '_', $fileName);
        $targetPath = $uploadDir . $safeFileName;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $uploadMessage = "File uploaded successfully: $safeFileName";
            header("Location: " . $_SERVER['PHP_SELF'] . "?upload=success&file=" . urlencode($safeFileName));
            exit();
        } else {
            $uploadMessage = "Failed to upload file.";
        }
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $filePath = $uploadDir . $fileToDelete;
    
    if (file_exists($filePath) && is_file($filePath)) {
        if (unlink($filePath)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?delete=success");
            exit();
        }
    }
}

// List uploaded files
$files = array_diff(scandir($uploadDir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yalu | File Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .upload-form {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .file-list {
            list-style-type: none;
            padding: 0;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .file-item:hover {
            background-color: #f9f9f9;
        }
        .file-actions a {
            margin-left: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .file-actions a.delete {
            color: #dc3545;
        }
        .file-info {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload New File</h1>
        
        <div class="upload-form">
            <?php if (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
                <div class="message success">
                    File uploaded successfully: <?php echo htmlspecialchars($_GET['file'] ?? ''); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['delete']) && $_GET['delete'] === 'success'): ?>
                <div class="message success">
                    File deleted successfully.
                </div>
            <?php endif; ?>
            
            <?php if (!empty($uploadMessage) && !isset($_GET['upload'])): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($uploadMessage); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <button type="submit">Upload</button>
                <div class="file-info">
                    Maximum file size: <?php echo round($maxFileSize / 1024 / 1024, 2); ?>MB. 
                    Allowed types: <?php echo implode(', ', $allowedTypes); ?>
                </div>
            </form>
        </div>

        <h1>Available Files</h1>
        <?php if (count($files) > 0): ?>
            <ul class="file-list">
                <?php foreach ($files as $file): 
                    $filePath = $uploadDir . $file;
                    $fileSize = filesize($filePath);
                    $fileDate = date("Y-m-d H:i", filemtime($filePath));
                ?>
                    <li class="file-item">
                        <div>
                            <a href="<?php echo $uploadDir . $file; ?>" download><?php echo htmlspecialchars($file); ?></a>
                            <div class="file-info">
                                Size: <?php echo round($fileSize / 1024, 2); ?>KB - 
                                Uploaded: <?php echo $fileDate; ?>
                            </div>
                        </div>
                        <div class="file-actions">
                            <a href="<?php echo $uploadDir . $file; ?>" target="_blank">View</a>
                            <a href="<?php echo $uploadDir . $file; ?>" download>Download</a>
                            <a href="?delete=<?php echo urlencode($file); ?>" class="delete" 
                               onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($file); ?>?')">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No files uploaded yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>