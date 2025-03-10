import os
import random
import numpy as np
import torch
import torch.nn as nn
import torch.optim as optim
from collections import deque

# ----------------- 1. DEFINE HYPERPARAMETERS -----------------
GAMMA = 0.99  # Discount factor
BATCH_SIZE = 32
LR = 0.001  # Learning rate
EPSILON = 1.0  # Initial exploration rate
EPSILON_DECAY = 0.995  # How fast we decrease exploration
EPSILON_MIN = 0.01
MEMORY_SIZE = 10000  # Replay buffer size
EPISODES = 10000  # Total training episodes
INPUT_DIM = 2  # Features (opponent behavior summary)
OUTPUT_DIM = 2  # Two actions: Cooperate (0) or Defect (1)

# ----------------- 2. DEFINE DQN MODEL -----------------
class MerlinDQN(nn.Module):
    def __init__(self, input_dim, output_dim):
        super(MerlinDQN, self).__init__()
        self.fc1 = nn.Linear(input_dim, 64)
        self.fc2 = nn.Linear(64, 64)
        self.fc3 = nn.Linear(64, output_dim)

    def forward(self, state):
        x = torch.relu(self.fc1(state))
        x = torch.relu(self.fc2(x))
        return self.fc3(x)  # Returns Q-values for each action

# ----------------- 3. EXPERIENCE REPLAY BUFFER -----------------
class ReplayMemory:
    def __init__(self, capacity=MEMORY_SIZE):
        self.memory = deque(maxlen=capacity)

    def push(self, state, action, reward, next_state, done):
        self.memory.append((state, action, reward, next_state, done))

    def sample(self, batch_size):
        return random.sample(self.memory, batch_size)

    def __len__(self):
        return len(self.memory)

# ----------------- 4. FEATURE EXTRACTION -----------------
def extract_features(opponent_decisions):
    """
    Reduces game state to a simple feature vector.
    Returns (sum of last 10 moves, last opponent move).
    """
    memory = 10

    window_size = min(memory, len(opponent_decisions))
    if window_size == 0:
        return np.array([0, 0], dtype=np.float32)

    recent_moves = opponent_decisions[-window_size:]
    recent_moves.extend([0] * (memory - len(recent_moves)))
    return np.array([recent_moves], dtype=np.float32)

# ----------------- 5. SELECT ACTION -----------------
def select_action(state, epsilon, dqn):
    """
    Selects action based on epsilon-greedy policy.
    """
    if random.random() < epsilon:
        return random.choice([0, 1])  # Random move (exploration)
    else:
        state_tensor = torch.tensor(state, dtype=torch.float32).unsqueeze(0)
        q_values = dqn(state_tensor)
        return torch.argmax(q_values).item()  # Best action (exploitation)

