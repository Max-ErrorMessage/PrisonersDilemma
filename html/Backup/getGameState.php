<?php
header('Content-Type: application/json');

function getGameState() {
    $gameState = json_decode(file_get_contents('game_state.json'), true);
    if ($gameState !== null) {
      return json_encode($gameState);
    } else {
      return null;
    }
}
echo getGameState()
?>

