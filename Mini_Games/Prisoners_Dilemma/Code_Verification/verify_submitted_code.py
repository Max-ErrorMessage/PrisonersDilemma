import re
import sys
import random
import json

user_id = sys.argv[1]

with open(f"/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_{user_id}.txt", 'r') as file:
    code = file.read()


keywords = ["print", "import", "exec", "eval", "open", "execfile", "compile", "input"]
pattern = r"\b(" + "|".join(keywords) + r")\b(?!\s*\()"

if re.search(pattern, code):
    print("Code Error: unsafe functions detected")
    quit()

file_code = f"import random\n\ndef user_{user_id}(self_decisions, opponent_decisions):\n"

for line in code.splitlines():
    file_code += f"    {line}"

namespace = {}

try:
    exec(file_code, namespace)
except SyntaxError as se:
    print(f"There is a syntax error somewhere in your code")
    quit()

user_function = namespace[f"user_{user_id}"]


def happy(self_decisions, opponent_decisions):
    """
    Always trusts (happily)
    """
    return True


def grumpy(self_decisions, opponent_decisions):
    """
    Always betrays (Grumpily)
    """
    return False


def bashful(self_decisions, opponent_decisions):
    """
    Does the opposite of what the opponent just did (Bashful is avoiding confrontation)
    """
    if len(self_decisions) > 0:
        return not opponent_decisions[-1]
    else:
        return False


def doc(self_decisions, opponent_decisions):
    """
    Copies what the opponent just did (Doc is sensible and fair)
    """
    if len(self_decisions) > 0:
        return opponent_decisions[-1]
    else:
        return True


def sneezy(self_decisions, opponent_decisions):
    """
    Randomly betrays 1/10 times (he had a sneeze)
    """
    if random.randint(0, 9) == 4:
        return False
    else:
        return True


def dopey(self_decisions, opponent_decisions):
    """
    Makes a random decision (dopey is a little silly)
    """
    return random.choice([True, False])


def sleepy(self_decisions, opponent_decisions):
    """
    Copies the opponent with a "delay" of one move (sleepy has a slow reaction time)
    """
    if len(self_decisions) > 1:
        return opponent_decisions[-2]
    else:
        return True


players = ["Happy", "Grumpy", "Bashful", "Doc", "Sneezy", "Dopey", "Sleepy", "Your Code"]
functions = [happy, grumpy, bashful, doc, sneezy, dopey, sleepy, user_function]
scores = [0, 0, 0, 0, 0, 0, 0, 0, 0]

for index, player1 in enumerate(players):
    for jindex, player2 in enumerate(players):
        if index >= jindex:
            continue

        player_1_score, player_2_score = 0, 0
        player_1_decisions, player_2_decisions = [], []
        player_1_function, player_2_function = functions[index], functions[jindex]

        for _ in range(200):
            try:
                player_1_decision = player_1_function(player_1_decisions, player_2_decisions)
                player_2_decision = player_2_function(player_2_decisions, player_1_decisions)
            except Exception as e:
                print(f"Your code has caused an error: {e}")
                quit()

            if player_1_decision and player_2_decision:  # Both players chose to trust
                player_1_score += 5
                player_2_score += 5
            elif player_1_decision and not player_2_decision:  # Player 2 betrayed player 1
                player_1_score -= 1
                player_2_score += 10
            elif not player_1_decision and player_2_decision:  # Player 1 betrayed player 2
                player_1_score += 10
                player_2_score -= 1

            # Otherwise, both players betrayed one another and nothing changes.

            player_1_decisions.append(player_1_decision)
            player_2_decisions.append(player_2_decision)

        scores[index] += player_1_score
        scores[jindex] += player_2_score


scores_dict = {player: score for player, score in zip(players, scores)}

with open(f"/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/dwarf_scores_{user_id}.json", "w") as json_file:
    json.dump(scores_dict, json_file, indent=4)

print("1")

