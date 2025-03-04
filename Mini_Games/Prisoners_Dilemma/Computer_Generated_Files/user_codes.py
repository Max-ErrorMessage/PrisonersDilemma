import random

def user_11(self_decisions, opponent_decisions, s, o, n):
	return True


def user_9(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) == 0:
	    return True
	else:
	    return not self_decisions[-1]


def user_10(self_decisions, opponent_decisions, s, o, n):
	return True if sum([0 if decision else 1 for decision in opponent_decisions]) == 0 else False


def user_18(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) == 0:
	    return True
	
	return opponent_decisions[-1]


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


def user_19(self_decisions, opponent_decisions, s, o, n):
	    return len(self_decisions) % 2


def user_17(self_decisions, opponent_decisions, s, o, n):
		x,y,s,o,t=len,range,self_decisions,opponent_decisions,5<<1;z,l,f=[~(228>>i)&1 for i in y(9,-1,-1)],x(s),sum(o),
		if l<t:return z[l]
		if o[:t]==z: return 0
		if s[-4:-1]!=o[-3:]:return not l%3
		c=e=0;d=l-f/l;t=sum(o[i]==s[i-1]for i in y(1,l))/max(1,l-1)
		for i in 1,0:
			a,b=s+[i],o[:]
			for _ in y(5):j=x(b)==l;r=a[-1]if j and t>.8 else 1if d<.2and j else 0if d>.8and j else q[0]if x(b)>l and x(q:=b[-3:])>2and q[0]==q[2]else b[-1]if b else 1;b+=[r];v=r*4+1-a[-1]*(r+1);c+=v*i;e+=v*(1-i);a+=[b[-1]*(d<=.25)]
		return c>=e


def user_20(self_decisions, opponent_decisions, s, o, n):
	if len(self_decisions) < 2:
	    return True
	else:
	    return opponent_decisions[-1] and opponent_decisions[-2]


def user_24(self_decisions, opponent_decisions, s, o, n):
	return True if len(opponent_decisions) == 0 else opponent_decisions[-1]


def user_28(self_decisions, opponent_decisions, s, o, n):
	n = len(opponent_decisions)
	if n >= 300:
	    return False  # At n=300, always return False
	else:
	    probability_of_false = n / 300
	return random.random() > probability_of_false


def user_7(self_decisions, opponent_decisions, s, o, n):
	if len(opponent_decisions) > 1 and opponent_decisions[0] == False:
	    if opponent_decisions[1] == False:
	        return False
	elif len(opponent_decisions) > 4 and opponent_decisions[1] == True:
	    if opponent_decisions[2] == True and opponent_decisions[4] == False:
	        return True
	elif len(opponent_decisions) % 4 == 3:
	    return False
	else:
	    return True


def user_26(self_decisions, opponent_decisions, s, o, n):
	return sum(o[-8:]) == 0


def user_6(self_decisions, opponent_decisions, s, o, n):
	return sum(o[-5:]) == 0


def user_3(self_decisions, opponent_decisions, s, o, n):
	if sum(o[-10:]) == 10:
	    return False
	return sum(o[-2:]) == 2


def user_25(self_decisions, opponent_decisions, s, o, n):
	if n < 5:
	    return True
	
	if n > 20 and sum(o[-10:]) == sum(s[-10:]):
	    return True
	
	betrayals = sum(1 for d in o if not d)
	
	betrayal_threshold = 0.5
	betrayal_rate = betrayals / n
	
	if betrayal_rate < betrayal_threshold:
	    return True
	
	punishment_threshold = 0.7
	if betrayal_rate > punishment_threshold:
	    return False
	
	if random.random() < 0.2:
	    return False
	
	return True


def user_8(self_decisions, opponent_decisions, s, o, n):
	if n == 0:
	    return True
	
	if sum(o) > 0:
	    return False
	
	if n == 150:
	    return False
	
	if o[151]:
	    return False
	
	return True


def user_2(self_decisions, opponent_decisions, s, o, n):
	if sum(o) == n:
	    return False
	if n > 200:
	    return random.choice([True, False])
	return True


def user_14(self_decisions, opponent_decisions, s, o, n):
	return False if n < 10 else True


def user_4(self_decisions, opponent_decisions, s, o, n):
	if sum(o[-10:]) == 10:
	    return False
	return sum(o[-2:]) == 2


def user_1(self_decisions, opponent_decisions, s, o, n):
	if False in o or n < 150:
	    return False
	
	if n < 150:
	    return True
	
	if n == 150:
	    return False
	
	if n == 151:
	    return True
	
	if o[151]:
	    return False
	
	if not o[151]:
	    if n == 152:
	        return True
	
	    if not o[152]:
	        return False
	    return True


def user_29(self_decisions, opponent_decisions, s, o, n):
	return True


user_code = {"11" : user_11, "9" : user_9, "10" : user_10, "18" : user_18, "21" : user_21, "22" : user_22, "19" : user_19, "17" : user_17, "20" : user_20, "24" : user_24, "28" : user_28, "7" : user_7, "26" : user_26, "6" : user_6, "3" : user_3, "25" : user_25, "8" : user_8, "2" : user_2, "14" : user_14, "4" : user_4, "1" : user_1, "29" : user_29}