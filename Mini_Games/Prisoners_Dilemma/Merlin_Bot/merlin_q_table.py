from merlin import *

merlin = AI_Agent()
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
merlin.print_q_table()
