<?php
require_once 'core/dbConfig.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if not logged in
  header("Location: login.php");
  exit();
}

// Query to get all the photos from the database
$query = "SELECT * FROM photos";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch the result
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 max-w-7xl mx-auto pt-5">
  <!-- Nav -->
  <div class="flex justify-end mb-10">
    <div class="flex space-x-4">
      <!-- Add Photo Button -->
      <a href="add.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">Add
        Photo</a>
      <!-- Logout Button -->
      <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Logout</a>
    </div>
  </div>

  <!-- Photo Gallery -->
  <div class="flex justify-center">
    <?php if (empty($result)) { ?>
      <h2 class="text-xl text-center text-gray-600">No photos uploaded yet! </h2>
    <?php } else { ?>
      <div>
        <?php foreach ($result as $row) { ?>
          <div class="bg-white rounded-lg shadow hover:shadow-lg transition mb-4">
            <!-- Make the image wider by using w-full and adjusting the height -->
            <img src="uploads/<?php echo htmlspecialchars($row['photo_name']); ?>" alt="Photo"
              class="w-full h-[500px] object-cover rounded-t-lg"> <!-- Adjust height as needed -->
            <div class="p-4">
              <h3 class="font-semibold text-xl text-gray-800 truncate"><?php echo htmlspecialchars($row['title']); ?></h3>
              <p class="text-lg text-gray-600 truncate"><?php echo htmlspecialchars($row['description']); ?></p>
              <div class=" flex space-x-4">
                <!-- Edit Button -->
                <a href="edit.php?id=<?php echo $row['photo_id']; ?>"
                  class="text-blue-500 hover:text-blue-700 text-md">Edit</a>
                <!-- Delete Button -->
                <a href="delete.php?id=<?php echo $row['photo_id']; ?>"
                  class="text-red-500 hover:text-red-700 text-md">Delete</a>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>


</body>

</html>