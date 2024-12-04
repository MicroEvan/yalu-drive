<?php
//uploads folder
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        echo "File uploaded successfully: $fileName";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; // Prevent further execution after successful upload
    } else {
        echo "Failed to upload file.";
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
    <title>Yalu |Files</title>
</head>
<body>
    <h1>New File</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <h1>Available Files</h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="<?php echo $uploadDir . $file; ?>" download><?php echo $file; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
