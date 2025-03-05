"""
This file takes the user codes that have been inputted and writes a python file that defines the codes and stores them
in a dictionary that other files can access later. By importing the dictionary from that file, other programs can
directly call the relevant function when needed.

This file is called by calculate_scores.php.
"""

import re
import json


with open('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.json', 'r') as file:
    data = json.load(file)
# Since passing in the data as an argument would be unwieldy and likely to cause errors, the file that calls this
# simply stores the relevant data in a JSON that this file reads.
    
functions = dict({})

for item in data:
    functions[item["User_ID"]] = item["Code"]

# file_out is building the literal string output for the file
file_out = "import random"
# the only acceptable library for users to use - random is manually imported at the head of the file

keywords = [
    "print", "import", "exec", "eval", "open", "execfile", "compile", "input", "__import__", "os.system", "os.popen",
    "subprocess.call", "subprocess.run", "globals", "locals", "file", "pickle", "pickle.load", "shlex.split",
    "os.remove", "os.rename", "socket", "quit"
]
# A list of keywords that the file scans for. There are a couple of levels of redundancy (in theory, it is impossible to
# run os-related functions without importing os, but additional security never hurt anyone)
pattern = r"(" + "|".join(keywords) + r")"

for username, code in functions.items():
    if re.search(pattern, code):
        code = "return False"
        # If any of the keywords are found in the code, the code is replaced with 'Return False'
        # There is an additional, identical check when the code is inputted so in theory it's not possible for harmful
        # code to get to this stage but if SQL was compromised it is a comforting thought that the code still wouldn't
        # execute.

        # This even detects variable names or string literals, but these are very odd things to name variables or have
        # in string literals


    file_out += f"\n\ndef user_{username}(self_decisions, opponent_decisions, s, o, n):\n"
    # All functions look like that and the user can use all of these parameters when writing their code.
    # s = self_decisions, o = opponent_decisions, n = len(self_decisions)


    for line in code.splitlines():
        file_out += f"\t{line}\n"

file_out += "\n\nuser_code = {"

for index, username in enumerate(functions.keys()):
    file_out += f'"{username}" : user_{username}'
    if index < len(functions.keys()) - 1:
        file_out += ", "

file_out += "}"
# Defines a dictionary that can be accessed by other files that links user IDs to their functions.

with open("/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py", "w") as file:
    file.write(file_out)
    # Stores the result in a JSON file for later programs to access.
    
