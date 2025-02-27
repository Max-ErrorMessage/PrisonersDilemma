<?php

$output = exec("python3 hello_world.py");

echo "Output:" . $output . "</p>";

$testing = exec("whereis python");

echo "Testing: " . $testing;

?>