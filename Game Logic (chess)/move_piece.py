"""
The point of this file is to allow the SQL server to keep track of the current board state without needing
internal game logic calculations in php or js
"""

import sys
from ChessBoard import *

if __name__ != "__main__":
    quit()

boardstate = sys.argv[1]
white_to_move = sys.argv[2]
move = sys.argv[3]
invulnerable = sys.argv[4] if sys.argv[4] != "0" else None
forced_move = sys.argv[5] if sys.argv[5] != "0" else None

invulnerable = (invulnerable[0], invulnerable[1])
forced_move = (forced_move[0], forced_move[1])

starting_position, ending_position = (int(move[0]), int(move[1])), (int(move[2]), int(move[3]))

board = ChessBoard(boardstate, white_to_move, invulnerable, forced_move)

starting_piece = board.square_in_position(starting_position)

if starting_piece is None:
    raise ValueError("Starting square is empty")

white_to_move = starting_piece.white.is_white

board.white_to_move = white_to_move

board.move_piece(starting_position, ending_position)

forcedMovePosition = board.forced_move.position if issubclass(Piece, type(board.forced_move)) else None
invulnerablePiecePosition = board.invulnerablePiece.position if issubclass(Piece, type(board.invulnerablePiece)) else None

print(board.board_state(), board.white_to_move, board.forced_move, invulnerablePiecePosition, forcedMovePosition)
