import matplotlib.pyplot as plt
import pandas as pd

head = 0
tail = 0
of = pd.read_csv('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/game_rewards')
if head == 0 and tail == 0:
    df = of.copy()  # Take the full dataset
else:
    df = of.iloc[max(0, len(of) - tail): len(of) if head == 0 else head]
df = df.reset_index(drop=True)

window_size = int(len(df) * 0.025)  
# Adjusts the rolling average to automatically be roughly 3/100th of the total number of data points
df['Rolling_Avg'] = df['Game rewards'].rolling(window=window_size, min_periods=1).mean()

plt.figure(figsize=(8, 5))
plt.plot([i * 100 for i in df.index], df['Rolling_Avg'], color='b')

plt.xlabel("Training round")
plt.ylabel("Average game rewards")
plt.title("How much is Merlin rewarded as he trains?")

plt.savefig("/var/www/html/rewards.png", dpi=300, bbox_inches='tight')
