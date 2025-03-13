from merlin import AI_Agent
import importlib.util
import random
import sys
from datetime import datetime
import matplotlib.pyplot as plt

simulations = int(sys.argv[1])

def sim_2_players(player_1_function, player_2_function, game_length):
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
    #heuristic_highest_scores = dict({})
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


# for i in total_heuristics.keys():
#     total_heuristics[i]/=1
# for i in user_codes.keys():
#     print(f"{i}: {total_heuristics[user_codes[i]]}")


merlin = AI_Agent(alpha=0.0001,gamma=0.09,epsilon=0,jamesExplore=True,exploration_chance=0.4,exploration_amount=0.005)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')


user_codes['0'] = merlin.action
highest_trust_count = 0
perfect_trust_rounds = 0
total_rounds_played = 0
plt_trusts = [] # these lists are for my (james) own sake, q_value for state (3,2,2,3) fluctuates a lot
plt_betrays = [] # i'd like to plot this on a graph, my hypothesis is like a sin/cos graph vibe
test_print = False
total_game_reward = 0
for repeat in range(simulations):
    scores = dict({user: 0 for user in user_codes.keys()})
    game_length = 200
    for player_1 in user_codes.keys():
        for player_2 in user_codes.keys():
            if player_1 == player_2 or player_2 != '0':
                continue
#            test_print = False
            merlin.setExplorationStates() # reset when to explore
            (player_1_score, player_2_score,
             player_1_decisions, player_2_decisions,
             scores_earned) = sim_2_players(user_codes[player_1], user_codes[player_2], game_length)

            if player_2 == '0':
#                if (player_1 == '1' or player_1 == '76') and player_2_decisions[:3] == [True,False,False] and player_1_decisions[1:3] == [False,True]:
#                    test_print = True
                total_rounds_played += 1
                game_reward_multiplier = 1.5
                try:
                    game_reward = player_2_score * game_length * game_reward_multiplier / total_heuristics[user_codes[player_1]]
                except ZeroDivisionError:
                    game_reward = player_2_score * game_length * game_reward_multiplier / 1000
                total_game_reward += game_reward
                # print(f"Merlin is being rewarded by {reward} for scoring {player_2_score} against {player_1}, whose "
                #       f"heuristic highest score is {heuristic_highest_scores[user_codes[player_1]]}")
                # print(f"Player 1 deicsions: {player_1_decisions}\nPlayer 2 decisions: {player_2_decisions}")
                for index, (player_1_decision, player_2_decision) in enumerate(zip(player_1_decisions, player_2_decisions)):
                    state = merlin.extract_features(player_2_decisions[:index], player_1_decisions[:index])
                    action = player_2_decision
#                    if test_print and state == (1,2,1,2,3):
#                        print(f"{player_1}: {action}, {round(game_reward,2)}, {player_2_score}")
                    reward = scores_earned[index][1] - scores_earned[index][1]
#                    if player_1_decision and not player_2_decision:
#                        reward -= 5
                    next_state = merlin.extract_features(player_2_decisions[:index + 1], player_1_decisions[:index + 1])
                    # print(f"Q Value of state {state} before updating: {merlin.get_q_value(state, action)}")
                    merlin.update_q_value(state, action, reward + game_reward, next_state)
                    # print(f"Q Value of state {state} after updating: {merlin.get_q_value(state, action)}")
                    # print(f"Index: {index}, State: {state}, Action: {action}, Reward: {reward}, Game Reward: {game_reward}, Next State: {next_state}")
                    # merlin.update_q_value(state, action, game_reward, next_state)
                #print(sum(player_2_decisions))
#                if sum(player_2_decisions) > highest_trust_count:
#                    highest_trust_count = sum(player_2_decisions)
#                if sum(player_2_decisions) == game_length:
#                    perfect_trust_rounds += 1
#                plt_trusts.append(merlin.get_q_value((1,2,1,2,3),True))
#                plt_betrays.append(merlin.get_q_value((1,2,1,2,3),False))
#                print(player_1_decisions)
#                print(player_2_decisions)
#                print("-")
#    if (repeat+1) % 500 == 0:
#        print(merlin.explorationStates)
#        print(highest_trust_count)
#        print(f"{perfect_trust_rounds}/{total_rounds_played} rounds where neither player betrayed")
#        print(player_2_decisions)
#        print("-=-=-=-=-=-=-")

    if (repeat+1) % 1000 == 0:
        merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
        print(f"Finished simulation {repeat+1}/{simulations}")
    if (repeat+1) % 100 == 0:
        with open('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/game_rewards', 'a') as file:
            file.write(f"{total_game_reward/(len(user_codes.keys()) - 1) / 100}\n")
        total_game_reward = 0
#    merlin.trustyness('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/trustyness')

now = datetime.now()
print(now.strftime("%H:%M:%S"))
merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
