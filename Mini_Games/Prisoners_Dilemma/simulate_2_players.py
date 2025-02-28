import sys
import importlib.util
import json

player_1 = sys.argv[1]
player_2 = sys.argv[2]
rounds = int(sys.argv[3])


module_path = "/var/www/twokie.com/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

player_1_function, player_2_function = user_codes[player_1], user_codes[player_2]

scores = {player_1: 0, player_2: 0}

player_1_decisions, player_2_decisions = [], []


for i in range(rounds):
    player_1_decision = player_1_function(player_1_decisions, player_2_decisions)
    player_2_decision = player_2_function(player_2_decisions, player_1_decisions)

    if player_1_decision and player_2_decision:  # Both players choose to Trust; they both gain 5 points
        scores[player_1] += 5
        scores[player_2] += 5
    elif player_1_decision and not player_2_decision:  # Player 1 betrayed player 2
        scores[player_1] -= 1
        scores[player_2] += 10
    elif not player_1_decision and player_2_decision:  # Player 2 betrayed player 1
        scores[player_1] += 10
        scores[player_2] -= 1

    # If none of these things happened then they both betrayed one another and scores remain the same

    player_1_decisions.append(player_1_decision)
    player_2_decisions.append(player_2_decision)

with open('/var/www/twokie.com/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/scores.json', 'r') as file:
    existing_scores = json.load(file)

with open('/var/www/twokie.com/html/Testing/debug_scores.json', "w") as debug_file:
    json.dump(existing_scores, debug_file, indent=4)

existing_scores[player_1], existing_scores[player_2] = existing_scores[player_1] + scores[player_1], existing_scores[player_2] + scores[player_2]

with open('/var/www/twokie.com/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/scores.json', 'w') as file:
    json.dump(existing_scores, file, indent=4)