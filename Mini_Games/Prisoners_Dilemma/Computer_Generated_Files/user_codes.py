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
	if len(opponent_decisions) < 10:
	    return True
	else:
	    return opponent_decisions[-1]


def user_17(self_decisions, opponent_decisions, s, o, n):
	return False


def user_20(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) < 2:
	    return True
	else:
	    return opponent_decisions[-1] and opponent_decisions[-2]


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


def user_60(self_decisions, opponent_decisions, s, o, n):
	return True


def user_28(self_decisions, opponent_decisions, s, o, n):
	return True if n < 1 else o[-1]


def user_62(self_decisions, opponent_decisions, s, o, n):
	if n == 0:
	    return True
	if n >= 2 and len(opponent_decisions) >= 2:
	    if not opponent_decisions[-1] and not opponent_decisions[-2]:
	        return False
	if len(opponent_decisions) >= 2 and opponent_decisions[-1] and opponent_decisions[-2]:
	    return True
	return opponent_decisions[-1] if n > 0 else True


def user_1(self_decisions, opponent_decisions, s, o, n):
	if o.count(False) == 0:
	    return False
	if o.count(False) < 4:
	    return True
	return False


def user_2(self_decisions, opponent_decisions, s, o, n):
	initial_blindness = 4
	# The bot doesn't see what you do for the first few rounds
	
	if n==0:
	    return True
	# Initially cooperate
	
	if n < initial_blindness:
	    return o[-1]
	# Copybot during the blindness period
	
	return sum(o[initial_blindness:]) == n - initial_blindness
	# Always cooperate afterwards, unless they have ever betrayed you


def user_7(self_decisions, opponent_decisions, s, o, n):
	copy_time = 10
	# amount of round the bot will be a copy bot for, before switching to grudge bot
	
	if n==0:
	    return True
	elif n < copy_time:
	    return o[-1]
	
	return sum(o[copy_time:]) == n - copy_time
	
	
	# dilemna


def user_55(self_decisions, opponent_decisions, s, o, n):
	return sum(o[-1:])


def user_56(self_decisions, opponent_decisions, s, o, n):
	t,f = False,True
	Burger,cHeese,hOtdOg,krill,paSta = t,f,5,n,o
	hungy = [Burger,cHeese]
	happy = [cHeese, Burger]
	burger_constant = 5.6
	hippos = [Burger]
	hungry = [cHeese,cHeese]
	HungryHungyHippos = hungy + hungry + hippos
	if krill < hOtdOg:
	    return Burger
	    if burger_constant * krill < 10:
	        return cHeese if paSta[krill-hOtdOg] else Burger 
	#if paSta[:hOtdOg] == HungryHungyHippos:
	#    return cHeese
	if Burger in paSta[hOtdOg:]:
	    return Burger
	return cHeese
	
	#hamburder


user_code = {"0" : user_0, "9" : user_9, "10" : user_10, "21" : user_21, "22" : user_22, "17" : user_17, "20" : user_20, "53" : user_53, "52" : user_52, "60" : user_60, "28" : user_28, "62" : user_62, "1" : user_1, "2" : user_2, "7" : user_7, "55" : user_55, "56" : user_56}