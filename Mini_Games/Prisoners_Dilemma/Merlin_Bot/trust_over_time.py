import matplotlib.pyplot as plt
import pandas as pd

df = pd.read_csv('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/trustyness')

plt.figure(figsize=(8, 5))
plt.plot(df.index, df["Total Trusts Scores"], marker='o', color='b', label="Trust")
plt.plot(df.index, df["Total Betrayal Scores"], marker='s', color='r', label="Betrayal")

plt.xlabel("Training round")
plt.ylabel("Total Q-Values")
plt.title("Trust and betrayal of Merlin over time")
plt.legend()

plt.savefig("/var/www/html/trustyness.png", dpi=300, bbox_inches='tight')