def user_56(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	l = ["Rock","Paper","Scissors"]
	return l[n%3]
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
def user_55(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	x = "</pre> Hello! <pre>"
	return paper
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
def user_1(self_decisions, opponent_decisions, s, o, n):
	rock, paper, scissors = 'Rock', 'Paper', 'Scissors'
	wins = {rock:paper,paper:scissors,scissors:rock}
	if n < 6:
	    return paper
	return wins[o[-6]]


user_code = {"56" : user_56, "10" : user_10, "21" : user_21, "28" : user_28, "2" : user_2, "55" : user_55, "7" : user_7, "73" : user_73, "1" : user_1}