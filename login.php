<?php
require_once 'core/dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect and sanitize user input
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);

  // Prepare the SQL query to check the user credentials
  $query = "SELECT * FROM user WHERE username = :username";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':username', $username);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    // If password is correct, start the session and redirect
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    header("Location: index.php"); // Redirect to dashboard
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">
  <div class="w-full max-w-md bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg p-8">
    <h2 class="text-white text-4xl font-bold text-center mb-8">Sign in</h2>
    <form method="POST" class="space-y-6">
      <!-- Username -->
      <div>
        <label for="username" class="text-white text-sm mb-2 block">Username</label>
        <input id="username" name="username" type="text" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          placeholder="Enter your username" />
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="text-white text-sm mb-2 block">Password</label>
        <input id="password" name="password" type="password" required
          class="w-full text-sm text-gray-800 bg-white border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          placeholder="Enter your password" />
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit"
          class="w-full py-3 px-4 text-sm font-semibold text-white bg-blue-800 rounded-lg hover:bg-blue-900 focus:ring-2 focus:ring-blue-400 transition-all duration-300">
          Log In
        </button>
      </div>

      <!-- Register Link -->
      <p class="text-sm text-white text-center">
        Don't have an account?
        <a href="register.php" class="text-white hover:text-slate-700 font-semibold ml-1">Register here</a>
      </p>
    </form>
  </div>
</body>

</html>