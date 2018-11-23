<?php

$grid = array();
$visit = array();
$parent = array();

// Direction Array
$di = array(0, 0, 1, -1, 1, 1, -1, -1);
$dj = array(1, -1, 0, 0, 1, -1, 1, -1);

function boundary($i, $j) {
	return ($i >= 1 && $i <= 16) && ($j >= 1 && $j <= 12);
}

function found($i, $j) {
	return ($i == $_POST['dR']) && ($j == $_POST['dC']);
}

function make_path($i, $j) {
	global $parent, $grid;
	if ($i == $_POST['sR'] && $j == $_POST['sC']) {
		$grid[$i][$j] = 2;
		return;
	} else {
		make_path($parent[$i][$j][0], $parent[$i][$j][1]);
		$grid[$i][$j] = 2;
	}
}

function bfs() {
	global $di, $dj, $visit, $grid, $parent;
	$q = new splQueue();
	$q->push([$_POST['sR'], $_POST['sC']]);
	$visit[$_POST['sR']][$_POST['sC']] = 1;
	while (!$q->isEmpty()) {
		$u = $q->bottom(); // q.front
		$q->dequeue(); // q.pop
		$flag = false;
		for ($k = 0; $k < 4; $k++) { // $k < 8 if diagonal move needed
			$vi = $di[$k] + $u[0];
			$vj = $dj[$k] + $u[1];
			if (boundary($vi, $vj)) {
				if (found($vi, $vj)) {
					$parent[$vi][$vj][0] = $u[0];
					$parent[$vi][$vj][1] = $u[1];
					$flag = true;
					break;
				} else if ($visit[$vi][$vj] == 0) {
					$visit[$vi][$vj] = 1;
					$q->push([$vi, $vj]);
					$parent[$vi][$vj][0] = $u[0];
					$parent[$vi][$vj][1] = $u[1];
				}
			}
		}
		if ($flag) break;
	}
	// Now mark the path so that we can color 
	make_path($_POST['dR'], $_POST['dC']);
}

function print_def() {
	global $grid;
	$room = 1;
	echo '<table align="center" border="1" height="700" width="700">';
	for ($r = 1; $r <= 16; $r++) {
		echo '<tr>';
		for ($c = 1; $c <= 12; $c++) {
			if ($grid[$r][$c] == 1) {
				echo '<td style="background: grey; text-align: center;"><strong>R-' . $room . '</strong><br><small>(' . $r . ', ' . $c . ')</small></td>';
				$room++;
			} else if ($grid[$r][$c] == 3) {
				echo '<td style="text-align: center;"><small><strong>enter</strong><br>(' . $r . ', ' . $c . ')</small></td>';
			} else {
				echo '<td style="text-align: center;"><small>(' . $r . ', ' . $c . ')</small></td>';
			}
		}
		echo '</tr>';
	}
	echo "</table><br><br>";
}

function print_with_path() {
	global $grid;
	$room = 1;
	echo '<table align="center" border="1" height="700" width="700">';
	for ($r = 1; $r <= 16; $r++) {
		echo '<tr>';
		for ($c = 1; $c <= 12; $c++) {
			if ($grid[$r][$c] == 1) {
				echo '<td style="background: grey; text-align: center;"><strong>R-' . $room . '</strong><br><small>(' . $r . ', ' . $c . ')</small></td>';
				$room++;
			} else if ($grid[$r][$c] == 2) {
				if ($r == $_POST['sR'] && $c == $_POST['sC']) {
					echo '<td style="background: red; text-align: center;"><small><strong>start</strong><br>(' . $r . ', ' . $c . ')</small></td>';
				} else if ($r == $_POST['dR'] && $c == $_POST['dC']) {
					echo '<td style="background: red; text-align: center;"><small><strong>stop</strong><br>(' . $r . ', ' . $c . ')</small></td>';
				} else {
					echo '<td style="background: yellow; text-align: center;"><small>(' . $r . ', ' . $c . ')</small></td>';
				}
			} else if ($grid[$r][$c] == 3) {
				echo '<td style="text-align: center;"><small><strong>enter</strong><br>(' . $r . ', ' . $c . ')</small></td>';
			} else {
				echo '<td style="text-align: center;"><small>(' . $r . ', ' . $c . ')</small></td>';
			}
		}
		echo '</tr>';
	}
	echo "</table><br><br>";
}

// Memset 0 to the grid
$x = 0;
for ($r = 1; $r <= 16; $r++) {
	for ($c = 1; $c <= 12; $c++) {
		$grid[$r][$c] = $visit[$r][$c] = 0;
	}
}

// Memset -1 to the parent array
for ($r = 1; $r <= 16; $r++) {
	for ($c = 1; $c <= 12; $c++) {
		for ($k = 0; $k < 2; $k++) {
			$parent[$r][$c][$k] = -1;
		}
	}
}

// Settings: 0 = empty_space | 1 = faculty | 2 = path | 3 = entrance 
for ($r = 1; $r <= 16; $r++) {
	for ($c = 1; $c <= 12; $c++) {
		if (($r == 1 && $c == 6) || ($r == 1 && $c == 7)) { // to add side entry ($r == 9 && $c == 12)
			$grid[$r][$c] = 3;
		} else if ($c == 1) {
			$grid[$r][$c] = $visit[$r][$c] = 1;
		} else if ($c == 12) {
			$grid[$r][$c] = $visit[$r][$c] = 1;
		} else if ($r == 3 || $r == 15) {
			continue;
		} else {
			if (($r == 1 || $r == 2) && ($c == 6 || $c == 7)) {
				continue;
			} else if ($c == 3 || $c == 4 || $c == 6 || $c == 7 || $c == 9 || $c == 10) {
				$grid[$r][$c] = $visit[$r][$c] = 1;
			}
		}
	}
}

if (isset($_POST['GO'])) {
	if (boundary($_POST['sR'], $_POST['sC']) && boundary($_POST['dR'], $_POST['dC'])) {
		bfs();
	} else {
		// alert 
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Faculty</title>
</head>
<body>
	<form action="#" method="POST">
  	<div align="center">
			<label align="center" style="font-size: 25px;">Faculty Office</label><br>
			<small style="font-style: italic;">(Enter the row and column number)</small><br><br>
			<small>START</small><br>
  		<input type="number" min="1" max="16" name="sR" placeholder="R" required="">
			<input type="number" min="1" max="12" name="sC" placeholder="C" required="">
			<br><br>
			<small>STOP</small><br>
			<input type="number" min="1" max="16" name="dR" placeholder="R" required="">
			<input type="number" min="1" max="12" name="dC" placeholder="C" required="">
	    <br>
	    <br>
	    <input type="submit" value="GO" name="GO" />
	    <br><br>
  	</div>
    <?php 
			if (!isset($_POST['GO'])) {
				print_def();
			} else {
				print_with_path();
			}
			?>
  </form> 
</body>
</html>
