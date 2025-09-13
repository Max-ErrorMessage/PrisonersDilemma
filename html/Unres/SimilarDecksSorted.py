import sys
sys.path.append('../../Unres-Meta/elo')  # path to the folder containing similarity_from_matrix.py

import similarity_from_matrix

print(deck_similarity(sys.argv[1], sys.argv[2]))