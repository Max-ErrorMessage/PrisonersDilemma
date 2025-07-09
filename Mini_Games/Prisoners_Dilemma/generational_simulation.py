from itertools import combinations
import matplotlib.pyplot as plt
import importlib.util
import random
import statistics

def plot_populations(populations_over_time):
    plt.clf()

    for label, values in populations_over_time.items():
        plt.plot(values, label=label)

    plt.title("Success of strategies over time")
    plt.xlabel("Generation")
    plt.ylabel("Population of the strategy")

    plt.legend(fontsize='small', loc='upper left', bbox_to_anchor=(1, 1))

    plt.savefig("/var/www/html/generations.png", dpi=300, bbox_inches='tight')

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

# def true_bot(self_decisions, opponent_decisions, s, o, n): return True
# def false_bot(self_decisions, opponent_decisions, s, o, n): return False
# def random_bot(self_decisions, opponent_decisions, s, o, n): return random.choice([True, False])
# def copy_bot(self_decisions, opponent_decisions, s, o, n): return True if n == 0 else o[-1]
# def copy_bot_leading_false(self_decisions, opponent_decisions, s, o, n): return False if n == 0 else o[-1]
# def grudge_bot(self_decisions, opponent_decisions, s, o, n): return sum(opponent_decisions) == n
#
# user_codes = {'0': true_bot, '1': false_bot, '2': random_bot, '3': copy_bot, '4': copy_bot_leading_false, '5': grudge_bot}

populations = {user_id: 1000 for user_id in user_codes.keys()}
scores = {user_id: 0 for user_id in user_codes.keys()}
populations_over_time = {user_id: [1000] for user_id in user_codes.keys()}
population_stabilised = False
generation = 0
while not population_stabilised:
    generation += 1
    initial_populations = populations.copy()
    for user in populations.keys():
        for opponent in populations.keys():
            self_decisions, opponent_decisions = [], []
            for round in range(300):
                self_decision = bool(user_codes[user](self_decisions, opponent_decisions, self_decisions, opponent_decisions, len(self_decisions)))
                opponent_decision = bool(user_codes[opponent](opponent_decisions, self_decisions, opponent_decisions, self_decisions, len(opponent_decisions)))
               
                if self_decision and opponent_decision:
                    scores[user] += 5 * populations[opponent]
                    scores[opponent] += 5 * populations[user]
                elif self_decision and not opponent_decision:
                    scores[user] -= 1 * populations[opponent]
                    scores[opponent] += 10 * populations[user]
                elif not self_decisions and opponent_decision:
                    scores[user] += 10 * populations[opponent]
                    scores[opponent] -= 1 * populations[user]

                self_decisions.append()
                opponent_decisions.append(opponent_decision)

    for user in scores.keys():
        scores[user] /= populations[user]

    mean_score = sum(scores.values()) / len(scores)
    median_score = statistics.median(scores.values())

    for user in user_codes.keys():
        if user not in populations.keys():
            continue
        population_multiplier = scores[user] / mean_score
        populations[user] *= population_multiplier ** (1/5)

        populations_over_time[user].append(int(populations[user]))

        if populations[user] <= 10 and False:
            del populations[user]
            del scores[user]

    population_stabilised = True
    for user, pop in populations.items():
        if abs(initial_populations[user] - populations[user]) > 0.5 or True:
            population_stabilised = False
            break

    print(f"Completed generation {generation}")

    if generation % 10 == 0:
        plot_populations(populations_over_time)
        pop = dict(sorted(populations.items(), key=lambda item: float(item[1]), reverse=False))
        top_user = list(pop.items())[0][0]
        print(f"Removing the bottom user: {top_user}")
        del scores[top_user]
        del populations[top_user]

plot_populations(populations_over_time)
