<?php

$output = exec("python3 hello_world.py 2>&1");

echo "Output:" . $output . "</p>";

$testing = exec("whereis python");

echo "Testing: " . $testing;

?>