<?php
$arrival = $_POST['arrival'] ?? [];
$burst = $_POST['burst'] ?? [];
$priority = $_POST['priority'] ?? [];
$process_count = $_POST['process_count'] ?? 0;

$processes = [];
for ($i = 0; $i < $process_count; $i++) {
    $processes[] = [
        'index' => $i,
        'id' => 'P' . ($i + 1),
        'arrival' => (int)$arrival[$i],
        'burst' => (int)$burst[$i],
        'priority' => (int)$priority[$i],
        'done' => false
    ];
}

$time = 0;
$gantt = [];
$total_waiting = 0;
$total_turnaround = 0;
$total_response = 0;
$completed = 0;

while ($completed < $process_count) {
    
    $available = array_filter($processes, function ($p) use ($time) {
        return !$p['done'] && $p['arrival'] <= $time;
    });

    if (empty($available)) {
        $time++;
        continue;
    }

   
    usort($available, function ($a, $b) {
        return ($a['priority'] === $b['priority'])
            ? $a['arrival'] <=> $b['arrival']
            : $a['priority'] <=> $b['priority'];
    });

   
    $selected = $available[0];
    $index = $selected['index'];

    $processes[$index]['start'] = $time;
    $processes[$index]['completion'] = $time + $selected['burst'];
    $processes[$index]['response'] = $time -$selected['arrival'] ;  
    $processes[$index]['turnaround'] = $processes[$index]['completion'] - $selected['arrival'];  
    $processes[$index]['waiting'] = $processes[$index]['turnaround']-  $selected['burst'];  // turnaround - burst
    $processes[$index]['done'] = true; 
    

    $gantt[] = [
        'id' => $selected['id'],
        'start' => $time,
        'end' => $processes[$index]['completion']
    ];

    $total_waiting += $processes[$index]['waiting'];
    $total_turnaround += $processes[$index]['turnaround'];
    $total_response += $processes[$index]['response'];
    $time = $processes[$index]['completion'];
    $completed++;
}
unset($p);

$avg_waiting = $total_waiting / $process_count;
$avg_turnaround = $total_turnaround / $process_count;
$avg_response = $total_response / $process_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Priority Scheduling - Result</title>
  <link rel="stylesheet" href="style.css?v=1">
  <style>
    
h2 {
  color: #003d6f;
  text-align: center;
  margin-bottom: 25px;
  font-size: 1.8em;
  letter-spacing: 1px;
}

table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  margin-bottom: 30px;
}

th {
  background-color: #003d6f;
  color: white;
  padding: 12px;
  font-weight: bold;
  text-align: center;
  border: 1px solid #ffffff;
}

td {
  background-color: #f8fbfd;
  padding: 12px;
  border: 1px solid #ccc;
  text-align: center;
  transition: background-color 0.3s ease;
}

tr:hover td {
  background-color: #e0efff;
}

tr:first-child th:first-child {
  border-top-left-radius: 12px;
}
tr:first-child th:last-child {
  border-top-right-radius: 12px;
}
tr:last-child td:first-child {
  border-bottom-left-radius: 12px;
}
tr:last-child td:last-child {
  border-bottom-right-radius: 12px;
}

h3 {
  text-align: center;
  font-size: 1.2em;
  margin-top: 10px;
  color: #003d6f;
}

@keyframes fadeIn {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes slideInUp {
  0% { transform: translateY(30px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

  </style>
</head>
<body class="home2">
  <div class="container2">
    <h2>Gantt Chart</h2>
    <div class="gantt">
      <?php foreach ($gantt as $g): ?>
        <div class="gantt-block">
          <strong><?= $g['id'] ?></strong>
          <div class="bar" style="width: <?= ($g['end'] - $g['start']) *40  ?>px">
            <?= $g['start'] ?> → <?= $g['end'] ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <h2>Process Table</h2>
    <table>
      <tr>
        <th>Process</th>
        <th>Arrival</th>
        <th>Burst</th>
        <th>Priority</th>
        <th>Start</th>
        <th>Waiting</th>
        <th>Turnaround</th>
        <th>Response</th>
      </tr>
      <?php foreach ($processes as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['id']) ?></td>
          <td><?= $p['arrival'] ?></td>
          <td><?= $p['burst'] ?></td>
          <td><?= $p['priority'] ?></td>
          <td><?= $p['start'] ?></td>
          <td><?= $p['waiting'] ?></td>
          <td><?= $p['turnaround'] ?></td>
          <td><?= $p['response'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

  <h3>
  Average Turnaround Time = <?= round($avg_turnaround, 2) ?>

</h3>

<h3>
  Average Waiting Time = <?= round($avg_waiting, 2) ?>
</h3>

<h3>
  Average Response Time = <?= round($avg_response, 2) ?>
</h3>

  </div>
<button id="openModal" class="fancy-btn">📘 Show Formulas</button>

<div id="formulaModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Scheduling Formulas</h2>
    <ul>
      <li><strong>Waiting Time</strong> = Turnaround Time − Burst Time</li>
      <li><strong>Response Time</strong> = Process Gets CPU − Arrival Time</li>
      <li><strong>Turnaround Time</strong> = Completion Time − Arrival Time</li>
      <li><strong>Or</strong> Turnaround Time = Burst Time + Waiting Time</li>
    </ul>
  </div>
</div>

<style>
.fancy-btn {
  background: linear-gradient(45deg,rgb(2, 52, 93),rgb(71, 125, 201));
  color: white;
  padding: 12px 25px;
  font-size: 18px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 0 10px rgba(255,165,0,0.6);
  transition: all 0.3s ease;
}
.fancy-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(0, 145, 255, 0.8);
}

.modal {
  display: none;
  position: fixed;
  z-index: 999;
  padding-top: 100px;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
}

.modal-content {
  background: #1e2b34;
  color:rgb(255, 255, 255);
  margin: auto;
  padding: 30px;
  border: 2px solid rgba(0, 73, 128, 0.8);
  width: 60%;
  border-radius: 20px;
  box-shadow: 0 0 20px rgba(0, 145, 255, 0.8);
  font-family: 'Segoe UI', sans-serif;
}
.modal-content h2 {
  margin-top: 0;
  color:rgb(255, 255, 255);
}
.modal-content ul {
  list-style: square;
  padding-left: 20px;
}
.modal-content li {
  margin-bottom: 10px;
  font-size: 17px;
}

.close {
  color: #fff;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}
.close:hover {
  color:rgb(51, 170, 255);
}
</style>

<script>
document.getElementById("openModal").onclick = function () {
  document.getElementById("formulaModal").style.display = "block";
};

document.querySelector(".close").onclick = function () {
  document.getElementById("formulaModal").style.display = "none";
};

window.onclick = function (event) {
  const modal = document.getElementById("formulaModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
</script>

</body>
</html>
