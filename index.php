<?php
function generateMaze($rows, $cols)
{
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

$rows = 8;
$cols = 8;
$maze = generateMaze($rows, $cols);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Meilleur Labyrinthe</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        h1 {
            margin: 20px;
        }

        #maze-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #maze {
            display: grid;
            grid-template-columns: repeat(<?php echo $cols; ?>, 40px);
            gap: 2px;
            margin-bottom: 20px;
        }

        .tile {
            width: 40px;
            height: 40px;
            border: 1px solid #ccc;
        }

        .wall {
            background-color: #FF914D;
        }

        .path {
            background-color: #ccc;
        }

        .cat {
            background-image: url('./assets/img/cat.png');
            background-size: cover;
        }

        .mouse {
            background-image: url('./assets/img/mouse.png');
            background-size: cover;
        }

        .hidden {
            background-color: #aaa;
        }

        .controls {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .button-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin: 5px;
        }

        .button-container.vertical {
            flex-direction: column;
        }

        button {
            margin: 5px;
            padding: 10px;
            font-size: 16px;
            background-color: #FFA500;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #FF8C00;
        }

        #restart {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        #restart:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>The Ultimate F*cking Greatest Maze</h1>
    <div id="maze-container">
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
        <div class="controls">
            <div class="button-container vertical">
                <button onclick="move('up')">↑</button>
            </div>
            <div class="button-container">
                <button onclick="move('left')">←</button>
                <button onclick="move('down')">↓</button>
                <button onclick="move('right')">→</button>
            </div>
        </div>
    </div>
    <button id="restart" onclick="location.reload()">Recommencer</button>

    <script>
        let maze = <?php echo json_encode($maze); ?>;
        let catPos = { x: 0, y: 0 };
        let mousePos = { x: maze.length - 1, y: maze[0].length - 1 };

        function isVisible(x, y) {
            return Math.abs(x - catPos.x) <= 1 && Math.abs(y - catPos.y) <= 1;
        }

        function updateVisibility() {
            document.querySelectorAll('.tile').forEach(tile => {
                const row = tile.getAttribute('data-row');
                const col = tile.getAttribute('data-col');
                if (isVisible(parseInt(row), parseInt(col))) {
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
                catPos = { x: newX, y: newY };
                document.querySelector(`[data-row='${catPos.x}'][data-col='${catPos.y}']`).classList.add('cat');
                updateVisibility();

                if (catPos.x === mousePos.x && catPos.y === mousePos.y) {
                    document.querySelector('#restart').insertAdjacentHTML('beforebegin', '<p>Gagné !</p>');
                }
            }
        }

        function moveMouse() {
            const directions = ['up', 'down', 'left', 'right'];
            let moved = false;
            while (!moved) {
                const direction = directions[Math.floor(Math.random() * directions.length)];
                let newX = mousePos.x;
                let newY = mousePos.y;
                if (direction === 'up' && mousePos.x > 0) newX--;
                if (direction === 'down' && mousePos.x < maze.length - 1) newX++;
                if (direction === 'left' && mousePos.y > 0) newY--;
                if (direction === 'right' && mousePos.y < maze[0].length - 1) newY++;

                if (maze[newX][newY] !== 1 && !(newX === catPos.x && newY === catPos.y)) {
                    document.querySelector(`[data-row='${mousePos.x}'][data-col='${mousePos.y}']`).classList.remove('mouse');
                    mousePos = { x: newX, y: newY };
                    document.querySelector(`[data-row='${mousePos.x}'][data-col='${mousePos.y}']`).classList.add('mouse');
                    moved = true;
                }
            }
        }

        function gameLoop() {
            moveMouse();
            updateVisibility();
            if (catPos.x === mousePos.x && catPos.y === mousePos.y) {
                document.querySelector('#restart').insertAdjacentHTML('beforebegin', '<p>Gagné !</p>');
                return;
            }
            setTimeout(gameLoop, 500); // Move the mouse every 500ms
        }

        updateVisibility();
        gameLoop();

        document.addEventListener('keydown', function(event) {
            const keyName = event.key;
            if (keyName === 'ArrowUp') {
                move('up');
            } else if (keyName === 'ArrowDown') {
                move('down');
            } else if (keyName === 'ArrowLeft') {
                move('left');
            } else if (keyName === 'ArrowRight') {
                move('right');
            }
        });
    </script>
</body>
</html>
