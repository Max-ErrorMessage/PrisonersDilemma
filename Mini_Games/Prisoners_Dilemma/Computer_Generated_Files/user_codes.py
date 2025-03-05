import random

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


def user_7(self_decisions, opponent_decisions, s, o, n):
	if n>0:
	    if random.randint(0,30) != 14 or False in o:
	        return False
	    return o[-1]
	else:
	    return True


def user_2(self_decisions, opponent_decisions, s, o, n):
	return sum(o) == n


def user_1(self_decisions, opponent_decisions, s, o, n):
	if n < 5:
	    return False
	else:
	    return o[-1]


def user_28(self_decisions, opponent_decisions, s, o, n):
	return not sum(o) == n


user_code = {"9" : user_9, "10" : user_10, "21" : user_21, "22" : user_22, "17" : user_17, "20" : user_20, "7" : user_7, "2" : user_2, "1" : user_1, "28" : user_28}