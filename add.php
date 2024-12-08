<?php
require_once 'core/dbConfig.php'; // Include dbConfig to access $pdo

if (isset($_POST['submit'])) {
  // Get description and title from the form
  $description = $_POST['description'];
  $title = $_POST['title'];

  // Get file name and temporary name
  $fileName = $_FILES['photo']['name'];
  $tempFileName = $_FILES['photo']['tmp_name'];

  // Get file extension
  $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

  // Generate a unique ID for the image name
  $uniqueID = sha1(md5(rand(1, 9999999)));

  // Combine the unique ID with the file extension to create a unique image name
  $imageName = $uniqueID . "." . $fileExtension;

  try {
    // Insert image details into the database using $pdo
    $query = "INSERT INTO photos (photo_name, description, title) VALUES (:photo_name, :description, :title)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':photo_name', $imageName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':title', $title);
    $stmt->execute();

    // Ensure the uploads folder exists and is writable
    $folder = "uploads/" . $imageName;

    if (!is_dir('uploads')) {
      mkdir('uploads', 0755, true); // Create uploads folder if it doesn't exist
    }

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($tempFileName, $folder)) {
      header("Location: index.php"); // Redirect after successful upload
      exit();
    } else {
      echo "Error uploading file. Make sure the uploads folder has proper permissions.";
    }
  } catch (PDOException $e) {
    echo "Error saving photo to the database: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Photo</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 flex justify-center items-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Upload Photo</h2>

    <form action="" method="POST" enctype="multipart/form-data">

      <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-800">Title:</label>
        <input type="text" name="title" id="title"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
          placeholder="Enter photo title" required>
      </div>

      <div class="mb-6">
        <label for="description" class="block text-sm font-medium text-gray-800">Description:</label>
        <textarea name="description" id="description" rows="4"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
          placeholder="Describe your photo" required></textarea>
      </div>

      <div class="mb-6">
        <label for="photo" class="block text-sm font-medium text-gray-800">Choose Image:</label>
        <input type="file" name="photo" id="photo" accept="image/*"
          class="w-full text-gray-500 bg-gray-100 file:border-0 file:py-2 file:px-4 file:mr-4 file:bg-teal-600 file:hover:bg-teal-500 file:text-white rounded-md cursor-pointer"
          required>
      </div>

      <div class="text-center mt-6">
        <input type="submit" name="submit" value="Post Moment"
          class="w-full py-2 px-4 bg-teal-600 text-white font-semibold rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
      </div>
    </form>
  </div>

</body>

</html>