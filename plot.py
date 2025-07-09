import matplotlib.pyplot as plt
import pandas as pd
import importlib.util

plt.style.use('dark_background')

"""
Makes a pie chart showing how much of the current bot population will betray unprovoked
"""
merlin_path = "/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.py"

spec = importlib.util.spec_from_file_location("merlin", merlin_path)
merlin_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(merlin_module)
Merlin_Agent = getattr(merlin_module, "AI_Agent", None)

merlin = Merlin_Agent(epsilon=0)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

user_codes['0'] = merlin.action

betray = 0
for function in user_codes.values():
    for i in range(400):
        trues = [True] * i
        decision = function(trues, trues, trues, trues, i)
        if not decision:
            betray += 1
            break

coop = len(user_codes) - betray

plt.pie([coop, betray], labels=['Cooperate', 'Betray'], colors=['#225522', '#003300'], autopct='%1.1f%%',
        textprops={'color': 'white'})
plt.suptitle("Twokie.com - Prolonged cooperation")
plt.title("How many of the current bots will betray unprovoked?", fontsize=10, color='gray')
plt.savefig("/var/www/html/betrayal.png", dpi=300, bbox_inches='tight')
########################################################################################################################

"""
Makes a line graph that shows the game rewards received by Merlin over time as he trains
"""
head = 0
tail = 0
of = pd.read_csv('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/game_rewards')
if head == 0 and tail == 0:
    df = of.copy()
else:
    df = of.iloc[max(0, len(of) - tail): len(of) if head == 0 else head]
df = df.reset_index(drop=True)

window_size = int(len(df) * 0.01)
df['Rolling_Avg'] = df['Game rewards'].rolling(window=window_size, min_periods=1).mean()

plt.figure(figsize=(8, 5))
plt.plot([i * 100 for i in df.index], df['Rolling_Avg'], color='#225522')

plt.xlabel("Training round")
plt.ylabel("Average game rewards")
plt.title("How much is Merlin rewarded as he trains?")

plt.savefig("/var/www/html/rewards.png", dpi=300, bbox_inches='tight')
########################################################################################################################

