#!/bin/bash

while true; do
    
    /usr/bin/php /var/www/Mini_Games/Prisoners_Dilemma/update_scores.php &>/dev/null
    /usr/bin/php /var/www/Mini_Games/RPS/update_scores.php &>/dev/null
    
    echo "Scores updated at $(date)"
    
    sleep 60
done
