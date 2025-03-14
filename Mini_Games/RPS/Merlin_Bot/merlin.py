import pickle
import random


class AI_Agent:
    def __init__(self, alpha=0.1, gamma=0.9, epsilon=0.1, jamesExplore = False, exploration_chance = 0.5, exploration_amount=0.05):
        self.q_table = {}
        self.alpha = alpha
        self.gamma = gamma
        self.epsilon = epsilon
        self.jamesExplore = jamesExplore
        self.exploration_chance = exploration_chance
        self.exploration_amount = exploration_amount
        self.explorationStates = []

    def get_q_value(self, state, action):
        return self.q_table.get((state, action), 0.0)

    def choose_action(self, state):
        if random.uniform(0, 1) < self.epsilon:
            # print(f"Exploring from state {state}")
            return random.choice(["Paper", "Scissors", "Rock"])
        elif not self.jamesExplore or state not in self.explorationStates:
            return max([True, False], key=lambda a: self.get_q_value(state, a))
        else:
            return min([True, False], key=lambda a: self.get_q_value(state, a))


    def update_q_value(self, state, action, reward, next_state):
        max_future_q = max([self.get_q_value(next_state, a) for a in [True, False]])
        current_q = self.get_q_value(state, action)
        self.q_table[(state, action)] = current_q + self.alpha * (reward + self.gamma * max_future_q - current_q)

    @staticmethod
    def extract_features(self_decisions, opponent_decisions):
        """
        Using the self_decisions and opponent_decisions as a state space is far too large so this simplifies it massively for the purpose of training an AI
        """

        if len(self_decisions) == 0:
            return 0, 0, 0, 0
        
        o_last, s_last = opponent_decisions[-1], self_decisions[-1]
        n = len(opponent_decisions)
        
        opponent_repeats, self_repeats = 0, 0
        stop = False
        for x in range(1,min(4,len(opponent_decisions)+1)):
            if opponent_decisions[-x] == opponent_decisions[-1] and not stop:
                opponent_repeats += 1
            else:
                stop = True

        stop = False
        for x in range(1,min(4,len(self_decisions)+1)):
            if self_decisions[-x] == self_decisions[-1] and not stop:
                self_repeats += 1
            else:
                stop = True        

        # return tuple(opponent_decisions[-6:] + self_decisions[-2:])
        return opponent_repeats, int(o_last) + 1, int(s_last) + 1, self_repeats, n if n < 5 else 5

    def action(self, self_decisions, opponent_decisions, s=None, o=None, n=None):
        return self.choose_action(self.extract_features(self_decisions, opponent_decisions))

    def setExplorationStates(self): # decide which states to explore on
        if random.random() > self.exploration_chance:
            states = self.q_table.keys()
            self.explorationStates = []
            for i in states:
                if random.uniform(0, 1) < self.exploration_amount:
                    self.explorationStates.append(i[0])
        else:
            self.explorationStates=[]


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
        if not self.q_table:
            print("Q-table is empty. Train the agent first!")
            return

        print("Q table for Merlin (Sorted by Q-value Descending):")

        sorted_q_table = sorted(self.q_table.items(), key=lambda item: item[0], reverse=True)

        for (state, action), value in sorted_q_table:
            print(f"State: {state}, Action: {action} -> Q-Value: {value:.2f}")

