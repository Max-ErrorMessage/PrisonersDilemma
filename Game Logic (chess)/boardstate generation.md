## How the fen string (or board state) is generated
The algorithm that generates the fen (which is used to store the current game position), and how the characters are generated.
<br><br><br>

### The board_state() function attached to the ChessBoard object
```python
def board_state(self):
    """
    The board_state is a 100 character string representing the current board.
    Each piece type has a unique letter.
    Black Pieces are uppercase and White Pieces are lowercase.
    There is a unique letter for pieces like an immortal panda or chicken,
     or a pawn who is enpassantable on the next move.
    """
    board_state = ""
    for row in self.squares:
        for square in row:
            if issubclass(type(square), Piece):
                board_state += square.as_char()
            else:
                board_state += " "
    return board_state
```

Because the programs that are used for game logic both use this function, this is the way in which the other programs interact with the board and game logic. All the relvant information about the board except whose turn it is can be stored with this function:
1. Basic pieces on the board
2. Eggs and their crack stages
3. Some pieces care if they have moved before in the game (Pawns, Chickens, Kings, Rooks)
4. If the boardstate fen is calculated after a dog (or a chicken next to a dog) has just taken a piece, the boardstate will include the information

### Initial Boardstate
The starting boardstate is "RDOZAKCFDRPPPPPPPPPP                                                            pppppppppprdozakcfdr"

### Piece -> Character conversion:
| Piece   | Colour | Special Properties (if any) | Character |
|---------|--------|-----------------------------|-----------|
| Pawn    | 1      | none                        | p         |
| Pawn    | 1      | en passantable              | e         |
| Pawn    | 1      | moved                       | /         |
| Pawn    | 0      | none                        | P         |
| Pawn    | 0      | en passantable              | E         |
| Pawn    | 0      | moved                       | ?         |
| Rook    | 1      | none                        | r         |
| Rook    | 1      | moved                       | m         |
| Rook    | 0      | none                        | R         |
| Rook    | 0      | moved                       | M         |
| King    | 1      | none                        | k         |
| King    | 1      | moved                       | (         |
| King    | 0      | none                        | K         |
| King    | 0      | moved                       | )         |
| Panda   | 1      | none                        | a         |
| Panda   | 1      | immortal                    | I         |
| Panda   | 0      | none                        | A         |
| Panda   | 0      | immortal                    | I         |
| Frog    | 1      | none                        | f         |
| Frog    | 0      | none                        | F         |
| Dog     | 1      | none                        | d         |
| Dog     | 0      | none                        | D         |
| Chicken | 1      | none                        | o         |
| Chicken | 1      | none                        | O         |
| Chicken | 1      | moved                       | h         |
| Chicken | 1      | moved                       | H         |
| Blob0   | 1      | none                        | z         |
| Blob0   | 0      | none                        | Z         |
| Blob1   | 1      | none                        | x         |
| Blob1   | 0      | none                        | X         |
| Blob2   | 1      | none                        | v         |
| Blob2   | 0      | none                        | V         |
| Blob3   | 1      | none                        | j         |
| Blob3   | 0      | none                        | J         |
| Cleric  | 1      | none                        | c         |
| Cleric  | 0      | none                        | C         |
| Egg     | 1      | 6 ticks until hatch         | 5         |
| Egg     | 1      | 5 ticks until hatch         | 4         |
| Egg     | 1      | 4 ticks until hatch         | 3         |
| Egg     | 1      | 3 ticks until hatch         | 2         |
| Egg     | 1      | 2 ticks until hatch         | 1         |
| Egg     | 1      | 1 ticks until hatch         | 0         |
| Egg     | 0      | 5 ticks until hatch         | %         |
| Egg     | 0      | 4 ticks until hatch         | $         |
| Egg     | 0      | 3 ticks until hatch         | \         |
| Egg     | 0      | 2 ticks until hatch         | “         |
| Egg     | 0      | 1 ticks until hatch         | !         |

_Note: Due to the complex nature of the Chicken piece and its tendency to change behaviours based on how it has recently interacted with the board, the Chicken character is calculated somewhere else._
_Because of the nature of the code, the piece objects have no knowledge of the rest of the board. For this reason, the dog only has one character in this environment._

