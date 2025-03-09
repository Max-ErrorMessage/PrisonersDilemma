import pickle
import random


class AI_Agent:
    def __init__(self, alpha=0.1, gamma=0.9, epsilon=0.3):
        self.q_table = {}
        self.alpha = alpha
        self.gamma = gamma
        self.epsilon = epsilon

    def get_q_value(self, state, action):
        return self.q_table.get((state, action), 0.0)

    def choose_action(self, state):
        if random.uniform(0, 1) < self.epsilon:
            return random.choice([True, False])
        else:
            return max([True, False], key=lambda a: self.get_q_value(state, a))

    def update_q_value(self, state, action, reward, next_state):
        max_future_q = max([self.get_q_value(next_state, a) for a in [True, False]])
        current_q = self.get_q_value(state, action)
        self.q_table[(state, action)] = current_q + self.alpha * (reward + self.gamma * max_future_q - current_q)

    @staticmethod
    def extract_features(self_decisions, opponent_decisions):
        """
        Using the self_decisions and opponent_decisions as a state space is far too large so this simplifies it massively for the purpose of training an AI
        """
        window_size = min(25, len(opponent_decisions))
        if window_size == 0:
            return (0, 0)

        recent_moves = opponent_decisions[-window_size:]

        return len(recent_moves) - sum(recent_moves), int(len(self_decisions) / 25)

    def action(self, self_decisions, opponent_decisions, s, o, n):
        return self.choose_action(self.extract_features(self_decisions, opponent_decisions))

    def save_model(self, filename="merlin.pkl"):
        with open(filename, "wb") as f:
            pickle.dump(self.q_table, f)

    def load_model(self, filename="merlin.pkl"):
        try:
            with open(filename, "rb") as f:
                self.q_table = pickle.load(f)
        except FileNotFoundError:
            print("No saved model found. Starting fresh.")

    def print_q_table(self):
        for key, value in self.q_table.items():
            print(f"State: {key[0]}, Action: {key[1]} -> Q-Value: {value:.2f}")

