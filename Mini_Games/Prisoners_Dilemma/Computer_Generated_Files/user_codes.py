import random

def user_0(self_decisions, opponent_decisions, s, o, n):
	return True


def user_9(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) == 0:
	    return True
	else:
	    return not self_decisions[-1]


def user_10(self_decisions, opponent_decisions, s, o, n):
	return True if sum([0 if decision else 1 for decision in opponent_decisions]) == 0 else False


def user_21(self_decisions, opponent_decisions, s, o, n):
	total_betrayals = sum([1 if not decision else 0 for decision in opponent_decisions])
	if total_betrayals >= 1:
	    if random.choice([True, True, False]):
	        return False
	    return True
	if random.choice([True, True, False]):
	    return True
	return False


def user_22(self_decisions, opponent_decisions, s, o, n):
	return True


def user_17(self_decisions, opponent_decisions, s, o, n):
	return False


def user_20(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) < 2:
	    return True
	else:
	    return opponent_decisions[-1] and opponent_decisions[-2]


def user_28(self_decisions, opponent_decisions, s, o, n):
	return False


def user_53(self_decisions, opponent_decisions, s, o, n):
	trust = opponent_decisions.count(True)
	betray = opponent_decisions.count(False)
	
	if trust < betray:
	    return True
	else:
	    return False


def user_52(self_decisions, opponent_decisions, s, o, n):
	if n < 1:
	    return True
	elif self_decisions[-1] == False:
	    return False
	elif opponent_decisions[-1] == False:
	    return False
	elif n > 158:
	    return False
	else:
	    return True 


def user_2(self_decisions, opponent_decisions, s, o, n):
	if n < 7:
	    return True
	return not False in o[6:]


def user_56(self_decisions, opponent_decisions, s, o, n):
	if n < 9:
	    return False
	return o[-1]


def user_55(self_decisions, opponent_decisions, s, o, n):
	return False


def user_60(self_decisions, opponent_decisions, s, o, n):
	return True


def user_1(self_decisions, opponent_decisions, s, o, n):
	if n == 0:
	    return True
	if n < 200:
	    return o[-1]
	if n == 200:
	    return False
	if n < 203:
	    return True
	if o[200]:
	    return False
	return o[-1]


user_code = {"0" : user_0, "9" : user_9, "10" : user_10, "21" : user_21, "22" : user_22, "17" : user_17, "20" : user_20, "28" : user_28, "53" : user_53, "52" : user_52, "2" : user_2, "56" : user_56, "55" : user_55, "60" : user_60, "1" : user_1}