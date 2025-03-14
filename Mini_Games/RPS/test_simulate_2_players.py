"""
This file access the user_codes.py file to access functions that run user code.
It takes in 2 User IDs and a game length as arguments.
It reads the current scores from scores.json and updates the scores to reflect the changes that the game played had on the scores of the 2 users
THis is run from update_scores.php and is only ever run with a timeout of 1.
If the code fails it will execute prematurely and no scores will be changed.
"""

import sys
import importlib.util
import json

player_1 = sys.argv[1]
player_2 = sys.argv[2]
rounds = int(sys.argv[3])
# Gets all the relevant arguments provided from update_scores.php

if '0' in [player_1, player_2]:
    module_path = "/var/www/Mini_Games/RPS/Merlin_Bot/merlin.py"

    spec = importlib.util.spec_from_file_location("merlin", module_path)
    merlin_module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(merlin_module)
    Merlin_Agent = getattr(merlin_module, "AI_Agent", None)

    merlin = Merlin_Agent(epsilon=0)
    merlin.load_model('/var/www/Mini_Games/RPS/Merlin_Bot/merlin.pkl')
    # Gets the merlin bot

module_path = "/var/www/Mini_Games/RPS/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

# Since the user_codes.py and the merlin bot file is in a different directory (and this file is being run indirectly
# from update_scores.php), the dictionary that stores all the user IDs and their equivalent functions has to be accessed
# via the importlib library.

if '0' in [player_1, player_2]:
    user_codes['0'] = merlin.action

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

    if wins[player_1_decision] == player_2_decision:
        # Player 1 wins
        scores[player_1] += 1
        scores[player_2] -= 1
    elif player_1_decision == wins[player_2_decision]:
        # Player 2 wins
        scores[player_1] -= 1
        scores[player_2] += 1

    # If none of these things happened then tied and scores remain the same

    player_1_decisions.append(player_1_decision)
    player_2_decisions.append(player_2_decision)

print(f"Player 1 score: {round(scores[player_1]/rounds, 2)}\nTotal betrayals by Player 1: {len(player_1_decisions) - sum(player_1_decisions)}\nPlayer 2 score: {round(scores[player_2]/rounds, 2)}\nTotal betrayals by player 2: {len(player_2_decisions) - sum(player_2_decisions)}")
