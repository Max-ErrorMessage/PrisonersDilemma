from merlin import *
import sys

s = sys.argv[1]
o = sys.argv[2]

self_decisions = [True for bit in s if bit == '1']
opponent_decisions = [True for bit in o if bit == '1']

merlin = AI_Agent(epsilon=0)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')

choice = merlin.choose_action(self_decisions, opponent_decisions)

print(1 if choice else 0)