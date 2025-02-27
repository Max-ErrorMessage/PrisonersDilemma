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


def user_12(self_decisions, opponent_decisions):
	return True


def user_13(self_decisions, opponent_decisions):
	return False


def user_14(self_decisions, opponent_decisions):
	if len(self_decisions) > 0:
	    return not opponent_decisions[-1]
	else:
	    return False


def user_15(self_decisions, opponent_decisions):
	if len(self_decisions) > 0:
	    return opponent_decisions[-1]
	else:
	    return True


def user_16(self_decisions, opponent_decisions):
	if random.randint(0,9) == 4:
	    return False
	else:
	    return True


def user_17(self_decisions, opponent_decisions):
	return random.choice([True,False])


def user_18(self_decisions, opponent_decisions):
	if len(self_decisions) > 1:
	    return opponent_decisions[-2]
	else:
	    return True


def user_19(self_decisions, opponent_decisions):
	20


user_code = {"6" : user_6, "8" : user_8, "7" : user_7, "10" : user_10, "11" : user_11, "12" : user_12, "13" : user_13, "14" : user_14, "15" : user_15, "16" : user_16, "17" : user_17, "18" : user_18, "19" : user_19}