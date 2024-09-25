<?php
function generateMaze($rows, $cols) {
    $maze = [];
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            $maze[$i][$j] = rand(0, 1) > 0.7 ? 1 : 0; // 1 pour mur, 0 pour chemin
        }
    }
    $maze[0][0] = 2; // Position initiale du chat
    $maze[$rows - 1][$cols - 1] = 3; // Position de la souris
    return $maze;
}

$rows = 20;
$cols = 20;
$maze = generateMaze($rows, $cols);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labyrinthe du Chat</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #maze {
            display: grid;
            grid-template-columns: repeat(<?php echo $cols; ?>, 20px);
            gap: 2px;
        }
        .tile {
            width: 20px;
            height: 20px;
        }
        .wall {
            background-color: black;
        }
        .path {
            background-color: white;
        }
        .cat {
            background-color: blue;
        }
        .mouse {
            background-color: red;
        }
        .hidden {
            background-color: grey;
        }
    </style>
</head>
<body>
    <div id="maze">
        <?php
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $class = "path";
                if ($maze[$i][$j] == 1) $class = "wall";
                elseif ($maze[$i][$j] == 2) $class = "cat";
                elseif ($maze[$i][$j] == 3) $class = "mouse";
                echo "<div class='tile $class' data-row='$i' data-col='$j'></div>";
            }
        }
        ?>
    </div>
    <button onclick="move('up')">Up</button>
    <button onclick="move('down')">Down</button>
    <button onclick="move('left')">Left</button>
    <button onclick="move('right')">Right</button>

    <script>
        let maze = <?php echo json_encode($maze); ?>;
        let catPos = {x: 0, y: 0};

        function isVisible(x, y) {
            return Math.abs(x - catPos.x) <= 1 && Math.abs(y - catPos.y) <= 1;
        }

        function updateVisibility() {
            document.querySelectorAll('.tile').forEach(tile => {
                const row = tile.getAttribute('data-row');
                const col = tile.getAttribute('data-col');
                if (isVisible(row, col)) {
                    tile.classList.remove('hidden');
                } else {
                    tile.classList.add('hidden');
                }
            });
        }

        function move(direction) {
            let newX = catPos.x;
            let newY = catPos.y;
            if (direction === 'up' && catPos.x > 0) newX--;
            if (direction === 'down' && catPos.x < maze.length - 1) newX++;
            if (direction === 'left' && catPos.y > 0) newY--;
            if (direction === 'right' && catPos.y < maze[0].length - 1) newY++;
            
            if (maze[newX][newY] !== 1) { // Check for walls
                document.querySelector(`[data-row='${catPos.x}'][data-col='${catPos.y}']`).classList.remove('cat');
                catPos = {x: newX, y: newY};
                document.querySelector(`[data-row='${catPos.x}'][data-col='${catPos.y}']`).classList.add('cat');
                updateVisibility();
                
                if (maze[catPos.x][catPos.y] === 3) {
                    alert("Victoire ! Le chat a trouvé la souris.");
                }
            }
        }

        updateVisibility();
    </script>
</body>
</html>
