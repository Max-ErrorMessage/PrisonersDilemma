import random

def user_1(self_decisions, opponent_decisions):
	if len(self_decisions) > 0:
	    return not opponent_decisions[-1]
	else:
	    return False


def user_3(self_decisions, opponent_decisions):
	return False


def user_4(self_decisions, opponent_decisions):
	if len(self_decisions) == 0:
	    return True
	else:
	    return opponent_decisions[-1]


def user_2(self_decisions, opponent_decisions):
	grudge = False
	if len(opponent_decisions) == 0:
	    return True
	if grudge:
	    return False
	if not opponent_decisions[-1]:
	    grudge = True
	    return False
	return True


def user_6(self_decisions, opponent_decisions):
	return True if len(self_decisions) <= 0 else opponent_decisions[-1]


user_code = {"1" : user_1, "3" : user_3, "4" : user_4, "2" : user_2, "6" : user_6}