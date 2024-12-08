<?php
require_once 'core/dbConfig.php'; // Include dbConfig to access $pdo

// Check if the photo ID is passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $photo_id = $_GET['id'];

  try {
    // Fetch the photo details (name, title, description) from the database
    $query = "SELECT * FROM photos WHERE photo_id = :photo_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->execute();

    $photo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($photo) {
      // If the form is submitted for deletion confirmation
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
        // Delete the photo from the database
        $deleteQuery = "DELETE FROM photos WHERE photo_id = :photo_id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
          // Delete the physical file from the server
          $filePath = 'uploads/' . $photo['photo_name'];
          if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
          }

          // Redirect after successful deletion
          header("Location: index.php");
          exit();
        } else {
          $error = "Error deleting photo from the database.";
        }
      }
    } else {
      $error = "Photo not found.";
    }
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
} else {
  $error = "Invalid photo ID.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Photo Confirmation</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 flex justify-center items-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
    <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Confirm Deletion</h2>

    <?php if (isset($error)): ?>
      <div class="bg-red-200 text-red-800 p-3 rounded-md mb-6"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($photo): ?>
      <p class="text-center text-lg font-medium text-gray-800 mb-6">Are you sure you want to delete this photo?</p>
      <div class="mb-6">
        <img src="uploads/<?php echo $photo['photo_name']; ?>" alt="Photo"
          class="mx-auto w-full h-auto max-h-48 object-cover rounded-md mb-4">
      </div>
      <p class="text-center text-gray-600 mb-4"><strong>Title:</strong> <?php echo $photo['title']; ?></p>
      <p class="text-center text-gray-600 mb-6"><strong>Description:</strong> <?php echo $photo['description']; ?></p>

      <!-- Deletion Form -->
      <form method="POST">
        <div class="flex justify-center gap-6">
          <button type="submit" name="confirm_delete"
            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            Yes, Delete
          </button>
          <a href="index.php"
            class="px-6 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500">
            No, Cancel
          </a>
        </div>
      </form>
    <?php endif; ?>

  </div>

</body>

</html>