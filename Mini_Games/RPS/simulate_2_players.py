"""
This file access the user_codes.py file to access functions that run user code.
It takes in 2 User IDs and a game length as arguments.
It reads the current scores from scores.json and updates the scores to reflect the changes that the game played had on the scores of the 2 users
THis is run from update_scores.php and is only ever run with a timeout of 1.
If the code fails it will execute prematurely and no scores will be changed.

Based on the simulate_2_players file in the Prisoner's Dilemma directory.

Author: James Aris
"""

import sys
import importlib.util
import json

player_1 = sys.argv[1]
player_2 = sys.argv[2]
rounds = int(sys.argv[3])
# Gets all the relevant arguments provided from update_scores.php
print(sys.argv)

module_path = '/var/www/Mini_Games/RPS/Computer_Generated_Files/user_codes.py'
spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

# Since the user_codes.py and the merlin bot file is in a different directory (and this file is being run indirectly
# from update_scores.php), the dictionary that stores all the user IDs and their equivalent functions has to be accessed
# via the importlib library.

player_1_function, player_2_function = user_codes[player_1], user_codes[player_2]

scores = {player_1: 0, player_2: 0}

player_1_decisions, player_2_decisions = [], []

wins = {"Rock":"Scissors","Paper":"Rock","Scissors":"Paper"}
for i in range(rounds):
    player_1_decision = player_1_function(player_1_decisions, player_2_decisions, player_1_decisions, player_2_decisions, len(player_1_decisions))
    player_2_decision = player_2_function(player_2_decisions, player_1_decisions, player_2_decisions, player_1_decisions, len(player_1_decisions))
    # Some parameters are provided twice because the template of the parameter is:
    # function(self_decisions, opponent_decisions, s, o, n)
    # The reason that the parameters are in this format is for ease of use for the user: writing self_decisions becomes
    # lengthy even though all the information is technically provided with just self_decisions and opponent_decisions
    try:
        if wins[player_1_decision] == player_2_decision:
            # Player 1 wins
            scores[player_1] += 10
            scores[player_2] -= 0
        elif player_1_decision == wins[player_2_decision]:
            # Player 2 wins
            scores[player_1] -= 0
            scores[player_2] += 10
        else:
            #its a tie
            scores[player_1] += 5
            scores[player_2] += 5
    except KeyError:
        pass
        # Sometimes people return thigns they arent supposed to and if that happens then it's a drwa
        # Later I will implement a strategy that punishes that player but for now this is fine.

    # If none of these things happened then tied and scores remain the same

    player_1_decisions.append(player_1_decision)
    player_2_decisions.append(player_2_decision)

with open('/var/www/Mini_Games/RPS/Computer_Generated_Files/scores.json', 'r') as file:
    existing_scores = json.load(file)

existing_scores[player_1] = existing_scores[player_1] + scores[player_1]
existing_scores[player_2] = existing_scores[player_2] + scores[player_2]

with open('/var/www/Mini_Games/RPS/Computer_Generated_Files/scores.json', 'w') as file:
    json.dump(existing_scores, file, indent=4)
    # Update the scores.json file with the new scores
