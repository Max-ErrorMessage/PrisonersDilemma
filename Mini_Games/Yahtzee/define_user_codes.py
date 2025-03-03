import re
import sys
import json


with open('/var/www/domains/twokie.com/Mini_Games/Yahtzee/Computer_Generated_Files/user_codes.json', 'r') as file:
    data = json.load(file)
    
functions = dict({})

for item in data:
    item["Code"] = item["Code"].split("$")
    functions[item["UserID"]] = item["Code"]
    
file_out = "import random"

keywords = ["print", "import", "exec", "eval", "open", "execfile", "compile", "input"]
pattern = r"\b(" + "|".join(keywords) + r")\s*\("

for username, code in functions.items():
    if re.search(pattern, code):
        code = "return False"

    file_out += f"\n\ndef user_{username}_reroll(availability, available_points, claimed_points, dice):\n"

    for line in code[0].splitlines():
        file_out += f"\t{line}\n"
    
    file_out += f"\n\ndef user_{username}_select(availability, available_points, claimed_points, dice, choices):\n"

    for line in code[1].splitlines():
        file_out += f"\t{line}\n"
    

file_out += "\n\nuser_code = {"

for index, username in enumerate(functions.keys()):
    file_out += f'"{username}" : [user_{username}_reroll, user_{username}_select]'
    if index < len(functions.keys()) - 1:
        file_out += ", "

file_out += "}"

with open("/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/user_codes.py", "w") as file:
    file.write(file_out)
    
print("Cree")