<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f9fafb;
        }

        .layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            height: 100vh;
        }

        .sidebar {
            background: #111827;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #9ca3af;
            padding: 10px;
            text-decoration: none;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: white;
        }

        .main {
            padding: 24px;
            overflow-y: auto;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        tr:hover {
            background: #f3f4f6;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>Admin</h2>
        <a href="#">Dashboard</a>
        <a href="#">Characters</a>
        <a href="#">Cards</a>
        <a href="#">Abilities</a>
    </div>

    <div class="main">

        <h1>Dashboard</h1>

        <!-- DATABASE VIEW -->
        <div class="card">
            <h2>Characters</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Attack</th>
                    <th>Icnome</th>
                    <th>HP</th>
                </tr>

                <?php
                $conn = new mysqli("localhost", "JameGame", "SuPeRsTr0nGpAs5w0rD|", "JameGame");
                $result = $conn->query("SELECT * FROM Characters");

                while($row = $result->fetch_assoc()) {
                    echo "<tr>\n";
                    echo "<td>" . $row['chr_id'] . "</td>\n";
                    echo "<td>" . $row['name'] . "</td>\n";
                    echo "<td>" . $row['attack'] . "</td>\n";
                    echo "<td>" . $row['income'] . "</td>\n";
                    echo "<td>" . $row['hp'] . "</td>\n";
                    echo "</tr>\n";
                }
                ?>

            </table>
        </div>

        <!-- INSERT FORM -->
        <div class="card">
            <h2>Add Character</h2>

            <form method="POST">
                <label>Name</label>
                <input type="text" name="char_name" required>

                <button type="submit" name="add_char">Add Character</button>
            </form>

            <?php
            if(isset($_POST['add_char'])) {
                $name = $_POST['char_name'];
                $conn->query("INSERT INTO characters (name) VALUES ('$name')");
                echo "<p>Character added!</p>";
            }
            ?>
        </div>

        <!-- CUSTOM QUERY AREA -->
        <div class="card">
            <h2>Custom Query</h2>

            <form method="POST">
                <label>SQL Query</label>
                <input type="text" name="query" placeholder="INSERT / UPDATE / SELECT...">

                <button type="submit" name="run_query">Run</button>
            </form>

            <?php
            if(isset($_POST['run_query'])) {
                $query = $_POST['query'];
                $result = $conn->query($query);

                if($result === TRUE) {
                    echo "<p>Query executed successfully.</p>";
                } else if($result) {
                    echo "<table>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach($row as $val) {
                            echo "<td>$val</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Error: " . $conn->error . "</p>";
                }
            }
            ?>
        </div>

    </div>

</div>

</body>
</html>
