<?php
$process_count = $_POST['process_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Input Data</title>
  <link rel="stylesheet" href="style.css?v=2">
</head>
<body>
  <div class="container">
    <h2>Enter Process Details</h2>
    <form action="result.php" method="POST">
      <input type="hidden" name="process_count" value="<?= $process_count ?>">
      <table>
        <tr>
          <th>Process</th>
          <th>Arrival Time</th>
          <th>Burst Time</th>
          <th>Priority</th>
        </tr>
        <?php for ($i = 0; $i < $process_count; $i++): ?>
          <tr>
  <td>P<?= $i + 1 ?></td>
  <td><input type="number" name="arrival[]" min="0" required oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
  <td><input type="number" name="burst[]" min="0" required oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
  <td><input type="number" name="priority[]" min="0" required oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
</tr>

        <?php endfor; ?>
      </table>
      <button type="submit">Calculate</button>
    </form>
  </div>
</body> 

</html>


