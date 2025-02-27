<!DOCTYPE HTML>

<?php
$servername = "127.0.0.1:3306";
$username = "u753770036_DougSantry";
$password = "demorgansL4W?";
$dbname = "u753770036_Chess";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Query to get table names
$table_query = "SHOW TABLES";
$table_result = $conn->query($table_query);

if ($table_result === false) {
    die("Query failed: " . $conn->error);
}

// Iterate over each table and print its columns
while ($table_row = $table_result->fetch_row()) {
    $table_name = $table_row[0];
    echo "Table: $table_name<br>";

    // Query to get column names for the current table
    $column_query = "SHOW COLUMNS FROM $table_name";
    $column_result = $conn->query($column_query);

    if ($column_result === false) {
        die("Query failed: " . $conn->error);
    }

    // Display column names for the current table
    while ($row = $column_result->fetch_assoc()) {
        echo "Column: " . $row['Field'] . "<br>";
    }

    echo "<br>";
}

// Close the connection
$conn->close();
?>


