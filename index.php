<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Priority Scheduling</title>
  <link rel="stylesheet" href="style.css?v=1">
  <style>
    input[type="number"] {
      padding: 12px;
      width: 80%;
      max-width: 300px;
      font-size: 16px;
      border: 2px solid #007acc;
      border-radius: 8px;
      outline: none;
      transition: 0.3s;
    }

    input[type="number"]:focus {
      border-color: #005999;
      background-color: #f0faff;
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="home">
  <div class="container1">
    <h2 class="header1">Enter Number of Processes</h2>
    <form action="input.php" method="POST">
      <input type="number" name="process_count" min="1" required>
      <br>
      <button type="submit" class="btn1">Next</button>
    </form>
  </div>
</body>
</html>
