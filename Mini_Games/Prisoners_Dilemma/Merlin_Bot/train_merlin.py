"""
Trains an AI Agent by the name of merlin against all currently submitted user strategies.

Authors: James Aris, Max Worby
"""

from merlin import AI_Agent
import importlib.util
import random
import sys
from datetime import datetime

simulations = int(sys.argv[1])

def sim_2_players(player_1_function, player_2_function, game_length):
    """
    Simulates the game between 2 strategies
    """
    player_1_score, player_2_score = 0, 0
    player_1_decisions, player_2_decisions = [], []
    scores_earned = []

    for _ in range(game_length):
        player_1_decision = bool(player_1_function(player_1_decisions, player_2_decisions, player_1_decisions,
                                              player_2_decisions, len(player_1_decisions)))
        player_2_decision = bool(player_2_function(player_2_decisions, player_1_decisions, player_2_decisions,
                                              player_1_decisions, len(player_1_decisions)))

        s = player_1_score, player_2_score
        if player_1_decision and player_2_decision:
            player_1_score += 5
            player_2_score += 5
        elif player_1_decision and not player_2_decision:
            player_1_score -= 1
            player_2_score += 10
        elif not player_1_decision and player_2_decision:
            player_1_score += 10
            player_2_score -= 1
        scores_earned.append((player_1_score - s[0], player_2_score - s[1]))

        player_1_decisions.append(bool(player_1_decision))
        player_2_decisions.append(bool(player_2_decision))

    return player_1_score, player_2_score, player_1_decisions, player_2_decisions, scores_earned
    # The returned values are all needed for calculating rewards for merlin.

def true_bot(self_decisions, opponent_decisions, s, o, n): return True
def false_bot(self_decisions, opponent_decisions, s, o, n): return False
def random_bot(self_decisions, opponent_decisions, s, o, n): return random.choice([True, False])
def copy_bot(self_decisions, opponent_decisions, s, o, n): return True if n == 0 else o[-1]
def grudge_bot(self_decisions, opponent_decisions, s, o, n): return sum(opponent_decisions) == n

base_strategies = [true_bot, false_bot, random_bot, copy_bot, grudge_bot]

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)
heuristic_highest_scores = dict({})
total_heuristics = dict({})
for i in range(10):
    for user_function in user_codes.values():
        for strategy in base_strategies:
            user_score, strategy_score, _, _, _ = sim_2_players(user_function, strategy, 400)
            if user_function not in heuristic_highest_scores:
                heuristic_highest_scores[user_function] = strategy_score
            elif strategy_score > heuristic_highest_scores[user_function]:
                heuristic_highest_scores[user_function] = strategy_score
    for key in heuristic_highest_scores.keys():
        if key not in total_heuristics:
            total_heuristics[key] = heuristic_highest_scores[key]
        else:
            total_heuristics[key] = heuristic_highest_scores[key]
# Calculates the heuristic score that Merlin "should" achieve against each strategy based on a number of base
# strategies. Rewarding Merlin a similar amount for earning close to 0 points against a copybot (who Merlin should
# have cooperated with) versus a FalseBot (who Merlin should not cooperate with) doesn't make any sense. This ensures
# that the reward schema takes expected scores and ideal strategies into account.

# NOTE: There will never be a strategy that can perform well against all bots. Performing well against a TruthBot and
# a GrudgeBot with the same strategy is impossible.



merlin = AI_Agent(alpha=0.0001,gamma=0.09,epsilon=0,jamesExplore=True,exploration_chance=0.4,exploration_amount=0.005)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')


user_codes['0'] = merlin.action
total_game_reward = 0
for repeat in range(simulations):
    scores = dict({user: 0 for user in user_codes.keys()})
    game_length = 300
    for player_1 in user_codes.keys():
        for player_2 in user_codes.keys():
            if player_1 == player_2 or player_2 != '0':
                continue
            merlin.setExplorationStates()  # reset when to explore
            (player_1_score, player_2_score,
             player_1_decisions, player_2_decisions,
             scores_earned) = sim_2_players(user_codes[player_1], user_codes[player_2], game_length)

            if player_2 == '0':
                game_reward_multiplier = 200
                try:
                    game_reward = player_2_score * game_reward_multiplier / total_heuristics[user_codes[player_1]]
                except ZeroDivisionError:
                    game_reward = player_2_score * game_reward_multiplier / 1000
                # Merlin is rewarded based on his his reward earned in the game compared to how much he could have
                # earned in that game (the heuristic score)
                total_game_reward += game_reward
                for index, (player_1_decision, player_2_decision) in enumerate(zip(player_1_decisions, player_2_decisions)):
                    state = merlin.extract_features(player_2_decisions[:index], player_1_decisions[:index])
                    action = player_2_decision
                    reward = scores_earned[index][1] - scores_earned[index][1]
                    next_state = merlin.extract_features(player_2_decisions[:index + 1], player_1_decisions[:index + 1])
                    merlin.update_q_value(state, action, reward + game_reward, next_state)
                    # For each move, reward Merlin based on his game reward when he made that move.
                    # Rewarding Merlin per move results in a greedy bot that priorities short-term points over long-term
                    # trust.

    if (repeat+1) % 1000 == 0:
        merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
        print(f"Finished simulation {repeat+1}/{simulations}")
        # Update the merlin pkl every 1000 simulations
    if (repeat+1) % 100 == 0:
        with open('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/game_rewards', 'a') as file:
            file.write(f"{total_game_reward/(len(user_codes.keys()) - 1) / 100}\n")
        total_game_reward = 0
        # Update the file that contributes to the rewards.png plot every 100 simulations.

now = datetime.now()
print(now.strftime("%H:%M:%S"))
merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
