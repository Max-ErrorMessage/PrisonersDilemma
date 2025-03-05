import re
import sys
import random
import json

user_id = sys.argv[1]

with open(f"/var/www/Mini_Games/Yahtzee/Code_Verification/User_Submitted_Code/user_{user_id}.txt", 'r') as file:
    code = file.read()

with open("/var/www/Mini_Games/Yahtzee/Code_Verification/User_Submitted_Code/log.txt", "a") as log:
    log.write(f'{code}\nUser ID: {user_id}\n\n')



keywords = [
    "print", "import", "exec", "eval", "open", "execfile", "compile", "input", "__import__", "os.system", "os.popen",
    "subprocess.call", "subprocess.run", "global", "globals", "locals", "file", "pickle", "pickle.load", "shlex.split",
    "os.remove", "os.rename", "socket"
]

pattern = r"\b(" + "|".join(keywords) + r")\b(?!\s*\()"

if re.search(pattern, code):
    print("Code Error: unsafe functions detected")
    quit()

if code.count("$") > 1:
    print("Code Error: Do not use the $ symbol")
    quit()

reroll_code, select_code = code.split("$")

file_code = f"import random\n\ndef user_{user_id}_reroll(availability, available_points, claimed_points, dice):\n"

for line in reroll_code.splitlines():
    file_code += f"    {line}\n"


file_code += f"\ndef user_{user_id}_select(availability, available_points, claimed_points, dice, choices):\n"

for line in select_code.splitlines():
    file_code += f"    {line}\n"

namespace = {}

try:
    exec(file_code, namespace)
except SyntaxError as se:
    print(f"There is a syntax error in your code in line {se.lineno - 3}")
    quit()

user_reroll_function = namespace[f"user_{user_id}_reroll"]
user_select_function = namespace[f"user_{user_id}_select"]

#############

availability = {}
available_points = {}
claimed_points = {}
dice = []

def roll(dice,reroll_indexes): # rolls the dice at the indexes included in the reroll indexes list, for roll([1,1,1,1,1],[0,2,4] could return [2,1,3,1,5])
    for i in reroll_indexes:
        dice[i] = random.randint(1,6)
    return dice

def calculate_points(): # calculates all available points with the current dice and updates the available_points dictionary accordingly
    global availability, available_points, claimed_points, dice
    for i in range(1,7): # checks all the ones, twos, threes etc
        calculate_upper_section(i)
    calculate_kind() # checks 3oaK, 4oaK, yahtzee and full house
    calculate_straights() # checks small/large straights
    calculate_chance() # checks chance


def calculate_upper_section(num):
    global availability, available_points, claimed_points, dice
    translator = {1:"Ones", 2:"Twos", 3:"Threes", 4:"Fours", 5:"Fives", 6:"Sixes"}
    if availability[translator[num]]:
        total = 0
        for i in dice:
            if i == num:
                total += num
        available_points[translator[num]] = total

def calculate_kind():
    global availability, available_points, claimed_points, dice
    counts = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0}
    total = 0
    for i in dice:
        counts[i] += 1
        total += i
    max_value = max(counts.values())
    max_key = max(counts, key=counts.get)
    if max_value > 4 and availability["Yahtzee"]:
        available_points["Yahtzee"] = 50
    if max_value > 3 and availability["4 of a Kind"]:
        available_points["4 of a Kind"] = total
    if max_value > 2:
        if availability["3 of a Kind"]:
            available_points["3 of a Kind"] = total
        counts[max_key] = 0
        if max(counts.values()) == 2 and availability["Full House"]:
            available_points["Full House"] = 25


def calculate_straights():
    global availability, available_points, claimed_points, dice
    dice_set = set(dice)

    small_straights = [{1, 2, 3, 4}, {2, 3, 4, 5}, {3, 4, 5, 6}]
    large_straights = [{1, 2, 3, 4, 5}, {2, 3, 4, 5, 6}]

    found = False
    if availability["Small Straight"]:
        for i in small_straights:
            if i <= dice_set and not found:
                found = True
                available_points["Small Straight"] = 30

    if availability["Large Straight"]:
        for i in large_straights:
            if i <= dice_set and not found:
                found = True
                available_points["Small Straight"] = 30

