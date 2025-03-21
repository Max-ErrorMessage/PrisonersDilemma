#!/bin/bash

# Continuously trains Merlin.

# Author: Max Worby

while true; do
    
    python3 /var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/train_merlin.py 100000 &>/dev/null
    
    echo "Trained merlin at $(date)"

    sleep 10
done
