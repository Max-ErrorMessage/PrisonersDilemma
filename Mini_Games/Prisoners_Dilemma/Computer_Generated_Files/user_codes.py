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


def user_1(self_decisions, opponent_decisions, s, o, n):
	h,e,a,r,t = o,n,9,True,False
	s,q,u,i,d,g,y = t,h,e,a,t,r,e
	n,o,b,u,r,g,e,r = d,i,s,a,s,t,e,r
	
	if g:
	    l = 1
	    for p in range(i):
	        if a == o and g == s:
	            l += sum(h[-a:])
	            if l > 3:
	                if s or ((g==t) and (l == 2)):
	                    return m
	                if t:
	                    return r if g != i else y
	if y < o and b == s:
	    return b
	return (n not in q)


def user_56(self_decisions, opponent_decisions, s, o, n):
	dough,tomato_sauce,cheese,pepporoni,sausages,mushrooms,chicken,ham,pepper = n,s,o,True,False,0,1,9,8
	pizza = [dough,tomato_sauce,cheese,pepporoni,sausages,mushrooms,chicken,ham,pepper]
	
	try:
	    if pizza[2] == tomato_sauce and pizza[pizza[6]][-(pizza[6])]:
	        if pizza[1] == pizza[2]:
	            return pizza[4]
	        if pizza [6] > pizza[0]:
	            return pepperoni
	        return pizza[1][-dough]
	except IndexError:
	    if sausages:
	       pizza[6]=pizza[5]
	       if mushrooms > sum(s):
	           return pepporoni
	       return cheese[pizza[5]]
	if pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[8]]]]]]]]]]]]]] != 8:
	    return pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[pizza[8]]]]]]]]]]]]]
	if pizza[0] < pizza[7]:
	    return pizza[4]
	return (pizza[4] not in pizza[2])


def user_55(self_decisions, opponent_decisions, s, o, n):
	if len(opponent_decisions) < 9:
	    return False
	return o[-1]


user_code = {"0" : user_0, "9" : user_9, "10" : user_10, "21" : user_21, "22" : user_22, "17" : user_17, "20" : user_20, "53" : user_53, "52" : user_52, "60" : user_60, "28" : user_28, "62" : user_62, "2" : user_2, "7" : user_7, "1" : user_1, "56" : user_56, "55" : user_55}