import re
import sys
import json


with open('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.json', 'r') as file:
    data = json.load(file)
    
functions = dict({})

for item in data:
    functions[item["User_ID"]] = item["Code"]
    
file_out = "import random"

keywords = [
    "print", "import", "exec", "eval", "open", "execfile", "compile", "input", "__import__", "os.system", "os.popen",
    "subprocess.call", "subprocess.run", "globals", "locals", "file", "pickle", "pickle.load", "shlex.split",
    "os.remove", "os.rename", "socket"
]
pattern = r"(" + "|".join(keywords) + r")"

for username, code in functions.items():
    if re.search(pattern, code):
        code = "return False"

    file_out += f"\n\ndef user_{username}(self_decisions, opponent_decisions):\n"

    for line in code.splitlines():
        file_out += f"\t{line}\n"

file_out += "\n\nuser_code = {"

for index, username in enumerate(functions.keys()):
    file_out += f'"{username}" : user_{username}'
    if index < len(functions.keys()) - 1:
        file_out += ", "

file_out += "}"

with open("/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py", "w") as file:
    file.write(file_out)
    
