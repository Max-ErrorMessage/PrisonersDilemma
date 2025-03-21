#!/bin/bash

# Updates the scores for the multiplayer games.
# Initially, this was done with a Cron Job but this was ineffective when computation times increased.
# This way, the leaderboard is always getting updated as quickly as it can be
# 60 seconds sleep time between leaderboard updates to decrease the strain on the server's CPU

# Author: Max Worby

while true; do
    
    /usr/bin/php /var/www/Mini_Games/Prisoners_Dilemma/update_scores.php &>/dev/null
    /usr/bin/php /var/www/Mini_Games/RPS/update_scores.php &>/dev/null
    
    echo "Scores updated at $(date)"
    
    sleep 60
done
