#!/bin/bash

while true; do
    
    python3 /var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/train_merlin.py &>/dev/null
    
    echo "Trained merlin at $(date)"
done
