from merlin import *


def train(dqn, memory, optimizer):
    """
    Train the DQN using a batch from the replay buffer.
    """
    if len(memory) < BATCH_SIZE:
        return  # Wait until we have enough experience

    batch = memory.sample(BATCH_SIZE)
    states, actions, rewards, next_states, dones = zip(*batch)

    states = torch.tensor(np.array(states), dtype=torch.float32)
    actions = torch.tensor(actions, dtype=torch.int64).unsqueeze(1)
    rewards = torch.tensor(rewards, dtype=torch.float32)
    next_states = torch.tensor(np.array(next_states), dtype=torch.float32)
    dones = torch.tensor(dones, dtype=torch.float32)

    # Compute current Q-values
    current_q = dqn(states).gather(1, actions).squeeze(1)

    # Compute target Q-values
    next_q = dqn(next_states).max(1)[0].detach()
    target_q = rewards + GAMMA * next_q * (1 - dones)

    # Compute loss
    loss = nn.MSELoss()(current_q, target_q)

    # Optimize the model
    optimizer.zero_grad()
    loss.backward()
    optimizer.step()

def simulate_game_step(state, actionz):
    """
    Simulates a step in the Prisoner's Dilemma.
    Returns (new_state, reward, done).
    """
    opponent_action = random.choice([0, 1])  # Random opponent for now
    reward_matrix = {(0, 0): 3, (0, 1): 0, (1, 0): 5, (1, 1): 1}
    reward = reward_matrix[(action, opponent_action)]

    # Update opponent history and extract new features
    new_opponent_history = list(state[:-1]) + [opponent_action]
    new_state = extract_features(new_opponent_history)

    done = False  # The game keeps running
    return new_state, reward, done

def train_merlin(opponent_functions):
    dqn = MerlinDQN(INPUT_DIM, OUTPUT_DIM)
    optimizer = optim.Adam(dqn.parameters(), lr=LR)
    memory = ReplayMemory()

    epsilon = EPSILON

    for episode in range(EPISODES):
        state = extract_features([])
        total_reward = 0
        done = False

        while not done:
            action = select_action(state, epsilon, dqn)
            next_state, reward, done = simulate_game_step(state, action,)

            memory.push(state, action, reward, next_state, done)
            state = next_state
            total_reward += reward

            train(dqn, memory, optimizer)

        # Decay epsilon
        epsilon = max(EPSILON_MIN, epsilon * EPSILON_DECAY)

        if episode % 100 == 0:
            print(f"Episode {episode}, Total Reward: {total_reward}, Epsilon: {epsilon:.4f}")

    # Save trained model
    torch.save(dqn.state_dict(), "merlin_dqn.pth")
    print("Training complete! Model saved.")


if __name__ == "__main__":
    train_merlin()
