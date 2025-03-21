"""
Simulates a decision made by merlin given the previous decisions of both players as a binary string.

Author: Max Worby
"""

from merlin import *
import sys

try:
    s = sys.argv[1]
    o = sys.argv[2]
except IndexError:
    s = ""
    o = ""

self_decisions = [bit == '1' for bit in s]
opponent_decisions = [bit == '1' for bit in o]

merlin = AI_Agent(epsilon=0)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')

choice = merlin.action(self_decisions, opponent_decisions)

print(1 if choice else 0)
