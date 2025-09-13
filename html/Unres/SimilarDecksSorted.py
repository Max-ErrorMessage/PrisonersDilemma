import sys
from pathlib import Path

# Add the module path
sys.path.append(str(Path(__file__).parent / "../../Unres-Meta/elo"))

import similarity_from_matrix

print(similarity_from_matrix.deck_similarity(int(sys.argv[1]), int(sys.argv[2])))