import numpy as np
import importlib.util
import sys

query_id = sys.argv[0]
n = sys.argv[1]

module_path = "/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.py"

spec = importlib.util.spec_from_file_location("merlin", module_path)
merlin_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(merlin_module)
Merlin_Agent = getattr(merlin_module, "AI_Agent", None)

merlin = Merlin_Agent()
merlin.load_model('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/merlin.pkl')


def read_bin(filename, n):
    with open(filename, "rb") as f:
        byte_data = f.read()

    bit_array = np.unpackbits(np.frombuffer(byte_data, dtype=np.uint8))

    return bit_array[:n].astype(bool)


s = read_bin('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/queries/self_decisions_{query_id}.bin', n)
o = read_bin('/var/www/Mini_Games/Prisoners_Dilemma/Merlin_Bot/queries/opponent_decisions_{query_id}.bin', n)

features = merlin.extract_features(s, o)

print("1" if merlin.action(features) else "0")
