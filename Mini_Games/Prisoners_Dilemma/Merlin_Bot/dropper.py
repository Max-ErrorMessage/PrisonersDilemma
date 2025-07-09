import pandas as pd

df = pd.read_csv('game_rewards')

# Number of rows to remove
num_remove = 5_000

# Calculate the center index
center_idx = len(df) // 2

# Calculate the start and end indices for removal
start_idx = center_idx - (num_remove // 2)
end_idx = center_idx + (num_remove // 2)

# Drop rows from start_idx to end_idx
df = df.drop(index=range(2000, 5000)).reset_index(drop=True)

# print(df.index)

df.to_csv('game_rewards', index=False)
