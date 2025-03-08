from merlin import AI_Agent
import importlib.util
import random

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

merlin = AI_Agent()
merlin.load_model()

user_codes['merlin'] = merlin.action

for repeat in range(1000):
    game_length = random.randint(200, 400)
    for player_1 in user_codes.keys():
        for player_2 in user_codes.keys():
            if player_1 == player_2 or "merlin" not in [player_1, player_2]:
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

            if player_1 == "merlin":
                merlin_score, merlin_decisions, opponent_decisions = player_1_score, player_1_decisions, player_2_decisions
            else:
                merlin_score, merlin_decisions, opponent_decisions = player_2_score, player_2_decisions, player_1_decisions

            reward = merlin_score / game_length
            for index, (merlin_decision, opponent_decision) in enumerate(zip(merlin_decisions, opponent_decisions)):
                state = merlin.extract_features(merlin_decisions[:index - 1], opponent_decisions[:index - 1])
                action = merlin_decision
                next_state = merlin.extract_features(merlin_decisions[:index], opponent_decisions[:index])
                merlin.update_q_value(state, action, reward, next_state)

    if repeat % 100 == 0:
        print(f"Finished simulation {repeat}/1000")

merlin.save_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')
