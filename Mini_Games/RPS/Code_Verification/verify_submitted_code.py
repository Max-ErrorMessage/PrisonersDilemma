"""
This file confirms that a user-submitted code is:
- Safe
- Syntactically correct
- Doesn't create a loop
- Works within the confines of the Prisoner's Dilemma environment.

It tests the code against the 'Seven Dwarves' - 7 different functions that also make choices in the Prisoner's Dilemma
The results are saved in the dwarf_scores.json file.
"""

import re
import sys
import random
import json

user_id = sys.argv[1]

with open(f"/var/www/Mini_Games/RPS/Code_Verification/User_Submitted_Code/user_{user_id}.txt", 'r') as file:
    code = file.read()
    # It's impractical to parse in the code as arguments, especially with new lines and indentation and such.
    # Instead, submission.php (which calls this file) stores the user code in a txt file which this file then reads.

with open("/var/www/Mini_Games/RPS/Code_Verification/User_Submitted_Code/log.txt", "a") as log:
    log.write(f'{code}\nUser ID: {user_id}\n\n')
    # This logs every single submission (even ones rejected because they don't work) to a log file so that we can tell
    # if someone is trying to break/exploit the code, even when this file prevents them from actually succeeding.

if len(code) > 100000:
    print("Your submission is too long")
    quit()

keywords = [
    "print", "import", "exec", "eval", "open", "execfile", "compile", "input", "__import__", "os.system", "os.popen",
    "subprocess.call", "subprocess.run", "globals", "locals", "file", "pickle", "pickle.load", "shlex.split",
    "os.remove", "os.rename", "socket", "quit", "fuck", "shit", "cunt", "dick", "cock", "ivy"
]
# A list of keywords that the file scans for. There are a couple of levels of redundancy (in theory, it is impossible to
# run os-related functions without importing os, but additional security never hurt anyone)
pattern = r"(" + "|".join(keywords) + r")"

if re.search(pattern, code):
    print("Code Error: unsafe functions detected")
    quit()
    # Scans the code for any of the above keywords (including as variable names and within string literals)
    # It's an odd choice for variable names and there should be no reason for Sting Literals within code
    # Either way, the user can read the error message provided and will know what they did
    # If their code includes one of the keywords they probably know what they are doing

file_code = f"import random\n\ndef user_{user_id}(self_decisions, opponent_decisions, s, o, n):\n"

for line in code.splitlines():
    file_code += f"    {line}\n"

namespace = {}

try:
    exec(file_code, namespace)
except SyntaxError as se:
    print(f"There is a syntax error in your code in line {se.lineno - 3}")
    # Since "import random", an empty line, and the function definition are above the lines of code that the user
    # inputted, the error has its line position shifted upwards by three so that the user gets an accurate error
    # message for their provided code.
    quit()

user_function = namespace[f"user_{user_id}"]

# Executes the code and stores the result of the function as user_function.
# This felt like a janky solution but it allows a helpful error message to be returned

def happy(self_decisions, opponent_decisions, s, o, n):
    """
    Always goes Scissors (happily)
    """
    return "Scissors"


def grumpy(self_decisions, opponent_decisions, s, o, n):
    """
    Always goes Rock (Grumpily)
    """
    return "Rock"


def bashful(self_decisions, opponent_decisions, s, o, n):
    """
    Does the opposite of what the opponent just did (Bashful is avoiding confrontation)
    """
    if len(self_decisions) == 0:
        return "Paper"
    else:
        if opponent_decisions[-1] == "Rock":
            return "Scissors"
        if opponent_decisions[-1] == "Paper":
            return "Rock"
        if opponent_decisions[-1] == "Scissors":
            return "Paper"


def doc(self_decisions, opponent_decisions, s, o, n):
    """
    Copies what the opponent just did (Doc is sensible and fair)
    """
    if len(self_decisions) == 0:
        return "Rock"
    else:
        return opponent_decisions[-1]


def sneezy(self_decisions, opponent_decisions, s, o, n):
    """
    Randomly rocks 1/10 times (he had a sneeze)
    """
    if random.randint(0, 9) == 4:
        return "Rock"
    else:
        return "Paper"


def dopey(self_decisions, opponent_decisions, s, o, n):
    """
    Makes a Paper decision (dopey is a little silly)
    """
    return "Paper"


def sleepy(self_decisions, opponent_decisions, s, o, n):
    """
    Copies the opponent with a "delay" of one move (sleepy has a slow reaction time)
    """
    if len(self_decisions) > 1:
        return opponent_decisions[-2]
    else:
        return "Paper"

# Defines the "Seven Dwarves" that are the other functions competing in this mini-tournament.


players = ["Happy", "Grumpy", "Bashful", "Doc", "Sneezy", "Dopey", "Sleepy", "Your Code"]
functions = [happy, grumpy, bashful, doc, sneezy, dopey, sleepy, user_function]
scores = [0, 0, 0, 0, 0, 0, 0, 0, 0]

for index, player1 in enumerate(players):
    for jindex, player2 in enumerate(players):
        if index >= jindex:
            continue
        # Each player plays each other player once, not twice when it loops around to the other player's turn.

        player_1_score, player_2_score = 0, 0
        player_1_decisions, player_2_decisions = [], []
        player_1_function, player_2_function = functions[index], functions[jindex]
        wins = {"Rock":"Scissors","Paper":"Rock","Scissors":"Paper"}
        for _ in range(400):
            try:
                player_1_decision = player_1_function(player_1_decisions, player_2_decisions, player_1_decisions, player_2_decisions, len(player_1_decisions))
                player_2_decision = player_2_function(player_2_decisions, player_1_decisions, player_2_decisions, player_1_decisions, len(player_1_decisions))
            except Exception as e:
                print(f"Your code has caused an error: {e}")
                # This is mostly name errors that are syntactically correct but use variables not in the namespace
                quit()

            if player_1_decision == wins[player_2_decision]:  # player 2 wins
                player_1_score -= 1
                player_2_score += 1
            elif wins[player_1_decision] == player_2_decision:  # Player 1 wins
                player_1_score += 1
                player_2_score -= 1

            # Otherwise, both players tie and nothing changes.

            player_1_decisions.append(player_1_decision)
            player_2_decisions.append(player_2_decision)

        scores[index] += player_1_score
        scores[jindex] += player_2_score


scores_dict = {player: round(score / 2800, 2) for player, score in zip(players, scores)}
# Divided by 1400 since that creates the average score per round (7 opponents * 200 rounds per game)

scores_dict = dict(sorted(scores_dict.items(), key=lambda item: item[1], reverse=True))

with open(f"/var/www/Mini_Games/RPS/Code_Verification/User_Submitted_Code/dwarf_scores_{user_id}.json", "w") as json_file:
    json.dump(scores_dict, json_file, indent=4)

print("1")
# "1" is what the final output is for submission.php which runs the code. This indicates that everything has gone
# smoothly and that the code can be added to the database
