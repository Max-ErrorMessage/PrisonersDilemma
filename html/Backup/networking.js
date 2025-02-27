
flipped = false

function blue(){

    $.ajax({
        type: 'POST',
        url: 'update_session.php',
        data: {color: 'blue'},
        
    });

    color = "blue"
    $.post("postTeam.php", { text: color });
    if (flipped == true){
        flipBoard()
    }
}

function red(){
    
    $.ajax({
        type: 'POST',
        url: 'update_session.php',
        data: {color: 'red'},
        
    });

    color = "red"
    $.post("postTeam.php", { text: color });
    if (flipped == false){
        flipBoard()
    }
}

function loadBoard(){
    if (evaluating == false){
        if (redKing != null){
            checkUpdateCheck()
        }
        if(turn != color || turn == null){
            $.post('getGameState.php', function(gamestate) {
                removeAllPieces()
                recreateBoard(gamestate.boardFen)
                turn = gamestate.playerTurn
                if (turn == "red"){
                    turnEnemy = "blue"
                } else {
                    turnEnemy = "red"
                }
                if (gamestate.immortalRow != "null"){
                    immortal = squares[gamestate.immortalRow][gamestate.immortalCol].contains
                } else {
                    immortal == "null"
                }
                if (gamestate.enpassantRow != "null"){
                    enPiece = squares[gamestate.enpassantRow][gamestate.enpassantCol].contains
                    if (enPiece != null){
                        enpassantablePiece = enPiece
                    } else {
                        enpassantablePiece = false
                    }
                } else {
                    enpassantablePiece == "null"
                }
                var rowstring = gamestate.highlightRow + ""
                var colstring = gamestate.highlightCol + ""
                if (rowstring!= null){
                    highlightedSquares = []
                    for(var i = 0; i < rowstring.length;i++){
                        highlightedSquares.push(squares[rowstring[i]][colstring[i]])
                    }
                    highlightSquares()
                }
            })
        }
    }
}

function update(){
    if (turn != null){
        var fen = getBoardFen()
        if (immortal == null){
            var imRow = "null"
            var imCol = "null"
        } else {
            var imRow = immortal.row
            var imCol = immortal.col
        }
        if (enpassantablePiece == false ||enpassantablePiece == null){
            var enRow = "null"
            var enCol = "null"
        } else {
            var enRow = enpassantablePiece.row
            var enCol = enpassantablePiece.col
        }
        if (enRow == null){
            enRow = "null"
            enCol = "null"
        }
        highlightedRows = ""
        highlightedCols = ""
        for(var i = 0; i < highlightedSquares.length;i++){
            highlightedRows += highlightedSquares[i].row + ""
            highlightedCols += highlightedSquares[i].col + ""
        }
        $.post('gameState.php', {newFen: fen, newTurn:turn, immortalRow: imRow, immortalCol: imCol, enpassantRow: enRow, enpassantCol: enCol, highlightRow:highlightedRows, highlightCol:highlightedCols});
    }
}

//--------------------------------------------------------------------------------------------
function removeAllPieces(){
    pieces = []
    eggs = []
    for(var r = 0; r < squares.length; r++){
        for(var c = 0; c < squares[r].length; c++){
            if (squares[r][c].contains != null){
                squares[r][c].contains.removePiece()
            }
        }
    }
}

function getBoardFen(){
    var fen = ""
    for(var row = 0; row < squares.length; row++){
        var blanks = 0
        for(var col = 0; col < squares.length; col++){
            if (squares[row][col].contains == null){
                blanks += 1
            } else {
                if (blanks != 0){
                    fen += blanks
                }
                blanks = 0
                var char = squares[row][col].contains.type[0]
                if (char == "b"){
                    var size = squares[row][col].contains.size
                    if (size != 0){
                        if (size == 1){
                            char = "x"
                        } else if (size == 2){
                            char = "y"
                        } else if (size == 3){
                            char = "z"
                        }
                    }
                }
                if (char == "e"){
                    var size = squares[row][col].contains.size
                    if (size != 0){
                        if (size == 1){
                            char = "w"
                        } else if (size == 2){
                            char = "v"
                        } else if (size == 3){
                            char = "u"
                        } else if (size == 4){
                            char = "t"
                        }
                    }
                }
                if (squares[row][col].contains.owner == "red"){
                    fen += char.toUpperCase()
                } else {
                    fen += char.toLowerCase()
                }
                
                
            }
        }
        if (blanks != 0){
            fen += blanks
        }
        fen += "/"
    }
    return fen
}

