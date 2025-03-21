def user_10(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	if n % 10 == 0 and n != 0: 
	    return 'Scissors'
	if n > 0 and o[-1] == 'Paper':
	    return 'Paper'
	return 'Rock' 
def user_21(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	if n == 0:
	    return "Scissors"
	return o[-1]
def user_28(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	if n < 6:
	    return scissors
	if o[-5:].count(rock) > 3:
	    return paper
	if o[-5:].count(paper) > 3:
	    return scissors
	if o[-5:].count(scissors) > 3:
	    return rock
	return paper
def user_2(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	cycle = [rock, paper, paper, scissors, scissors, rock, scissors, rock, rock, paper, paper, scissors]
	return cycle[n % len(cycle)] #james is hot
def user_7(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	if n>2:
	    return o[-2]
	return "Rock"
def user_73(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	return "Rock"
	
	#James is really fucking hot and i think he might be the funniest person i know
	# love that man
def user_55(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	w = {rock: scissors, scissors: paper, paper: rock}
	
	if n == 0:
	    return rock
	
	if paper not in o[-5:]:
	    return rock
	if scissors not in o[-5:]:
	    return paper
	if rock not in o[-5:]:
	    return scissors
	
	return w[o[-1]]
def user_1(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	wins = {rock:paper,paper:scissors,scissors:rock}
	if n < 6:
	    return paper
	if n < 12:
	    return wins[o[-6]]
	return wins[o[-12]]
def user_56(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	seq = [rock,paper,scissors]
	
	return seq[n%3]


user_code = {"10" : user_10, "21" : user_21, "28" : user_28, "2" : user_2, "7" : user_7, "73" : user_73, "55" : user_55, "1" : user_1, "56" : user_56}