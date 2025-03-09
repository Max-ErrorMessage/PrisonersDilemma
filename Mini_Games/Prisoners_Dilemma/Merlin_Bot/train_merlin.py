from merlin import AI_Agent
import importlib.util
import random
import numpy as np

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

merlin = AI_Agent()
merlin.load_model()

user_codes[0] = merlin.action

for repeat in range(1000):
    scores = dict({user: 0 for user in user_codes.keys()})
    merlin_games = []
    game_length = random.randint(200, 400)
    for player_1 in user_codes.keys():
        for player_2 in user_codes.keys():
            if player_1 <= player_2:
                continue

            player_1_score, player_2_score = 0, 0
            player_1_decisions, player_2_decisions = [], []
            player_1_function, player_2_function = user_codes[player_1], user_codes[player_2]

            for _ in range(game_length):
                player_1_decision = player_1_function(player_1_decisions, player_2_decisions, player_1_decisions,
                                                      player_2_decisions, len(player_1_decisions))
                player_2_decision = player_2_function(player_1_decisions, player_2_decisions, player_1_decisions,
                                                      player_2_decisions, len(player_1_decisions))

                scores_before = player_1_score, player_2_score

                if player_1_decision and player_2_decision:
                    player_1_score += 5
                    player_2_score += 5
                elif player_1_decision and not player_2_decision:
                    player_1_score -= 1
                    player_2_score += 10
                elif not player_1_decision and player_2_decision:
                    player_1_score += 10
                    player_2_score -= 1

                player_1_decisions.append(player_1_decision)
                player_2_decisions.append(player_2_decision)

            scores[player_1] += player_1_score
            scores[player_2] += player_2_score

            if player_1 == 0:
                merlin_games.append(zip(player_1_decisions, player_2_decisions))

    score_values = np.array(list(scores.values()))

    mean_score = np.mean(score_values)
    std_dev = np.std(score_values)
    z_scores = {user_id: (score - mean_score) / std_dev for user_id, score in scores.items()}

    reward = z_scores[0]

    for game in merlin_games:
        for index, (player_1_decision, player_2_decision) in game:
            state = merlin.extract_features(game[0][:index - 1], game[1][:index - 1])
            action = player_1_decision
            next_state = merlin.extract_features(game[0][:index], game[1][:index])
            merlin.update_state(state, action, reward, next_state)


    if repeat % 100 == 0:
        print(f"Finished simulation {repeat}/1000")

merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