function recreateBoard(fen = 'RDCBPKSFDR/OOOOOOOOOO/10/10/10/10/10/10/oooooooooo/rdcbpksfdr/'){
    var origFen = fen
    turn = "blue"
    highlightedSquares = []
    removeAllPieces()
    for(var row = 0; row < squares.length; row++){
        for(var col = 0; col < squares.length; col++){
            var sq = squares[row][col]
            char = fen[0]
            fen = fen.substring(1)
            if (char == "o"){
                sq.placePiece(new BluePawn(0,0,0))
            } else if (char == "O"){
                sq.placePiece(new RedPawn(0,0,0))
            } else if (char == "r"){
                sq.placePiece(new BlueRook(0,0,0))
            } else if (char == "R"){
                sq.placePiece(new RedRook(0,0,0))
            } else if (char == "d"){
                sq.placePiece(new BlueDog(0,0,0))
            } else if (char == "D"){
                sq.placePiece(new RedDog(0,0,0))
            } else if (char == "c"){
                sq.placePiece(new BlueChicken(0,0,0))
            } else if (char == "C"){
                sq.placePiece(new RedChicken(0,0,0))
            } else if (char == "b"){
                sq.placePiece(new BlueBlob0(0,0,0))
            } else if (char == "B"){
                sq.placePiece(new RedBlob0(0,0,0))
            } else if (char == "x"){
                sq.placePiece(new BlueBlob1(0,0,0))
            } else if (char == "X"){
                sq.placePiece(new RedBlob1(0,0,0))
            } else if (char == "y"){
                sq.placePiece(new BlueBlob2(0,0,0))
            } else if (char == "Y"){
                sq.placePiece(new RedBlob2(0,0,0))
            } else if (char == "z"){
                sq.placePiece(new BlueBlob3(0,0,0))
            } else if (char == "Z"){
                sq.placePiece(new RedBlob3(0,0,0))
            } else if (char == "p"){
                sq.placePiece(new BluePanda(0,0,0))
            } else if (char == "P"){
                sq.placePiece(new RedPanda(0,0,0))
            } else if (char == "k"){
                sq.placePiece(new BlueKing(0,0,0))
            } else if (char == "K"){
                sq.placePiece(new RedKing(0,0,0))
            } else if (char == "s"){
                sq.placePiece(new BlueSquire(0,0,0))
            } else if (char == "S"){
                sq.placePiece(new RedSquire(0,0,0))
            } else if (char == "f"){
                sq.placePiece(new BlueFrog(0,0,0))
            } else if (char == "F"){
                sq.placePiece(new RedFrog(0,0,0))
            } else if (char == "e"){
                sq.placePiece(new BlueEgg0(0,0,0))
            } else if (char == "E"){
                sq.placePiece(new RedEgg0(0,0,0))
            } else if (char == "w"){
                sq.placePiece(new BlueEgg1(0,0,0))
            } else if (char == "W"){
                sq.placePiece(new RedEgg1(0,0,0))
            } else if (char == "v"){
                sq.placePiece(new BlueEgg2(0,0,0))
            } else if (char == "V"){
                sq.placePiece(new RedEgg2(0,0,0))
            } else if (char == "u"){
                sq.placePiece(new BlueEgg3(0,0,0))
            } else if (char == "U"){
                sq.placePiece(new RedEgg3(0,0,0))
            } else if (char == "t"){
                sq.placePiece(new BlueEgg4(0,0,0))
            } else if (char == "T"){
                sq.placePiece(new RedEgg4(0,0,0))
            } else if (char == "1"){
                if (fen[0] == "0"){
                    fen = fen.substring(1)
                    fen = "9" + fen
                }
            } else {
                newChar = char - 1
                fen = newChar + fen
            }
            

        }
        
        if(fen[0] == "/"){
            fen = fen.substring(1)
        }
    }
    if(origFen == 'RDCBPKSFDR/OOOOOOOOOO/10/10/10/10/10/10/oooooooooo/rdcbpksfdr/'){
        update()
    }
}



function flipBoard(){
    unmark()
    if (flipped == false){
        flipped = true
        for(var row = 0; row < squares.length; row ++){
                for (var col = 0; col < squares[row].length; col ++){
                
                square = squares[row][col]
                square.div.style.top = 64 * (9-row) + "px"
                square.div.style.left = 64 * (9-col) + "px"
            }
        }
    } else {
        flipped = false
        for(var row = 0; row < squares.length; row ++){
                for (var col = 0; col < squares[row].length; col ++){
                
                square = squares[row][col]
                square.div.style.top = 64 * (row) + "px"
                square.div.style.left = 64 * (col) + "px"
            }
        }

    }
}

function checkUpdateCheck(){
    isCheck = false
    var oldCheck = document.getElementById("check")
    if (oldCheck != null){
        oldCheck.remove()
    }
    if (turn == "blue"){
        var kingRow = blueKing.row
        var kingCol = blueKing.col 
        var king = blueKing
    } else {
        var kingRow = redKing.row
        var kingCol = redKing.col
        var king = redKing
    }
    var isMate = false
    if(checkChecks(king) == true){
        var check = document.createElement("img")
        check.src = "check.png"
        check.style.pointerEvents = "none"
        check.id = "check"
        squares[kingRow][kingCol].div.appendChild(check)   
        if (king.checkStuck() == true){
            var foundMove = false
            for (var r = 0; r < squares.length; r ++){
                for (var c = 0; c < squares[r].length; c++){
                    if (squares[r][c].contains != null && squares[r][c].contains.owner == turn){
                        if (squares[r][c].contains.checkStuck() == false){
                            foundMove = true
                        } 
                    }
                }
            }
            if (foundMove == false){
                console.log(turnEnemy + " wins!")
                isMate = true
            }
        } 
    }
}