def calculate_chance():
    global availability, available_points, claimed_points, dice
    if availability["Chance"]:
        total = 0
        for i in dice:
            total += i
        available_points["Chance"] = total

def check_bonus_and_total():
    global availability, available_points, claimed_points, dice
    upper_section = ["Ones","Twos","Threes","Fours","Fives","Sixes"]
    upper_sum = 0
    for i in upper_section:
        upper_sum += claimed_points[i]
    lower_section = ["3 of a Kind","4 of a Kind","Full House","Small Straight","Large Straight","Yahtzee","Chance"]
    lower_sum = 0
    for i in lower_section:
        lower_sum += claimed_points[i]
    if upper_sum >= 63:
        claimed_points["Bonus"] = 35
        upper_sum += 35
    claimed_points["Total"] = upper_sum + lower_sum + claimed_points["Yahtzee Bonus"]

def check_yahtzee_bonus():
    global availability, available_points, claimed_points, dice
    if claimed_points["Yahtzee"] == 50:
        counts = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0}
        for i in dice:
            counts[i] += 1
        max_value = max(counts.values())
        if max_value == 5:
            claimed_points["Yahtzee Bonus"] += 100



def simulate():
    global availability, available_points, claimed_points, dice
    availability = {"Ones": True, "Twos": True, "Threes": True, "Fours": True, "Fives": True, "Sixes": True,
                    "3 of a Kind": True, "4 of a Kind": True, "Full House": True, "Small Straight": True,
                    "Large Straight": True, "Yahtzee": True, "Chance": True}

    available_points = {"Ones": 0, "Twos": 0, "Threes": 0, "Fours": 0, "Fives": 0, "Sixes": 0, "3 of a Kind": 0,
                        "4 of a Kind": 0, "Full House": 0, "Small Straight": 0, "Large Straight": 0, "Yahtzee": 0,
                        "Chance": 0}

    claimed_points = {"Ones": 0, "Twos": 0, "Threes": 0, "Fours": 0, "Fives": 0, "Sixes": 0, "3 of a Kind": 0,
                      "4 of a Kind": 0, "Full House": 0, "Small Straight": 0, "Large Straight": 0, "Yahtzee": 0,
                      "Chance": 0, "Bonus": 0, "Yahtzee Bonus":0, "Total": 0}
    stop = False
    while not stop: # each round starts here
        available_points = {"Ones": 0, "Twos": 0, "Threes": 0, "Fours": 0, "Fives": 0, "Sixes": 0, "3 of a Kind": 0,
                                "4 of a Kind": 0, "Full House": 0, "Small Straight": 0, "Large Straight": 0, "Yahtzee": 0,
                                "Chance": 0} # resets available points
        for i in availability.keys():
            if availability[i] == False:
                available_points[i] = -1 # sets all avaialable points of preclaimed spaces to -1

        dice = roll([1,1,1,1,1],[0,1,2,3,4])
        for i in range(2):
            try:
                dice_to_reroll = user_reroll_function(availability, available_points, claimed_points, dice)
            except Exception as e:
                print(f"Your code has caused an error: {e}")
                quit()
            roll(dice, dice_to_reroll)
            calculate_points()
        choices = []
        for i in availability.keys():
            if availability[i] == True:
                choices.append(i)
        if len(choices) == 1:
            stop = True
        if check_yahtzee_bonus():
            break
        try:
            choice = user_select_function(availability, available_points, claimed_points, dice, choices)
        except Exception as e:
            print(f"Your code has caused an error: {e}")
            quit()
        claimed_points[choice] = available_points[choice]
        availability[choice] = False
        check_bonus_and_total()
    return claimed_points["Total"]

rounds = 100
score_sum = 0
for i in range(rounds):
    score_sum += simulate()
final_score = score_sum / rounds
#############

scores_dict = {"Your Code": final_score}

with open(f"/var/www/Mini_Games/Yahtzee/Code_Verification/User_Submitted_Code/dwarf_scores_{user_id}.json", "w") as json_file:
    json.dump(scores_dict, json_file, indent=4)


with open('/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json', 'r') as file:
    existing_scores = json.load(file)

existing_scores[user_id] = final_score

with open('/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json', 'w') as file:
    json.dump(existing_scores, file, indent=4)


print(1)

