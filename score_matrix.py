import matplotlib.pyplot as plt
import random
import numpy as np
import seaborn as sns
import importlib.util


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

    return player_1_score, player_2_score


merlin_path = "/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.py"

spec = importlib.util.spec_from_file_location("merlin", merlin_path)
merlin_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(merlin_module)
Merlin_Agent = getattr(merlin_module, "AI_Agent", None)

merlin = Merlin_Agent(epsilon=0)
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py"

spec = importlib.util.spec_from_file_location("user_codes", module_path)
user_codes_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(user_codes_module)

user_codes = getattr(user_codes_module, "user_code", None)

user_codes['0'] = merlin.action

user_codes = dict(sorted(user_codes.items(), key=lambda item: int(item[0])))

score_matrix = np.zeros((len(user_codes), len(user_codes)))

game_length = random.randint(200, 400)
for index, player_1 in enumerate(user_codes.keys()):
    for jindex, player_2 in enumerate(user_codes.keys()):
        if player_1 < player_2:
            continue
        player_1_score, player_2_score = tuple(round(score / game_length, 2) for score in sim_2_players(user_codes[player_1], user_codes[player_2], game_length))
        score_matrix[index][jindex] = player_1_score
        score_matrix[jindex][index] = player_2_score

plt.figure(figsize=(200, 200))
plt.style.use('dark_background')
plt.figure(figsize=(6,5))
ax = sns.heatmap(score_matrix, cmap="Greens", center=0, linewidths=0.5, fmt=".2f")
plt.title("Matchups between players")
ax.set_xticklabels(user_codes.keys())
ax.set_yticklabels(user_codes.keys())

plt.savefig("/var/www/html/score_matrix.png", dpi=300, bbox_inches='tight')
