import sys
from pathlib import Path

sys.path.append(str(Path(__file__).parent / "../../Unres-Meta/elo"))

import similarity_from_matrix
import importlib.util

def deck_ids():
  spec = importlib.util.spec_from_file_location("connect_to_db", "/var/www/Unres-Meta/db/connect_to_db.py")
  connect_to_db = importlib.util.module_from_spec(spec)
  sys.modules["connect_to_db"] = connect_to_db
  spec.loader.exec_module(connect_to_db)

  cursor = connect_to_db.cursor
  conn = connect_to_db.conn

  deck_ids = dict()

  cursor.execute(f"SELECT id, name, url FROM decks")
  rows = cursor.fetchall()

  for deck_id, url, name in rows:
    deck_ids[deck_id] = (name, url)

  return deck_ids

ids = deck_ids()
for d_id in ids:
    print(str(ids[d_id]) + ": " + str(similarity_from_matrix.deck_similarity(sys.argv[1],id)))