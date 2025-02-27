import random

def user_6(self_decisions, opponent_decisions):
	return True


def user_8(self_decisions, opponent_decisions):
	return random.choice([True, False])


def user_7(self_decisions, opponent_decisions):
	if len(self_decisions)>0:
	    return(not opponent_decisions[-1])
	else:
	    return(False)


def user_10(self_decisions, opponent_decisions):
	return False


def user_11(self_decisions, opponent_decisions):
	return True


user_code = {"6" : user_6, "8" : user_8, "7" : user_7, "10" : user_10, "11" : user_11}