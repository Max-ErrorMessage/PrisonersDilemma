"""
The point of this command is that it takes 3 arguments in the command line:
- Board state as str
- White to Move (0/1)
- Invulnerable piece 0 or 00-99
- Forced Move Piece 0 or 00-99
- Location on the board (00 - 99)

The file will then return all valid positions for movement in that position from that location
"""

from ChessBoard import *
import sys

boardstate = sys.argv[1]
white_to_move = True if sys.argv[2] == "1" else False


invulnerable = (int(sys.argv[3][0]), int(sys.argv[3][1])) if sys.argv[3] != "0" else None
forced_move = (int(sys.argv[4][0]), int(sys.argv[4][1])) if sys.argv[4] != "0" else None

location = int(sys.argv[5][0]), int(sys.argv[5][1])

board = ChessBoard(boardstate, white_to_move, invulnerable, forced_move)

moves = board.legal_moves(location)
moves.sort()

[print(str(move[0]) + str(move[1])) for move in moves]
