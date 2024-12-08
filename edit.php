<?php
require_once 'core/dbConfig.php'; // Include dbConfig to access $pdo

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
  $photoID = $_GET['id'];

  // Fetch the current photo details from the database
  try {
    $query = "SELECT * FROM photos WHERE photo_id = :photo_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':photo_id', $photoID);
    $stmt->execute();

    // Fetch the photo details
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      echo "Photo not found!";
      exit();
    }

    $currentDescription = $row['description'];
    $currentTitle = $row['title'];
    $currentPhotoName = $row['photo_name'];
  } catch (PDOException $e) {
    echo "Error fetching photo details: " . $e->getMessage();
    exit();
  }
}

// Handle the form submission
if (isset($_POST['submit'])) {
  // Get updated description and title from the form
  $description = $_POST['description'];
  $title = $_POST['title'];

  // Get new file name and temporary name (if a new file is uploaded)
  $fileName = $_FILES['photo']['name'];
  $tempFileName = $_FILES['photo']['tmp_name'];

  // Initialize the update query
  $fieldsUpdated = [];
  $updateQuery = "UPDATE photos SET ";

  // Check if description is updated
  if ($description != $currentDescription) {
    $updateQuery .= "description = :description, ";
    $fieldsUpdated[] = "Description";
  }

  // Check if title is updated
  if ($title != $currentTitle) {
    $updateQuery .= "title = :title, ";
    $fieldsUpdated[] = "Title";
  }

  // If a new file is uploaded, update the photo name
  if ($fileName) {
    // Get file extension
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Generate a unique ID for the image name
    $uniqueID = sha1(md5(rand(1, 9999999)));

    // Combine the unique ID with the file extension to create a new image name
    $imageName = $uniqueID . "." . $fileExtension;

    // Move the uploaded file to the desired directory
    $folder = "uploads/" . $imageName;

    if (move_uploaded_file($tempFileName, $folder)) {
      $updateQuery .= "photo_name = :photo_name, ";
      $fieldsUpdated[] = "File"; // Track file change
    }
  }

  // If fields were updated, remove the trailing comma and space
  if (!empty($fieldsUpdated)) {
    $updateQuery = rtrim($updateQuery, ', '); // Remove the last comma

    // Add the WHERE clause to the query
    $updateQuery .= " WHERE photo_id = :photo_id";

    // Prepare the query
    $stmt = $pdo->prepare($updateQuery);

    // Bind parameters for updated fields
    if (in_array('Description', $fieldsUpdated)) {
      $stmt->bindParam(':description', $description);
    }
    if (in_array('Title', $fieldsUpdated)) {
      $stmt->bindParam(':title', $title);
    }
    if (in_array('File', $fieldsUpdated)) {
      $stmt->bindParam(':photo_name', $imageName);
    }

    $stmt->bindParam(':photo_id', $photoID);

    try {
      $stmt->execute();

      // Redirect after successful update
      header("Location: index.php");
      exit();
    } catch (PDOException $e) {
      echo "Error updating photo details: " . $e->getMessage();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Photo</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 flex justify-center items-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Edit Photo</h2>

    <form action="" method="POST" enctype="multipart/form-data">
      <!-- Title field -->
      <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-800">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($currentTitle); ?>"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
          required>
      </div>

      <!-- Description field -->
      <div class="mb-6">
        <label for="description" class="block text-sm font-medium text-gray-800">Description:</label>
        <textarea name="description" id="description" rows="4"
          class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
          required><?php echo htmlspecialchars($currentDescription); ?></textarea>
      </div>

      <!-- Photo upload field -->
      <div class="mb-6">
        <label for="photo" class="block text-sm font-medium text-gray-800">Choose New Image (Optional):</label>
        <input type="file" name="photo" id="photo" accept="image/*"
          class="w-full text-gray-500 bg-gray-100 file:border-0 file:py-2 file:px-4 file:mr-4 file:bg-teal-600 file:hover:bg-teal-500 file:text-white rounded-md cursor-pointer">
      </div>

      <div class="text-center mt-6">
        <input type="submit" name="submit" value="Update Moment"
          class="w-full py-2 px-4 bg-teal-600 text-white font-semibold rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
      </div>
    </form>
  </div>

</body>

</html>