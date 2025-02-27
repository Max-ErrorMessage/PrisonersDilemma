<?php
$newFen = $_POST['newFen'];
$newTurn = $_POST['newTurn'];
$immortalRow = $_POST['immortalRow'];
$immortalCol = $_POST['immortalCol'];
$enpassantRow = $_POST['enpassantRow'];
$enpassantCol = $_POST['enpassantCol'];
$highlightRow = $_POST['highlightRow'];
$highlightCol = $_POST['highlightCol'];

function updateGameState($newFen, $newTurn, $immortalRow, $immortalCol,$enpassantRow,$enpassantCol,$highlightRow , $highlightCol) {
  // Read the current game state from the file
  $gameState = json_decode(file_get_contents('game_state.json'), true);
  
  // Update the game state with the new data
  $gameState['boardFen'] = $newFen;
  $gameState['playerTurn'] = $newTurn;
  $gameState['immortalRow'] = $immortalRow;
  $gameState['immortalCol'] = $immortalCol;
  $gameState['enpassantRow'] = $enpassantRow;
  $gameState['enpassantCol'] = $enpassantCol;
  $gameState['highlightRow'] = $highlightRow;
  $gameState['highlightCol'] = $highlightCol;
  
  // Write the updated game state back to the file
  file_put_contents('game_state.json', json_encode($gameState));
}

updateGameState($newFen,$newTurn,$immortalRow,$immortalCol,$enpassantRow,$enpassantCol,$highlightRow , $highlightCol);
?>