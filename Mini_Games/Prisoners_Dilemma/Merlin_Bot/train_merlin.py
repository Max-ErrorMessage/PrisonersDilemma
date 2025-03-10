from merlin import AI_Agent
import importlib.util
import random

def sim_2_players(player_1_function, player_2_function, game_length):
    player_1_score, player_2_score = 0, 0
    player_1_decisions, player_2_decisions = [], []
    scores_earned = []

    for _ in range(game_length):
        player_1_decision = bool(player_1_function(player_1_decisions, player_2_decisions, player_1_decisions,
                                              player_2_decisions, len(player_1_decisions)))
        player_2_decision = bool(player_2_function(player_1_decisions, player_2_decisions, player_1_decisions,
                                              player_2_decisions, len(player_1_decisions)))

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
        scores_earned.append((player_1_score - s[0], player_2_score - s[0]))

        player_1_decisions.append(bool(player_1_decision))
        player_2_decisions.append(bool(player_2_decision))

    return player_1_score, player_2_score, player_1_decisions, player_2_decisions, scores_earned

def true_bot(self_decisions, opponent_decisions, s, o, n): return True
def false_bot(self_decisions, opponent_decisions, s, o, n): return False
def random_bot(self_decisions, opponent_decisions, s, o, n): return random.choice([True, False])
def copy_bot(self_decisions, opponent_decisions, s, o,n): return True if n == 0 else o[-1]
def grudge_bot(self_decisions, opponent_decisions, s, o, n): return sum(opponent_decisions) == n

base_strategies = [true_bot, false_bot, random_bot, copy_bot, grudge_bot]

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

heuristic_highest_scores = dict({})
for user_function in user_codes.values():
    for strategy in base_strategies:
        user_score, strategy_score, _, _, _ = sim_2_players(user_function, strategy, 400)
        if user_function not in heuristic_highest_scores:
            heuristic_highest_scores[user_function] = strategy_score
        elif strategy_score > heuristic_highest_scores[user_function]:
            heuristic_highest_scores[user_function] = strategy_score

merlin = AI_Agent()
merlin.load_model()

user_codes['0'] = merlin.action

for repeat in range(10000):
    scores = dict({user: 0 for user in user_codes.keys()})
    game_length = 20
    for player_1 in user_codes.keys():
        for player_2 in user_codes.keys():
            if player_1 == player_2 or '0' not in [player_1, player_2]:
                continue

            (player_1_score, player_2_score,
             player_1_decisions, player_2_decisions,
             scores_earned) = sim_2_players(user_codes[player_1], user_codes[player_2], game_length)

            if player_2 == '0':
                try:
                    game_reward = player_2_score / (heuristic_highest_scores[user_codes[player_1]]
                except ZeroDivisionError:
                    game_reward = max(player_2_score, 1)
                # print(f"Merlin is being rewarded by {reward} for scoring {player_2_score} against {player_1}, whose "
                #       f"heuristic highest score is {heuristic_highest_scores[user_codes[player_1]]}")
                for index, (player_1_decision, player_2_decision) in enumerate(zip(player_1_decisions, player_2_decisions)):
                    state = merlin.extract_features(player_1_decisions[:index - 1], player_2_decisions[:index - 1])
                    action = player_1_decision
                    reward = 3 if scores_earned[index - 1] > 0 else -5
                    next_state = merlin.extract_features(player_1_decisions[:index], player_2_decisions[:index])
                    merlin.update_q_value(state, action, reward, next_state)
                    merlin.update_q_value(state, action, game_reward, next_state)


    if repeat % 1000 == 0:
        print(f"Finished simulation {repeat}/10000")

merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
