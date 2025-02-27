var maxDepth = 1
var evaluating = false
function evaluate(depth = 0,col = 1,max = maxDepth){
    if (depth == 0){
        evaluating = true
    }
    var piecesToCheck = []
    var bestScore = -Infinity
    if (col == 1){
        var color = "blue" 
    } else {
        var color = "red"
    }
    var fen = getBoardFen()
    console.log(fen)
    if (depth != max){
        for(var r = 0; r < squares.length; r++){
            for(var c = 0; c < squares[r].length; c++){
                if (squares[r][c].contains != null){
                    if (squares[r][c].contains.owner == color){
                        piecesToCheck.push(squares[r][c].contains)
                    }
                }
            }
        }
        for(var pieceNo = 0; pieceNo < piecesToCheck.length; pieceNo++){
            var piece = piecesToCheck[pieceNo]
            var pieceSquare = piece.square
            moves = piece.filter(piece.getLegalMoves())
            if(piece.type == "opawn"){
                current = piece
                for (var i = 0; i < moves[0].length; i++){
                    if(moves[0][i].contains == null){
                        current = piece
                        sq = moves[0][i]
                        sq.legal = true
                        sq.button = document.createElement("img")
                        moves[0][i].moves()
                        if(pieceSquare.contains != null){
                            pieceSquare.contains.clearPiece()
                        }
                        moveScore = evaluate(depth + 1,col * -1,fen,max)
                        if (moves[0][i].contains != null){
                            moves[0][i].contains.clearPiece()
                        }
                        recreateBoard(fen)
                        if(moveScore * col > bestScore){
                            bestScore = moveScore
                        }
                    } else {
                        current = piece
                        sq = moves[0][i]
                        sq.legal = true
                        sq.button = document.createElement("img")
                        moves[0][i].moves(false,false,true)
                        if(pieceSquare.contains != null){
                            pieceSquare.contains.clearPiece()
                        }
                        moveScore = evaluate(depth + 1,col * -1,fen,max)
                        if (moves[0][i].contains.owner == color){
                            moves[0][i].contains.clearPiece()
                        }
                        if(moveScore * col > bestScore){
                            bestScore = moveScore
                        }
                    }
                }
                try{
                    for (var i = 0; i < moves[1].length; i++){
                        current = piece
                        pieceSquare = piece.square
                        sq = moves[1][i]
                        sq.legal = true
                        sq.button = document.createElement("img")
                        moves[1][i].moves(true)
                        if(pieceSquare.contains != null){
                            pieceSquare.contains.clearPiece()
                        }
                        moveScore = evaluate(depth + 1,col * -1,fen,max)
                        if (moves[0][i].contains != null){
                            moves[0][i].contains.clearPiece()
                        }
                        recreateBoard(fen)
                        if(moveScore * col > bestScore){
                            bestScore = moveScore
                        }
                    }
                } catch (err){
                    console.log("1: " + err)
                }
                try{
                    for (var i = 0; i < moves[2].length; i++){
                        current = piece
                        sq = moves[2][i]
                        sq.legal = true
                        sq.button = document.createElement("img")
                        moves[2][i].moves(false,true,true)
                        if(pieceSquare.contains != null){
                            pieceSquare.contains.clearPiece()
                        }
                        moveScore = evaluate(depth + 1,col * -1)
                        if (moves[0][i].contains != null){
                            moves[0][i].contains.clearPiece()
                        }
                        recreateBoard(fen)
                        if(moveScore * col > bestScore){
                            bestScore = moveScore
                        }
                    }
                } catch (err) {
                    console.log("2: " + err)
                }
            }
        }
        
        if (depth == 0){
            evaluating = false
        }
        return bestScore*col
    } else {
        var eval = evaluatePosition()
        return eval
    }

}


function evaluatePosition(){
    var eval = 0
    var piecesToCheck = []
    for(var r = 0; r < squares.length; r++){
        for(var c = 0; c < squares[r].length; c++){
            if (squares[r][c].contains != null){
                piecesToCheck.push(squares[r][c].contains)
            }
        }
    }
    for (var p = 0; p < piecesToCheck.length; p++){
        piece = piecesToCheck[p]
        if (piece.owner == "blue"){
            var mult = 1
        } else {
            var mult = -1 
        }
        if (piece.type == "opawn" || (piece.type == "blob" && piece.size == 3)){
            eval += mult
        } else if (piece.type == "blob" && piece.size == 2){
            eval += 2 * mult
        } else if (piece.type == "panda" || piece.type == "chicken"){
            eval += 2 * mult
        } else if (piece.type == "squire" || (piece.type == "blob" && piece.size == 1)){
            eval += 4 * mult
        } else if (piece.type == "rook"){
            eval += 5 * mult
        } else if (piece.type == "frog"){
            eval += 6 * mult
        } else if (piece.type == "dog"){
            eval += 7 * mult
        } else if (piece.type == "blob" && piece.size == 0){
            eval += 8 * mult
        }
    }
    return eval
}
