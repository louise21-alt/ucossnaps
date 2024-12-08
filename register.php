<?php
require_once 'core/dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect and sanitize user input
  $username = htmlspecialchars($_POST['username']);
  $first_name = htmlspecialchars($_POST['first_name']);
  $last_name = htmlspecialchars($_POST['last_name']);
  $email = htmlspecialchars($_POST['email']);
  $password = htmlspecialchars($_POST['password']);
  $confirm_password = htmlspecialchars($_POST['confirm_password']);

  // Check if passwords match
  if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to insert the new user
    $query = "INSERT INTO user (username, first_name, last_name, email, password) 
                  VALUES (:username, :first_name, :last_name, :email, :password)";
    $stmt = $pdo->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
      header("Location: login.php"); // Redirect to login page after successful registration
      exit();
    } else {
      $error = "Error: Could not register user.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">
  <div class="w-full max-w-md bg-gradient-to-r from-teal-500 via-teal-600 to-teal-700 rounded-lg shadow-lg p-8">
    <h2 class="text-white text-4xl font-bold text-center mb-8">Create Your Account</h2>
    <form action="register.php" method="POST" class="space-y-6">
      <!-- Error Message -->
      <?php if (isset($error)) {
        echo "<p class='text-red-600'>$error</p>";
      } ?>

      <!-- Username -->
      <div>
        <label for="username" class="text-white text-sm mb-2 block">Username</label>
        <input type="text" name="username" id="username" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Enter username">
      </div>

      <!-- First Name -->
      <div>
        <label for="first_name" class="text-white text-sm mb-2 block">First Name</label>
        <input type="text" name="first_name" id="first_name" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Enter first name">
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="text-white text-sm mb-2 block">Last Name</label>
        <input type="text" name="last_name" id="last_name" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Enter last name">
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="text-white text-sm mb-2 block">Email</label>
        <input type="email" name="email" id="email" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Enter email">
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="text-white text-sm mb-2 block">Password</label>
        <input type="password" name="password" id="password" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Enter password">
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="confirm_password" class="text-white text-sm mb-2 block">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none"
          placeholder="Confirm password">
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit"
          class="w-full py-3 px-4 text-sm font-semibold text-white bg-teal-800 rounded-lg hover:bg-teal-900 focus:ring-2 focus:ring-teal-400 transition-all duration-300">
          Register
        </button>
      </div>

      <!-- Login Link -->
      <p class="text-sm text-white text-center">
        Already have an account?
        <a href="login.php" class="text-teal-300 hover:text-teal-400 font-semibold ml-1">Login here</a>
      </p>
    </form>
  </div>
</body>

</html>