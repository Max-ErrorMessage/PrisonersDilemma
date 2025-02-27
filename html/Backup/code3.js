var rowA = []
var squares = []
current = null
enpassantablePiece = false
rowMax = 9
colMax = 9
turn = null
turnEnemy = null
dogTime = null
pieces = []
isCheck = false
immortal = null
turns = []
files = ["a","b","c","d","e","f","g","h","i","j"]
turnNo = 1
color = null
redKing = null
blueKing = null
over = false
highlightedSquares = []
doghighlight = null
var counter1 = 0
var counter2 = 0
document.addEventListener('contextmenu', event => event.preventDefault());
eggs = []

class Square {
    constructor(row,col,div,contains){
        this.row = row
        this.col = col

        this.div = div
        this.contains = null
        this.legal = false
        this.marked = false
        
        this.div.onclick = function jsFunc() {clearIfNull(row,col)}
        this.div.oncontextmenu = function jsFunc() {markSquare(row,col)}
    }

    placePiece(piece){
        piece.element = document.createElement("img")
        piece.element.src = piece.img
        piece.element.onclick = function jsFunc() {piece.move()}
        this.div.appendChild(piece.element)
        piece.row = this.row
        piece.col = this.col
        this.contains = piece
        piece.square = this
    }

    legalMove(enpassantable = false, chance = 1){
        if (this.checkMove(current) == true){
            this.legal = true
            this.button = document.createElement("img")
            this.button.src = "button.png"
            var squareBoy = this
            this.button.onclick = function jsFunc() {squareBoy.moves(enpassantable,true,false,false,chance)}
            this.div.appendChild(this.button)
        }
    }

    legalSquireMove(enpassantable = false){
        if (this.checkMove(current) == true){
            this.legal = true
            this.button = document.createElement("img")
            this.button.src = "bitchopButton.png"
            //this.button.style.top = this.row * 64 + "px"
            //this.button.style.left = this.col * 64 + "px"
            var squareBoy = this
            this.button.onclick = function jsFunc() {squareBoy.moves(enpassantable,true,false,false,1)}
            this.div.appendChild(this.button)
        }
    }

    legalTake(enpassant = false, blob = false,chance = 1){
        if (this.checkMove(current) == true && this.contains != immortal){
            if (enpassant == false){
                this.legal = true
                this.button = document.createElement("img")
                this.button.src = "ring.png"
                var squareBoy = this
                this.button.onclick = function jsFunc() {squareBoy.moves(false,enpassant,true,blob,chance)}
                //this.button.style.top = this.row * 64 + "px"
                //this.button.style.left = this.col * 64 + "px"
                this.div.appendChild(this.button)
            } else {
                var enpassantedPiece = this.contains
                if(current.owner == "blue"){
                    var square = squares[current.row - 1][this.col]
                } else {
                    var square = squares[current.row + 1][this.col]
                }
                square.legal = true
                square.button = document.createElement("img")
                square.button.src = "ring.png"
                square.button.onclick = function jsFunc() {square.moves(false,enpassant,true,blob,chance,enpassantedPiece)}
                squares[square.row][square.col].div.appendChild(square.button)
            }
        }
    }

    legalDogTake(enpassant = false, blob = false){
        if (current.checkStuck2(this) == false && this.contains != immortal){
            if(enpassant == false){
                this.legal = true
                this.button = document.createElement("img")
                this.button.src = "dogRing.png"
                var squareBoy = this
                this.button.onclick = function jsFunc() {squareBoy.dogMoves(enpassant,blob,enpassantedPiece)}
                this.div.appendChild(this.button)
            } else {
                var enpassantedPiece = this.contains
                if(current.owner == "blue"){
                    var square = squares[current.row - 1][this.col]
                } else {
                    var square = squares[current.row + 1][this.col]
                }
                square.legal = true
                square.button = document.createElement("img")
                square.button.src = "dogRing.png"
                square.button.onclick = function jsFunc() {square.dogMoves(enpassant,blob,enpassantedPiece)}
                squares[square.row][square.col].div.appendChild(square.button)
            }
        } else {
            //console.log(this.checkMove(current) + " " + current.checkStuck2(this) + " " + this.contains)
        }
    }

    clear(){
        if (this.legal == true){
            this.button.remove()
            this.legal = false
        }
    }

    moves(enpassantable = false, enpassant = false, takes = false,blob = false,chance = 1, enpassantedPiece = null){
        highlightedSquares = [current.square]
        if (dogTime == null){
            crackEggs()
        }
        if (doghighlight != null){
            highlightedSquares.push(doghighlight)
            doghighlight = null
        }
        if (turn == "red"){
            var kingRow = blueKing.row
            var kingCol = blueKing.col 
            var king = blueKing
        } else {
            var kingRow = redKing.row
            var kingCol = redKing.col
            var king = redKing
        }
        var fromRow = current.row
        var fromCol = current.col
        if (immortal != null && immortal.owner != current.owner){
            immortal = null
        }
        clearLegals()
        var n = Math.random()
        if (n < chance || isCheck == true){
            counter1 ++
            if (enpassantable == true){
                enpassantablePiece = current
            } else {
                enpassantablePiece = false
            }
            //resets dogtime
            if (dogTime != null){
                dogTime = null
            }

            if (blob == false){
                 current.square.contains = null
                 current.clearPiece()
            }
            current.moved = true
            

            if (takes == true){
                if (current.type == "panda" || (current.type == "chicken" && current.types.includes("panda"))){
                    immortal = current
                }
                //en-passant
                if (enpassant == false){
                    this.contains.removePiece()
                    if (blob == false){
                        this.placePiece(current)
                    } else {
                        if (turn == "red"){
                            if (current.type == "chicken"){
                                this.placePiece(new RedChicken(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new RedEgg0(0,0,this))
                            } else if (current.size == 0){
                                this.placePiece(new RedBlob1(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new RedBlob1(0,0,this)) 
                            } else if (current.size == 1){
                                this.placePiece(new RedBlob2(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new RedBlob2(0,0,this)) 
                            } else if (current.size == 2){
                                this.placePiece(new RedBlob3(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new RedBlob3(0,0,this)) 
                            } 
                        } else {
                            if (current.type == "chicken"){
                                this.placePiece(new BlueChicken(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new BlueEgg0(0,0,this))
                            } else if (current.size == 0){
                                this.placePiece(new BlueBlob1(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new BlueBlob1(0,0,this)) 
                            } else if (current.size == 1){
                                this.placePiece(new BlueBlob2(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new BlueBlob2(0,0,this)) 
                            } else if (current.size == 2){
                                this.placePiece(new BlueBlob3(0,0,this))
                                var blobSquare = current.square
                                blobSquare.contains.removePiece()
                                blobSquare.placePiece(new BlueBlob3(0,0,this)) 
                            }

                        }
                    }
                } else {
                    squares[this.row][this.col].placePiece(current)
                    enpassantedPiece.removePiece()
                }
            } else {
                this.placePiece(current)
            }
            var failed = false
            highlightedSquares.push(current.square)
        } else {
            var failed = true
            counter2++
        }
        if (current.type == "opawn"){
            if (current.row == 0 || current.row == rowMax){
                current.promote()
            }
        }
        isCheck = false
        var oldCheck = document.getElementById("check")
        if (oldCheck != null){
            oldCheck.remove()
            isCheck = false
        }

        
        var turnTemp = turn
        turn = turnEnemy
        turnEnemy = turnTemp
        var isMate = false
        if(checkChecks(king) == true){
            var check = document.createElement("img")
            check.src = "check.png"
            check.style.pointerEvents = "none"
            //check.style.top = kingRow * 64 + "px"
            //check.style.left = kingCol * 64 + "px"
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
                    $.post("postWin.php");
                    isMate = true
                }
            } 
        }
        new Turn(current,squares[fromRow][fromCol],this,this.contains,takes,isCheck,isMate,failed)
        update()
    }
    
    dogMoves(enpassant = false, blob = false, enpassantedPiece = null){
        //if (current.checkStuck2(this) == false){       
            if (current.type == "chicken" && current.types.includes("panda")){
                immortal = current
            }
            doghighlight = current.square
            clearLegals()
            var currentSquare = current.square
            currentSquare.contains = null
            current.clearPiece()
            if (blob == true){
                if (turn == "red"){
                    currentSquare.placePiece(new RedEgg0(0,0,0))
                } else {
                    currentSquare.placePiece(new BlueEgg0(0,0,0))
                }
            }
            current.moved = true
            if (enpassant == false){
                this.contains.removePiece()
                this.placePiece(current)
            }else {
                squares[this.row][this.col].placePiece(current)
                enpassantedPiece.removePiece()
            }
            isCheck = false
            var oldCheck = document.getElementById("check")
            if (oldCheck != null){
                oldCheck.remove()
                isCheck = false
            }
            
            dogTime = current
            current.move()
        //}
        update()
    }

    checkMove(piece,square = null){
        var squareOccupant = null
        if (square != null){
            squareOccupant = square.contains
            square.contains = null
        }
        // get corresponding king
        if (turn == "blue"){
            var king = blueKing
        } else {
            var king = redKing
        }
        var row = piece.row
        var col = piece.col
        var currentOccupant = this.contains
        if (immortal == currentOccupant && immortal != null){
            return false
        }
        var preImmortal = immortal
        if (currentOccupant != null){
            if (piece.type == "panda" || (piece.type == "chicken" && piece.types.includes("panda"))){
                immortal = piece
            }
        }
        this.testPiece(piece,null,row,col)
        if(checkChecks(king) == true){
            squares[row][col].testPiece(piece,currentOccupant,this.row,this.col)
            if (squareOccupant != null){
                square.contains = squareOccupant
            }
            immortal = preImmortal
            return false
        } else {
            squares[row][col].testPiece(piece,currentOccupant,this.row,this.col)
            if (squareOccupant != null){
                square.contains = squareOccupant
            }
            immortal = preImmortal
            return true
        }
    }

    testPiece(piece,replacement,row,col){
        squares[row][col].contains = replacement
        piece.row = this.row
        piece.col = this.col
        this.contains = piece
        piece.square = this
    }
}

class Turn {
    constructor(piece,from,to,inhabitant,takes = false,check = false, checkmate = false, failed = false){
        this.piece = piece
        this.from = from
        this.to = to
        this.inhabitant = inhabitant
        turns.push(this)
        if (this.piece.type == "opawn"){
            this.algebraic = ""
        } else {
            this.algebraic = this.piece.type[0].toUpperCase()
        }
        if (takes == true){
            if (this.piece.type == "opawn"){
                this.algebraic += files[this.from.col]
            }
            this.algebraic += "x"
            
        }
        this.algebraic += files[this.to.col] + (this.to.row + 1)

        if (checkmate == true){
            this.algebraic += "#"
        } else if (check == true){
            this.algebraic += "+"
        }
        if (failed == true){
            this.algebraic += "<X>"
        }

        if (turns.length % 2 == 0){
            var turnPair = turns.slice(turns.length - 2)
            new TurnPair(turnPair[0],turnPair[1])
        }
    }
}

class TurnPair {
    constructor (blueMove,redMove){
        this.blueMove = blueMove
        this.redMove = redMove

        this.div = document.createElement("div")
        this.div.className = "turns"

        this.turnDiv = document.createElement("div")
        this.blueDiv = document.createElement("div")
        this.redDiv = document.createElement("div")

        this.turnDiv.className = "subTurns"
        this.blueDiv.className = "subTurns"
        this.redDiv.className = "subTurns"

        this.turnText = document.createTextNode(turnNo + ".")
        this.blueText = document.createTextNode(blueMove.algebraic)
        this.redText = document.createTextNode(redMove.algebraic)

        this.turnDiv.appendChild(this.turnText)
        this.blueDiv.appendChild(this.blueText)
        this.redDiv.appendChild(this.redText)

        this.div.appendChild(this.turnDiv)
        this.div.appendChild(this.blueDiv)
        this.div.appendChild(this.redDiv)
        var sideboard = document.getElementById("turns")
        sideboard.appendChild(this.div)
        turnNo += 1

    }
}

class Piece {
    constructor(row,col,img,square){
        this.row = row
        this.col = col
        this.img = img
        this.moved = false
        this.element = null
        this.square = square
        if (this.owner == "red"){
            this.enemy == "blue"
        } else {
            this.enemy == "red"
        }
        pieces.push(this)
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            var moves = legalMoves[0]
            for (var i = 0; i < moves.length; i++){
                if(moves[i].contains == null){
                    moves[i].legalMove()
                } else {
                    moves[i].legalTake()
                }
            }
        }
    }

    checkStuck(){
        if (this.getLegalMoves.length == 0){
            return true
        } else {
            return false
        }
    }  

    removePiece(){
        pieces.splice(pieces.indexOf(this),1)
        this.square.contains = null
        this.element.remove()
    }

    clearPiece(){
        this.square.contains = null
        this.element.remove()
    }

    moveCheck(){
        if (turn == this.owner && turn == color){
            if (dogTime == null || dogTime == this){
                return true
            }
        }
        return false
    }

    checkSquare(square,blob = false){
        if (square.contains == null){
            square.legalMove(false,blob)
            return true
        } else if (square.contains.owner == this.enemy){
            square.legalTake(false,blob)
            return false
        }
        return false
    }

    checkSquare2(square){
        if (square.contains == null){
            return "null"
        } else if (square.contains.owner == this.enemy){
            return true
        }
        return false
    }

    filter(movesList){
        var newMoves = []
        for(var i = 0; i < movesList.length; i++){
            var subMoves = []
            for(var j = 0; j < movesList[i].length; j++){
                if(movesList[i][j].checkMove(this)){
                    subMoves.push(movesList[i][j])
                }
            }
            newMoves.push(subMoves)
        }
        return newMoves
    }

    check(r,c){
        return false
    }
}

class Pawn extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "opawn"
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            var moves = legalMoves[0]
            var enpMoves = legalMoves[1]
            var enpTakes = legalMoves[2]
            for (var i = 0; i < moves.length; i++){
                if(moves[i].contains == null){
                    moves[i].legalMove()
                } else {
                    moves[i].legalTake()
                }
            }
            for (var i = 0; i < enpMoves.length; i++){
                enpMoves[i].legalMove(true)
            }
            for (var i = 0; i < enpTakes.length; i++){
                enpTakes[i].legalTake(true)
            }
        }
    }

    promote(){
        this.removePiece()
        var n = Math.floor(Math.random() * 6)
        if (n == 0){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedRook(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueRook(0,0,squares[this.row][this.col]))
            }
        } else if (n == 1){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedDog(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueDog(0,0,squares[this.row][this.col]))
            }
        } else if (n == 2){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedBlob0(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueBlob0(0,0,squares[this.row][this.col]))
            }
        } else if (n == 3){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedFrog(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueFrog(0,0,squares[this.row][this.col]))
            }
        } else if (n == 4){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedPanda(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BluePanda(0,0,squares[this.row][this.col]))
            }
        } else if (n == 5){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedSquire(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueSquire(0,0,squares[this.row][this.col]))
            }
        } else if (n == 6){
            if (this.owner == "red"){
                squares[this.row][this.col].placePiece(new RedChicken(0,0,squares[this.row][this.col]))
            } else {
                squares[this.row][this.col].placePiece(new BlueChicken(0,0,squares[this.row][this.col]))
            }
        }
    }

}

class RedPawn extends Pawn{
    constructor(row,col,square){
        super(row,col,"redPawn.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }

    getLegalMoves(){
        if (true){
            var moves = []
            var enpMoves = []
            var enpTakes = []
            if (this.row == 1){
                enpMoves = this.firstMove()
            }
            if (this.row != rowMax && squares[this.row+1][this.col].contains == null){
                moves.push(squares[this.row+1][this.col])
            }
            if (this.row != rowMax && this.col != colMax){
                if(squares[this.row+1][this.col+1].contains != null){
                    if(squares[this.row+1][this.col+1].contains.owner == "blue"){
                        moves.push(squares[this.row+1][this.col+1])
                    }
                }
            }
            
            if(this.row != rowMax && this.col != 0){
                if(squares[this.row+1][this.col-1].contains != null){
                    if(squares[this.row+1][this.col-1].contains.owner == "blue"){
                        moves.push(squares[this.row+1][this.col-1])
                    }
                }
            }
            
            
            if(this.col != colMax){
                if(squares[this.row][this.col + 1].contains == enpassantablePiece){
                    if(squares[this.row][this.col + 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row][this.col + 1])
                    }
                } else if(this.row != 0 && squares[this.row - 1][this.col + 1].contains == enpassantablePiece){
                    if(squares[this.row - 1][this.col + 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row - 1][this.col + 1])
                    }
                }
            }
    
            if(this.col != 0){
                if(squares[this.row][this.col - 1].contains == enpassantablePiece){
                    if(squares[this.row][this.col - 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row][this.col - 1])
                    }
                } else if(this.row != 0 && squares[this.row - 1][this.col - 1].contains == enpassantablePiece){
                    if(squares[this.row - 1][this.col - 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row - 1][this.col - 1])
                    }
                }
            }
            
        }
        return [moves,enpMoves,enpTakes]
    }    

    firstMove(){
        var enpMoves = []
        if(squares[this.row+2][this.col].contains == null && squares[this.row+1][this.col].contains == null){
            enpMoves.push(squares[this.row+2][this.col])
            if (squares[this.row + 3][this.col].contains == null){
                enpMoves.push(squares[this.row + 3][this.col])
            }
            return enpMoves
        }
    }

    check(row,col){
        if (row == this.row + 1){
            if (col == this.col + 1){
                return true
            } else if (col == this.col - 1){
                return true
            } else {
                return false
            }
        } else {
            return false
        }
    }

    // checkStuck(){
    //     if (this.moved == false){
    //         if(squares[this.row+2][this.col].contains == null && squares[this.row+1][this.col].contains == null){
    //             if (squares[this.row+2][this.col].checkMove(this)){
    //                 return false
    //             }
    //         }
    //     }
    //     if (squares[this.row+1][this.col].contains == null){
    //         if (squares[this.row+1][this.col].checkMove(this)){
    //             return false
    //         }
    //     }
    //     if (this.col != colMax){
    //         if(squares[this.row + 1][this.col + 1].contains != null){
    //             if(squares[this.row + 1][this.col + 1].contains.owner == this.enemy){
    //                 if (squares[this.row + 1][this.col + 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
    //     if(this.col != 0){
    //         if(squares[this.row + 1][this.col - 1].contains != null){
    //             if(squares[this.row + 1][this.col - 1].contains.owner == this.enemy){
    //                 if (squares[this.row + 1][this.col - 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
        
    //     if(this.col != colMax){
    //         if(squares[this.row][this.col+1].contains == enpassantablePiece){
    //             if(squares[this.row][this.col+1].contains.owner == this.enemy){
    //                 if (squares[this.row][this.col + 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }

    //     if(this.col != 0){
    //         if(squares[this.row][this.col-1].contains == enpassantablePiece){
    //             if(squares[this.row][this.col-1].contains.owner == this.enemy){
    //                 if (squares[this.row][this.col - 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
    //     return true
    // }
}

class BluePawn extends Pawn{
    constructor(row,col,square){
        super(row,col,"bluePawn.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }

    getLegalMoves(){
        if (true){
            var moves = []
            var enpMoves = []
            var enpTakes = []
            if (this.row == 8){
                enpMoves = this.firstMove()
            }
            if (this.row != 0 && squares[this.row-1][this.col].contains == null){
                moves.push(squares[this.row-1][this.col])
            }
            
            if (this.row != 0 && this.col != colMax){
                if(squares[this.row-1][this.col+1].contains != null){
                    if(squares[this.row-1][this.col+1].contains.owner == "red"){
                        moves.push(squares[this.row-1][this.col+1])
                    }
                }
            }
            
            if(this.row != 0 && this.col != 0){
                if(squares[this.row-1][this.col-1].contains != null){
                    if(squares[this.row-1][this.col-1].contains.owner == "red"){
                        moves.push(squares[this.row-1][this.col-1])
                    }
                }
            }
            

            if(this.col != colMax){
                if(squares[this.row][this.col + 1].contains == enpassantablePiece){
                    if(squares[this.row][this.col + 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row][this.col + 1])
                    }
                } else if(this.row != rowMax && squares[this.row + 1][this.col + 1].contains == enpassantablePiece){
                    if(squares[this.row + 1][this.col + 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row + 1][this.col + 1])
                    }
                }
            }
    
            if(this.col != 0){
                if(squares[this.row][this.col - 1].contains == enpassantablePiece){
                    if(squares[this.row][this.col - 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row][this.col - 1])
                    }
                } else if(this.row != rowMax && squares[this.row + 1][this.col - 1].contains == enpassantablePiece){
                    if(this.row != rowMax && squares[this.row + 1][this.col - 1].contains.owner == this.enemy){
                        enpTakes.push(squares[this.row + 1][this.col - 1])
                    }
                }
            }
        }
        
        return[moves,enpMoves,enpTakes]
    }
    
    firstMove(){
        var enpMoves = []
        if(squares[this.row - 2][this.col].contains == null && squares[this.row - 1][this.col].contains == null){
            enpMoves.push(squares[this.row - 2][this.col])
            if (squares[this.row - 3][this.col].contains == null){
                enpMoves.push(squares[this.row - 3][this.col])
            }
        }
        return enpMoves
    }

    check(row,col){
        if (row == this.row - 1){
            if (col == this.col + 1){
                return true
            } else if (col == this.col - 1){
                return true
            } else {
                return false
            }
        } else {
            return false
        }
    }

    // checkStuck(){
    //     if (this.moved == false){
    //         if(squares[this.row - 2][this.col].contains == null && squares[this.row - 1][this.col].contains == null){
    //             if (squares[this.row-2][this.col].checkMove(this)){
    //                 return false
    //             }
    //         }
    //     }
    //     if (squares[this.row - 1][this.col].contains == null){
    //         if (squares[this.row - 1][this.col].checkMove(this)){
    //             return false
    //         }
    //     }
    //     if (this.col != colMax){
    //         if(squares[this.row - 1][this.col + 1].contains != null){
    //             if(squares[this.row - 1][this.col + 1].contains.owner == this.enemy){
    //                 if (squares[this.row - 1][this.col + 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
    //     if(this.col != 0){
    //         if(squares[this.row - 1][this.col - 1].contains != null){
    //             if(squares[this.row - 1][this.col - 1].contains.owner == this.enemy){
    //                 if (squares[this.row - 1][this.col - 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
        
    //     if(this.col != colMax){
    //         if(squares[this.row][this.col + 1].contains == enpassantablePiece){
    //             if(squares[this.row][this.col + 1].contains.owner == this.enemy){
    //                 if (squares[this.row][this.col + 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }

    //     if(this.col != 0){
    //         if(squares[this.row][this.col - 1].contains == enpassantablePiece){
    //             if(squares[this.row][this.col - 1].contains.owner == this.enemy){
    //                 if (squares[this.row][this.col - 1].checkMove(this)){
    //                     return false
    //                 }
    //             }
    //         }
    //     }
        
    //     return true

    // }
}

class Rook extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "rook"
    }

    getLegalMoves(){
        var moves = []
        if (true){
            var row = this.row
            var step = 1
            if(row == rowMax){
                step = 2
            }
            while (step == 1){
                if (squares[row+1][this.col].contains == null){
                    moves.push(squares[row+1][this.col])
                    row += 1
                    if(row == rowMax){
                        step = 2
                    }
                } else if (squares[row+1][this.col].contains.owner == this.enemy){
                    moves.push(squares[row+1][this.col])
                    step = 2
                } else {
                    step = 2
                }
            }
            
            row = this.row
            if(row == 0){
                step = 3
            }
            while (step == 2){
                if (squares[row-1][this.col].contains == null){
                    moves.push(squares[row-1][this.col])
                    row -= 1
                    if(row == 0){
                        step = 3
                    }
                } else if (squares[row-1][this.col].contains.owner == this.enemy){
                    moves.push(squares[row-1][this.col])
                    step = 3
                } else {
                    step = 3
                }
            }

            var col = this.col
            if(col == colMax){
                step = 4
            }
            while (step == 3){
                if (squares[this.row][col+1].contains == null){
                    moves.push(squares[this.row][col + 1])
                    col += 1
                    if(col == colMax){
                        step = 4
                    }
                } else if (squares[this.row][col+1].contains.owner == this.enemy){
                    moves.push(squares[this.row][col + 1])
                    step = 4
                } else {
                    step = 4
                }
            }

            col = this.col
            if(col == 0){
                step = 5
            }
            while (step == 4){
                if (squares[this.row][col-1].contains == null){
                    moves.push(squares[this.row][col - 1])
                    col -= 1
                    if(col == 0){
                        step = 5
                    }
                } else if (squares[this.row][col-1].contains.owner == this.enemy){
                    moves.push(squares[this.row][col - 1])
                    step = 5
                } else {
                    step = 5
                }
            }
        }   
        return [moves]
    }

    check(r,c){
        row = this.row
        var step = 1
        if(row == rowMax){
            step = 2
        }
        while (step == 1){
            if (row + 1 == r && this.col == c){
                return true
            }
            if (squares[row+1][this.col].contains == null){
                row += 1
                if(row == rowMax){
                    step = 2
                }
            } else {
                step = 2
            }
        }
        
        row = this.row
        if(row == 0){
            step = 3
        }
        while (step == 2){
            if (row - 1 == r && this.col == c){
                return true
            }
            if (squares[row-1][this.col].contains == null){
                row -= 1
                if(row == 0){
                    step = 3
                }
            } else {
                step = 3
            }
        }

        var col = this.col
        if(col == colMax){
            step = 4
        }
        while (step == 3){
            if (this.row == r && col + 1 == c){
                return true
            }
            if (squares[this.row][col+1].contains == null){
                col += 1
                if(col == colMax){
                    step = 4
                }
            } else {
                step = 4
            }
        }

        col = this.col
        if(col == 0){
            step = 5
        }
        while (step == 4){
            if (this.row == r && col - 1 == c){
                return true
            }
            if (squares[this.row][col-1].contains == null){
                col -= 1
                if(col == 0){
                    step = 5
                }
            } else {
                step = 5
            }
        }
        return false

    }

    checkStuck(){
        var row = this.row
        var step = 1
        if(row == rowMax){
            step = 2
        }
        while (step == 1){
            if (squares[row + 1][this.col].contains == null){
                if (squares[row + 1][this.col].checkMove(this)){
                    return false
                }
                row += 1
                if(row == rowMax){
                    step = 2
                }
            } else if (squares[row + 1][this.col].contains.owner == this.enemy){
                if (squares[row + 1][this.col].checkMove(this)){
                    return false
                }
                step = 2
            } else {
                step = 2
            }
        }
        
        row = this.row
        if(row == 0){
            step = 3
        }
        while (step == 2){
            if (squares[row - 1][this.col].contains == null){
                if (squares[row - 1][this.col].checkMove(this)){
                    return false
                }
                row -= 1
                if(row == 0){
                    step = 3
                }
            } else if (squares[row - 1][this.col].contains.owner == this.enemy){
                if (squares[row - 1][this.col].checkMove(this)){
                    return false
                }
                step = 3
            } else {
                step = 3
            }
        }

        var col = this.col
        if(col == colMax){
            step = 4
        }
        while (step == 3){
            if (squares[this.row][col + 1].contains == null){
                if (squares[this.row][col + 1].checkMove(this)){
                    return false
                }
                col += 1
                if(col == colMax){
                    step = 4
                }
            } else if (squares[this.row][col + 1].contains.owner == this.enemy){
                if (squares[this.row][col + 1].checkMove(this)){
                    return false
                }
                step = 4
            } else {
                step = 4
            }
        }

        col = this.col
        if(col == 0){
            return true
        }
        while (step == 4){
            if (squares[this.row][col - 1].contains == null){
                if (squares[this.row][col - 1].checkMove(this)){
                    return false
                }
                col -= 1
                if(col == 0){
                    return true
                }
            } else if (squares[this.row][col - 1].contains.owner == this.enemy){
                if (squares[this.row][col - 1].checkMove(this)){
                    return false
                }
                return true
            } else {
                return true
            }
            
        }   
    }
}

class RedRook extends Rook{
    constructor(row,col,square){
        super(row,col,"redRook.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueRook extends Rook{
    constructor(row,col,square){
        super(row,col,"blueRook.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class King extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "king"
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row != rowMax){
                for(var i=-1;i<2;i++){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row+1][this.col + i].contains == null){
                            moves.push(squares[this.row + 1][this.col + i])
                            
                        } else if (squares[this.row+1][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row + 1][this.col + i])
                        }
                    }
                }
            }

            
            for(var k=-1;k<2;k+=2){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row][this.col + k].contains == null){
                        moves.push(squares[this.row][this.col + k])
                    } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                        moves.push(squares[this.row][this.col + k])
                    }
                }
                
            }
            
            if (this.row != 0){ 
                for(var i=-1;i<2;i++){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row-1][this.col + i].contains == null){
                            moves.push(squares[this.row - 1][this.col + i])
                        } else if (squares[this.row-1][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row - 1][this.col + i])
                        }
                    }
                }
            }
        }   
        return [moves]
    }

    check(row,col){
        for(i=-1;i<2;i++){
            if (this.row + 1 == row && this.col + i == col){
                return true
            }
        }
        
        for(i=-1;i<2;i+=2){
            if (this.row == row && this.col + i == col){
                return true
            }
        }
        
        for(i=-1;i<2;i++){
            if (this.row - 1 == row && this.col + i == col){
                return true
            }
        }

        return false
    }

    checkStuck(square = squares[this.row][this.col]){
        if (this.row != rowMax){
            for(var i=-1;i<2;i++){
                if(this.col + i >= 0 && this.col + i < 10){
                    if (squares[square.row + 1][square.col + i].contains == null){
                        if (squares[square.row + 1][square.col + i].checkMove(this) == true){
                            return false
                        }                      
                    } else if (squares[square.row + 1][square.col + i].contains.owner == this.enemy){
                        if (squares[square.row + 1][square.col + i].checkMove(this) == true){
                            return false
                        }                      

                    }
                }
            }
        }
        
        for(var i=-1;i<2;i+=2){
            if(this.col + i >= 0 && this.col + i < 10){
                if (squares[square.row][square.col + i].contains == null){
                    if (squares[square.row][square.col + i].checkMove(this) == true){
                        return false
                    }
                }  else if (squares[square.row][square.col + i].contains.owner == this.enemy){
                    if (squares[square.row][square.col + i].checkMove(this) == true){
                        return false
                    }
                }
            }
        }
        
        
        if (this.row != 0){
            for(var i=-1;i<2;i++){
                if(this.col + i >= 0 && this.col + i < 10){
                    if (squares[square.row - 1][square.col + i].contains == null){
                        if (squares[square.row - 1][square.col + i].checkMove(this) == true){
                            return false
                        }
                    } else if (squares[square.row - 1][square.col + i].contains.owner == this.enemy){
                        if (squares[square.row - 1][square.col + i].checkMove(this) == true){
                            return false
                        }

                    }
                }
            }
        }
        
        return true
    }
}

class RedKing extends King{
    constructor(row,col,square){
        super(row,col,"redKing.png",square)
        this.owner = "red"
        this.enemy = "blue"
        redKing = this
    }
}

class BlueKing extends King{
    constructor(row,col,square){
        super(row,col,"blueKing.png",square)
        this.owner = "blue"
        this.enemy = "red"
        blueKing = this
    }
}

class Dog extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "dog"
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            var moves = legalMoves[0]
            for (var i = 0; i < moves.length; i++){
                if(moves[i].contains == null){
                    moves[i].legalMove()
                } else {
                    if (dogTime == this){
                        moves[i].legalTake()
                    } else {
                        moves[i].legalDogTake()
                    }
                }
            }
        }
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row < rowMax - 1){
                for(var i=-1;i<2;i+=2){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row+2][this.col + i].contains == null){
                            moves.push(squares[this.row+2][this.col + i])
                        } else if (squares[this.row+2][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row+2][this.col + i])
                        }
                    }
                }
            }
            
            if (this.row < rowMax){
                for(var i=-2;i<4;i+=4){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row + 1][this.col + i].contains == null){
                            moves.push(squares[this.row + 1][this.col + i])
                        } else if (squares[this.row+1][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row + 1][this.col + i])
                        }
                    }
                }
            }

            
            if (this.row > 0){
                for(var i=-2;i<4;i+=4){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row-1][this.col + i].contains == null){
                            moves.push(squares[this.row - 1][this.col + i])
                        } else if (squares[this.row-1][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row - 1][this.col + i])
                        }
                    }
                }
            }
            
            if (this.row > 1){
                for(var i=-1;i<2;i+=2){
                    if(this.col + i >= 0 && this.col + i < 10){
                        if (squares[this.row - 2][this.col + i].contains == null){
                            moves.push(squares[this.row - 2][this.col + i])
                        } else if (squares[this.row - 2][this.col + i].contains.owner == this.enemy){
                            moves.push(squares[this.row - 2][this.col + i])
                        }
                    }
                }
            }
        }   
        var newMoves = []
        for (var i = 0; i < moves.length; i++){
            if(moves[i].contains == null || moves[i].contains != immortal){
                newMoves.push(moves[i])
            }
        }
        return [newMoves]
    }

    check(row,col){
        for(var i = -1;i < 2;i += 2){
            if (this.row + 2 == row && this.col + i == col){
                return true
            } else {
                try{
                    if(squares[this.row + 2][this.col + i].contains.owner == this.enemy && squares[this.row + 2][this.col + i].contains != immortal){
                        if(this.check2(2,i,row,col) == true){
                            return true
                        }
                    }
                }
                catch(err){}
            }
        }
        
        for(var i=-2;i<3;i+=4){
            if (this.row + 1 == row && this.col + i == col){
                return true
            } else {
                try{
                    if(squares[this.row + 1][this.col + i].contains.owner == this.enemy && squares[this.row + 1][this.col + i].contains != immortal){
                        if(this.check2(1,i,row,col) == true){
                            return true
                        }
                    }
                }
                catch(err){}
            }
        }
        
        for(var i=-2;i<3;i+=4){
            if (this.row - 1 == row && this.col + i == col){
                return true
            } else {
                try{
                    if(squares[this.row - 1][this.col + i].contains.owner == this.enemy && squares[this.row - 1][this.col + i].contains != immortal){
                        if(this.check2(-1,i,row,col) == true){
                            return true
                        }
                    }
                }
                catch(err){}
            }
        }
        
        for(var i=-1;i<2;i+=2){
            if (this.row - 2 == row && this.col + i == col){
                return true
            } else {
                try{
                    if(squares[this.row - 2][this.col + i].contains.owner == this.enemy && squares[this.row - 2][this.col + i].contains != immortal){
                        if(this.check2(-2,i,row,col) == true){
                            return true
                        }
                    }
                }
                catch(err){}
            }
        }
        
        return false
    }

    check2(r,c,row,col){
        for(i=-1;i<2;i+=2){
            if (this.row + r + 2 == row && this.col + c + i == col){
                return true
            }
        }
        
        for(i=-2;i<3;i+=4){
            if (this.row + r + 1 == row && this.col + c + i == col){
                return true
            }
        }
        
        for(i=-2;i<3;i+=4){
            if (this.row + r - 1 == row && this.col + c + i == col){
                return true
            }
        }
        
        for(i=-1;i<2;i+=2){
            if (this.row + r - 2 == row && this.col + c + i == col){
                return true
            }
        }
        
        return false
    }

    checkStuck(){

        square = this.square

        for(var i=-1;i<2;i+=2){
            try{
                if (squares[square.row + 2][square.col + i].contains == null){
                    if (squares[square.row + 2][square.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[square.row + 2][square.col + i].contains.owner == this.enemy){
                    if (this.checkStuck2(squares[square.row + 2][square.col + i]) == false){
                        return false
                    }
                }
            }
            catch{}
        }
        
        for(var i=-2;i<3;i+=4){
            try{
                if (squares[square.row + 1][square.col + i].contains == null){
                    if (squares[square.row + 1][square.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[square.row + 1][square.col + i].contains.owner == this.enemy){
                    if (this.checkStuck2(squares[square.row + 1][square.col + i]) == false){
                        return false
                    }
                }
            }
            catch{}
        }
        
        if (this.row > 0){
            for(var i=-2;i<3;i+=4){
                if (this.col + i >= 0 && this.col + i <= colMax){
                    if (squares[square.row - 1][square.col + i].contains == null){
                        if (squares[square.row - 1][square.col + i].checkMove(this)){
                            return false
                        }
                    } else if (squares[square.row - 1][square.col + i].contains.owner == this.enemy){
                        if (this.checkStuck2(squares[square.row - 1][square.col + i]) == false){
                            return false
                        }
                    }
                }
            }
        }
        for(var i=-1;i<2;i+=2){
            try{  
                
                if (squares[square.row - 2][square.col + i].contains == null){
                    if (squares[square.row - 2][square.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[square.row - 2][square.col + i].contains.owner == this.enemy){
                    if (this.checkStuck2(squares[square.row - 2][square.col + i]) == false){
                        return false
                    }
                }
            }
            catch{}
        }
        return true
    }

    checkStuck2(square){
        for(var i=-1;i<2;i+=2){
            if(square.row + 2 <= rowMax && square.row + 2 >= 0 && square.col + i >= 0 && square.col + i <= colMax){
                if (squares[square.row + 2][square.col + i].checkMove(this,square) == true){
                    return false
                } 
            }
        }
        
        for(var i=-2;i<3;i+=4){
            try{
                if (squares[square.row + 1][square.col + i].checkMove(this,square) == true){
                    return false
                } 
            }
            catch{
            }
        }
        
        for(var i=-2;i<3;i+=4){
            try{
                if (squares[square.row - 1][square.col + i].checkMove(this,square) == true){
                    return false
                } 
            }
            catch{
            }
        }
        
        for(var i=-1;i<2;i+=2){
            try{  
                if (squares[square.row - 2][square.col + i].checkMove(this,square) == true){
                    return false
                } 
            }
            catch{
            }
        }
        return true
    }
}

class RedDog extends Dog{
    constructor(row,col,square){
        super(row,col,"redDog.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueDog extends Dog{
    constructor(row,col,square){
        super(row,col,"blueDog.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Blob0 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "blob"
        this.size = 0
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            for (var i = 0; i < legalMoves.length; i++){
                this.checkSquare(legalMoves[i], true)
            }
        }
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row < (rowMax - 1)){
                for (var i = -1; i < 2; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        if (squares[this.row + 1][this.col + i].contains == null){
                            if (squares[this.row + 2][this.col + i].contains == null){
                                moves.push(squares[this.row + 2][this.col + i])
                            } else if (squares[this.row + 2][this.col + i].contains.owner == this.enemy){
                                moves.push(squares[this.row + 2][this.col + i])
                            }
                        }
                    }
                }
            }

            if (this.row < rowMax){
                for (var i = -2; i < 3; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        if (Math.abs(i) != 2 || squares[this.row + 1][this.col + (i/2)].contains == null){
                            if (squares[this.row + 1][this.col + i].contains == null){
                                moves.push(squares[this.row + 1][this.col + i])
                            } else if (squares[this.row + 1][this.col + i].contains.owner == this.enemy){
                                moves.push(squares[this.row + 1][this.col + i])
                            }
                        }
                    }
                }
            }
            
            for (var i = -2; i < 3; i++){
                if(i != 0){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        if (Math.abs(i) != 2 || squares[this.row][this.col + (i/2)].contains == null){
                            if (squares[this.row][this.col + i].contains == null){
                                moves.push(squares[this.row][this.col + i])
                            } else if (squares[this.row][this.col + i].contains.owner == this.enemy){
                                moves.push(squares[this.row][this.col + i])
                            }
                        }
                    }
                }
            }
            
            if (this.row > 0){
                for (var i = -2; i < 3; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        if (Math.abs(i) != 2 || squares[this.row - 1][this.col + (i/2)].contains == null){
                            if (squares[this.row - 1][this.col + i].contains == null){
                                moves.push(squares[this.row - 1][this.col + i])
                            } else if (squares[this.row - 1][this.col + i].contains.owner == this.enemy){
                                moves.push(squares[this.row - 1][this.col + i])
                            }
                        }
                    }
                }
            }


            
            if (this.row > 1){
                for (var i = -1; i < 2; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        if (squares[this.row - 1][this.col + i].contains == null){
                            if (squares[this.row - 2][this.col + i].contains == null){
                                moves.push(squares[this.row - 2][this.col + i])
                            } else if (squares[this.row - 2][this.col + i].contains.owner == this.enemy){
                                moves.push(squares[this.row - 2][this.col + i])
                            }
                        }
                    }
                }
            }
        }
        return moves
    }

    check(row,col){
        
        var moves = []
        for (var i = -1; i < 2; i++){
            if (this.row + 1 == row && this.col + i == col){
                return true
            }
            if ((this.row != rowMax && this.col + i <= colMax && this.col + i >= 0) && squares[this.row + 1][this.col + i].contains == null){  
                if (this.row + 2 == row && this.col + i == col){
                    return true
                }
            }
        }

        for (var i = -2; i < 3; i++){
            if (this.col + i >= 0 && this.col + i <= colMax){
                if ((this.row != rowMax && this.col + i/2 <= colMax && this.col + i/2 >= 0) && (Math.abs(i) != 2 || squares[this.row + 1][this.col + (i/2)].contains == null)){   
                    if (this.row + 1 == row && this.col + i == col){
                        return true
                    }
                }
            }
        }
        
        
        for (var i = -2; i < 3; i++){
            if(i != 0){
                if ((this.col + i/2 <= colMax && this.col + i/2 >= 0) && this.col + i >= 0 && this.col + i <= colMax){
                    if (Math.abs(i) != 2 || squares[this.row][this.col + (i/2)].contains == null){   
                        if (this.row == row && this.col + i == col){
                            return true
                        }
                    }
                }
            }
        }
        
        for (var i = -2; i < 3; i++){
            if ((this.row != 0 && this.col + i/2 <= colMax && this.col + i/2 >= 0) && this.col + i >= 0 && this.col + i <= colMax){
                if (Math.abs(i) != 2 || squares[this.row - 1][this.col + (i/2)].contains == null){   
                    if (this.row - 1 == row && this.col + i == col){
                        return true
                    }
                }
            }
        }


        
        for (var i = -1; i < 2; i++){
            if (this.row - 1 == row && this.col + i == col){
                return true
            }
            if ((this.row != 0 && this.col + i <= colMax && this.col + i >= 0) && squares[this.row - 1][this.col + i].contains == null){  
                if (this.row - 2 == row && this.col + i == col){
                    return true
                }
            }
        }
    
        return false
    }

    checkStuck(){
        if(this.row < rowMax){
            var square = squares[this.row + 1][this.col]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.row < rowMax - 1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row + 2][this.col]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        if(this.row > 0){
            var square = squares[this.row - 1][this.col]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.row >  1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row - 2][this.col]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }
        
        if(this.col < colMax){
            var square = squares[this.row][this.col + 1]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.col < colMax - 1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row][this.col + 2]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        if(this.col > 0){
            var square = squares[this.row][this.col - 1]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.col >  1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row][this.col - 2]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        for (var r = -1; r < 2; r += 2){
            for (var c = -1; c < 2; c += 2){
                if (this.row + r >= 0 && this.row + r <= rowMax && this.col + c >= 0 && this.col + c <= colMax){
                    if (squares[this.row + r][this.col + c].contains != null){
                        if (squares[this.row + r][this.col + c].contains.owner != this.owner){
                            if (squares[this.row + r][this.col + c].checkMove(this)){
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true
    }
}

class Blob1 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "blob"
        this.size = 1
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            for (var i = 0; i < legalMoves.length; i++){
                this.checkSquare(legalMoves[i], true)
            }
        }
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if(this.row < rowMax){
                var square = squares[this.row + 1][this.col]
                if (square.contains == null){
                    moves.push(square)
                    if(this.row < rowMax - 1){
                        square = squares[this.row + 2][this.col]
                        if (square.contains == null || square.contains.owner == this.enemy){
                            moves.push(square)
                        }
                    }
                } else if (square.contains.owner == this.enemy){
                    moves.push(square)
                }
            }

            if(this.row > 0){
                var square = squares[this.row - 1][this.col]
                if (square.contains == null){
                    moves.push(square)
                    if(this.row > 1){
                        square = squares[this.row - 2][this.col]
                        if (square.contains == null || square.contains.owner == this.enemy){
                            moves.push(square)
                        }
                    }
                } else if (square.contains.owner == this.enemy){
                    moves.push(square)
                }
            }

            if(this.col < colMax){
                var square = squares[this.row][this.col + 1]
                if (square.contains == null){
                    moves.push(square)
                    if(this.col < colMax - 1){
                        square = squares[this.row][this.col + 2]
                        if (square.contains == null || square.contains.owner == this.enemy){
                            moves.push(square)
                        }
                    }
                } else if (square.contains.owner == this.enemy){
                    moves.push(square)
                }
            }

            if(this.col > 0){
                var square = squares[this.row][this.col - 1]
                if (square.contains == null){
                    moves.push(square)
                    if(this.col > 1){
                        square = squares[this.row][this.col - 2]
                        if (square.contains == null || square.contains.owner == this.enemy){
                            moves.push(square)
                        }
                    }
                } else if (square.contains.owner == this.enemy){
                    moves.push(square)
                }
            }

            for (var r = -1; r < 2; r += 2){
                for (var c = -1; c < 2; c += 2){
                    if (this.row + r >= 0 && this.row + r <= rowMax && this.col + c >= 0 && this.col + c <= colMax){
                        square = squares[this.row + r][this.col + c]
                        if (square.contains == null || square.contains.owner == this.enemy){
                            moves.push(square)
                        }
                    }
                }
            }
        }  
        return moves 
    }

    check(row,col){
        if (this.row + 1 == row && this.col == col){
            return true
        } else if (this.row + 2 == row && this.col == col){
            if (squares[this.row + 1][this.col].contains == null){
                return true
            }
        }

        if (this.row - 1 == row && this.col == col){
            return true
        } else if (this.row - 2 == row && this.col == col){
            if (squares[this.row - 1][this.col].contains == null){
                return true
            }
        }

        if (this.row == row && this.col + 1 == col){
            return true
        } else if (this.row == row && this.col + 2 == col){
            if (squares[this.row][this.col + 1].contains == null){
                return true
            }
        }

        if (this.row == row && this.col - 1 == col){
            return true
        } else if (this.row == row && this.col - 2 == col){
            if (squares[this.row][this.col - 1].contains == null){
                return true
            }
        }

        for (var r = -1; r < 2; r += 2){
            for (var c = -1; c < 2; c += 2){
                if (this.row + r == row && this.col + c == col) {
                    return true
                }
            }
        }

        return false
    }

    checkStuck(){
        if(this.row < rowMax){
            var square = squares[this.row + 1][this.col]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.row < rowMax - 1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row + 2][this.col]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        if(this.row > 0){
            var square = squares[this.row - 1][this.col]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.row >  1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row - 2][this.col]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }
        
        if(this.col < colMax){
            var square = squares[this.row][this.col + 1]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.col < colMax - 1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row][this.col + 2]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        if(this.col > 0){
            var square = squares[this.row][this.col - 1]
            if (this.checkSquare2(square) != false){
                if(square.checkMove(this)){
                    return false
                }
                if(this.col >  1){
                    if (this.checkSquare2(square) == "null"){
                        square = squares[this.row][this.col - 2]
                        if(square.checkMove(this)){
                            return false
                        }
                    }
                }
            }
        }

        for (var r = -1; r < 2; r += 2){
            for (var c = -1; c < 2; c += 2){
                if (this.row + r >= 0 && this.row + r <= rowMax && this.col + c >= 0 && this.col + c <= colMax){
                    if (squares[this.row + r][this.col + c].contains != null){
                        if (squares[this.row + r][this.col + c].contains.owner != this.owner){
                            if (squares[this.row + r][this.col + c].checkMove(this)){
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true
    }
}

class Blob2 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "blob"
        this.size = 2
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            for (var i = 0; i < legalMoves.length; i++){
                this.checkSquare(legalMoves[i], true)
            }
        }
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row != rowMax){   
                for(var k=-1;k<2;k++){
                    if(this.col + k >= 0 && this.col + k < 10){
                        if (squares[this.row+1][this.col + k].contains == null){
                            moves.push(squares[this.row+1][this.col + k])
                        } else if (squares[this.row+1][this.col + k].contains.owner == this.enemy){
                            moves.push(squares[this.row+1][this.col + k])
                        }
                    }
                }
            }
            
            for(var k=-1;k<2;k+=2){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row][this.col + k].contains == null){
                        moves.push(squares[this.row][this.col + k])
                    } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                        moves.push(squares[this.row][this.col + k])
                    }
                }
                
            }
            
            if (this.row != 0){   
                for(var k=-1;k<2;k++){
                    if(this.col + k >= 0 && this.col + k < 10){
                        if (squares[this.row - 1][this.col + k].contains == null){
                            moves.push(squares[this.row - 1][this.col + k])
                        } else if (squares[this.row - 1][this.col + k].contains.owner == this.enemy){
                            moves.push(squares[this.row - 1][this.col + k])
                        }
                    }
                }
            }
        }  
        return moves 
    }

    check(row,col){
        
        for(var i=-1;i<2;i++){
            if (this.row + 1 == row && this.col + i == col){
                return true
            }
        }
        
        for(var i=-1;i<2;i+=2){
            if (this.row == row && this.col + i == col){
                return true
            }
        }
        
        for(var i=-1;i<2;i++){
            if (this.row - 1 == row && this.col + i == col){
                return true
            }
        }

        return false
    }

    checkStuck(){
        if (this.row != rowMax){   
            for(var k=-1;k<2;k++){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row + 1][this.col + k].contains == null){
                        if (squares[this.row + 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    } else if (squares[this.row + 1][this.col + k].contains.owner == this.enemy){
                        if (squares[this.row + 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    }
                }
            }
        }
        
        for(var k=-1;k<2;k+=2){
            if(this.col + k >= 0 && this.col + k < 10){
                if (squares[this.row][this.col + k].contains == null){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                }
            }
            
        }
        
        if (this.row != 0){   
            for(var k=-1;k<2;k++){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row - 1][this.col + k].contains == null){
                        if (squares[this.row - 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    } else if (squares[this.row - 1][this.col + k].contains.owner == this.enemy){
                        if (squares[this.row - 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    }
                }
            }
        }
        return true
    }
}

class Blob3 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "blob"
        this.size = 3
    }
    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            for (var i = 0; i < legalMoves.length; i++){
                this.checkSquare(legalMoves[i])
            }
        }
    }
    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row != rowMax){
                if (squares[this.row+1][this.col].contains == null){
                    moves.push(squares[this.row+1][this.col])
                } else if (squares[this.row+1][this.col].contains.owner == this.enemy){
                    moves.push(squares[this.row+1][this.col])
                }
            }

            
            for(var k=-1;k<2;k+=2){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row][this.col + k].contains == null){
                        moves.push(squares[this.row][this.col + k])
                    } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                        moves.push(squares[this.row][this.col + k])
                    }
                }
                
            }
            
            if (this.row != 0){ 
                if (squares[this.row-1][this.col].contains == null){
                    moves.push(squares[this.row-1][this.col])
                } else if (squares[this.row-1][this.col].contains.owner == this.enemy){
                    moves.push(squares[this.row-1][this.col])
                }
            }
        }   
        return moves
    }

    check(row,col){
        if (this.row + 1 == row && this.col == col){
            return true
        }
        
        for(i=-1;i<2;i+=2){
            if (this.row == row && this.col + i == col){
                return true
            }
        }
        
        if (this.row - 1 == row && this.col == col){
            return true
        }

        return false
    }

    checkStuck(){
        if (this.row != rowMax){
            if (squares[this.row + 1][this.col].contains == null){
                if (squares[this.row + 1][this.col].checkMove(this)){
                    return false;
                }
            } else if (squares[this.row + 1][this.col].contains.owner == this.enemy){
                if (squares[this.row + 1][this.col].checkMove(this)){
                    return false;
                }
            }
        }

        
        for(var k=-1;k<2;k+=2){
            if(this.col + k >= 0 && this.col + k < 10){
                if (squares[this.row][this.col + k].contains == null){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                }
            }
            
        }
        
        if (this.row != 0){ 
            if (squares[this.row - 1][this.col].contains == null){
                if (squares[this.row - 1][this.col].checkMove(this)){
                    return false;
                }
            } else if (squares[this.row - 1][this.col].contains.owner == this.enemy){
                if (squares[this.row - 1][this.col].checkMove(this)){
                    return false;
                }
            }
        }
        return true
    }
}

class RedBlob0 extends Blob0{
    constructor(row,col,square){
        super(row,col,"redBlob0.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueBlob0 extends Blob0{
    constructor(row,col,square){
        super(row,col,"blueBlob0.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class RedBlob1 extends Blob1{
    constructor(row,col,square){
        super(row,col,"redBlob1.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueBlob1 extends Blob1{
    constructor(row,col,square){
        super(row,col,"blueBlob1.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class RedBlob2 extends Blob2{
    constructor(row,col,square){
        super(row,col,"redBlob2.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueBlob2 extends Blob2{
    constructor(row,col,square){
        super(row,col,"blueBlob2.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class RedBlob3 extends Blob3{
    constructor(row,col,square){
        super(row,col,"redBlob3.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueBlob3 extends Blob3{
    constructor(row,col,square){
        super(row,col,"blueBlob3.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Frog extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "frog"
    }

    getLegalMoves(){
        if (true){
            var moves = []
            for(var i=-2;i<3;i++){
                if(this.row + i >= 0 && this.row + i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                    if (squares[this.row + i][this.col + i].contains == null){
                        moves.push(squares[this.row + i][this.col + i])
                    } else if (squares[this.row + i][this.col + i].contains.owner == this.enemy){ 
                        moves.push(squares[this.row + i][this.col + i])
                    }
                }
            }
            
            for(var i=-2;i<3;i++){
                if(this.row - i >= 0 && this.row - i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                    if (squares[this.row - i][this.col + i].contains == null){
                        moves.push(squares[this.row - i][this.col + i])
                    } else if (squares[this.row - i][this.col + i].contains.owner == this.enemy){ 
                        moves.push(squares[this.row - i][this.col + i])
                    }
                }
            }
            
            for(var i=-3;i<4;i++){
                if(this.col + i >= 0 && this.col + i < 10 && i != 0){
                    if (squares[this.row][this.col + i].contains == null){
                        moves.push(squares[this.row][this.col + i])
                    } else if (squares[this.row][this.col + i].contains.owner == this.enemy){ 
                        moves.push(squares[this.row][this.col + i])
                    }
                }
            }
            
            for(var i=-3;i<4;i++){
                if(this.row + i >= 0 && this.row + i < 10 && i != 0){
                    if (squares[this.row + i][this.col].contains == null){
                        moves.push(squares[this.row + i][this.col])
                    } else if (squares[this.row + i][this.col].contains.owner == this.enemy){ 
                        moves.push(squares[this.row + i][this.col])
                    }
                }
            } 
        }   
        return [moves]
    }

    check(row,col){
        for(var i=-2;i<3;i++){
            if (this.row + i == row && this.col + i == col){
                return true
            } 
        }

        for(var i=-2;i<3;i++){
            if (this.row - i == row && this.col + i == col){
                return true
            } 
        }
        
        for(var i=-3;i<4;i++){
            if (this.row == row && this.col + i == col){
                return true
            } 
        }
        
        for(var i=-3;i<4;i++){
            if (this.row + i == row && this.col == col){
                return true
            } 
        }
        
       
        
        return false
    }

    checkStuck(){
        for(var i=-2;i<3;i++){
            if(this.row + i >= 0 && this.row + i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                if (squares[this.row + i][this.col + i].contains == null){
                    if(squares[this.row + i][this.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[this.row + i][this.col + i].contains.owner == this.enemy){ 
                    if(squares[this.row + i][this.col + i].checkMove(this)){
                        return false
                    }
                }
            }
        }
        
        for(var i=-2;i<3;i++){
            if(this.row - i >= 0 && this.row - i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                if (squares[this.row - i][this.col + i].contains == null){
                    if(squares[this.row - i][this.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[this.row - i][this.col + i].contains.owner == this.enemy){ 
                    if(squares[this.row - i][this.col + i].checkMove(this)){
                        return false
                    }
                }
            }
        }
        
        for(var i=-3;i<4;i++){
            if(this.col + i >= 0 && this.col + i < 10 && i != 0){
                if (squares[this.row][this.col + i].contains == null){
                    if(squares[this.row][this.col + i].checkMove(this)){
                        return false
                    }
                } else if (squares[this.row][this.col + i].contains.owner == this.enemy){ 
                    if(squares[this.row][this.col + i].checkMove(this)){
                        return false
                    }
                }
            }
        }
        
        for(var i=-3;i<4;i++){
            if(this.row + i >= 0 && this.row + i < 10 && i != 0){
                if (squares[this.row + i][this.col].contains == null){
                    if(squares[this.row + i][this.col].checkMove(this)){
                        return false
                    }
                } else if (squares[this.row + i][this.col].contains.owner == this.enemy){ 
                    if(squares[this.row + i][this.col].checkMove(this)){
                        return false
                    }
                }
            }
        } 
        return true
    }  
}

class RedFrog extends Frog{
    constructor(row,col,square){
        super(row,col,"redFrog.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueFrog extends Frog{
    constructor(row,col,square){
        super(row,col,"blueFrog.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Panda extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "panda"
    }

    getLegalMoves(){
        if (true){
            var moves = []
            if (this.row != rowMax){   
                for(var k=-1;k<2;k++){
                    if(this.col + k >= 0 && this.col + k < 10){
                        if (squares[this.row+1][this.col + k].contains == null){
                            moves.push(squares[this.row+1][this.col + k])
                        } else if (squares[this.row+1][this.col + k].contains.owner == this.enemy){
                            moves.push(squares[this.row+1][this.col + k])
                        }
                    }
                }
            }
            
            for(var k=-1;k<2;k+=2){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row][this.col + k].contains == null){
                        moves.push(squares[this.row][this.col + k])
                    } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                        moves.push(squares[this.row][this.col + k])
                    }
                }
                
            }
            
            if (this.row != 0){   
                for(var k=-1;k<2;k++){
                    if(this.col + k >= 0 && this.col + k < 10){
                        if (squares[this.row - 1][this.col + k].contains == null){
                            moves.push(squares[this.row - 1][this.col + k])
                        } else if (squares[this.row - 1][this.col + k].contains.owner == this.enemy){
                            moves.push(squares[this.row - 1][this.col + k])
                        }
                    }
                }
            }

            
            if (this.row < rowMax - 1){
                if (squares[this.row + 1][this.col].contains == null && squares[this.row + 2][this.col].contains == null){
                    moves.push(squares[this.row + 2][this.col])
                }
            }

            if (this.row > 1){
                if (squares[this.row - 1][this.col].contains == null && squares[this.row - 2][this.col].contains == null){
                    moves.push(squares[this.row - 2][this.col])
                }
            }

            if (this.col < colMax - 1){
                if (squares[this.row][this.col + 1].contains == null && squares[this.row][this.col + 2].contains == null){
                    moves.push(squares[this.row][this.col + 2])
                }
            }

            if (this.col > 1){
                if (squares[this.row][this.col - 1].contains == null && squares[this.row][this.col - 2].contains == null){
                    moves.push(squares[this.row][this.col - 2])
                }
            }
        }   
        return [moves]
    }

    check(row,col){
        
        for(var i=-1;i<2;i++){
            if (this.row + 1 == row && this.col + i == col){
                return true
            }
        }
        
        for(var i=-1;i<2;i+=2){
            if (this.row == row && this.col + i == col){
                return true
            }
        }
        
        for(var i=-1;i<2;i++){
            if (this.row - 1 == row && this.col + i == col){
                return true
            }
        }
        return false
    }

    checkStuck(){
        if (this.row != rowMax){   
            for(var k=-1;k<2;k++){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row + 1][this.col + k].contains == null){
                        if (squares[this.row + 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    } else if (squares[this.row + 1][this.col + k].contains.owner == this.enemy){
                        if (squares[this.row + 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    }
                }
            }
        }
        
        for(var k=-1;k<2;k+=2){
            if(this.col + k >= 0 && this.col + k < 10){
                if (squares[this.row][this.col + k].contains == null){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                } else if (squares[this.row][this.col + k].contains.owner == this.enemy){
                    if (squares[this.row][this.col + k].checkMove(this)){
                        return false;
                    }
                }
            }
            
        }
        
        if (this.row != 0){   
            for(var k=-1;k<2;k++){
                if(this.col + k >= 0 && this.col + k < 10){
                    if (squares[this.row - 1][this.col + k].contains == null){
                        if (squares[this.row - 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    } else if (squares[this.row - 1][this.col + k].contains.owner == this.enemy){
                        if (squares[this.row - 1][this.col + k].checkMove(this)){
                            return false;
                        }
                    }
                }
            }
        }
        return true
    }
}

class RedPanda extends Panda{
    constructor(row,col,square){
        super(row,col,"redPanda.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BluePanda extends Panda{
    constructor(row,col,square){
        super(row,col,"bluePanda.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Squire extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "squire"
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()
            var either = legalMoves[0]
            var moves = legalMoves[1]
            for (var i = 0; i < either.length; i++){
                if(either[i].contains == null){
                    either[i].legalMove()
                } else {
                    either[i].legalTake()
                }
            }
            for (var i = 0; i < moves.length; i++){
                moves[i].legalSquireMove()
            }
        }
    }

    getLegalMoves(){
        if (true){
            var either = []
            var moves = []
            var row = this.row
            var col = this.col
            var step = 1
            if(row == rowMax||col == colMax){
                step = 2
            }
            while (step == 1){
                if (squares[row + 1][col + 1].contains == null){
                    either.push(squares[row + 1][col + 1])  
                    row += 1
                    col += 1
                    if(row == rowMax||col == colMax){
                        step = 2
                    }
                } else if (squares[row+1][col+1].contains.owner == this.enemy){
                    either.push(squares[row+1][col+1])   
                    step = 2
                } else {
                    step = 2
                }
            }
            
            row = this.row
            col = this.col
            if(row == 0 || col == 0){
                step = 3
            }
            while (step == 2){
                if (squares[row - 1][col - 1].contains == null){
                    either.push(squares[row - 1][col - 1])   
                    row -= 1
                    col -= 1
                    if(row == 0 || col == 0){
                        step = 3
                    }
                } else if (squares[row - 1][col - 1].contains.owner == this.enemy){
                    either.push(squares[row - 1][col - 1])   
                    step = 3
                } else {
                    step = 3
                }
            }

            col = this.col
            row = this.row
            if(col == colMax || row == 0){
                step = 4
            }
            while (step == 3){
                if (squares[row - 1][col + 1].contains == null){
                    either.push(squares[row - 1][col + 1])   
                    col += 1
                    row -= 1
                    if(col == colMax || row == 0){
                        step = 4
                    }
                } else if (squares[row - 1][col + 1].contains.owner == this.enemy){
                    either.push(squares[row - 1][col + 1])   
                    step = 4
                } else {
                    step = 4
                }
            }

            col = this.col
            row = this.row
            if(col == 0 || row == rowMax){
                step = 5
            }
            while (step == 4){
                if (squares[row + 1][col - 1].contains == null){
                    either.push(squares[row + 1][col - 1])   
                    col -= 1
                    row += 1
                    if(col == 0 || row == rowMax){
                        step = 5
                    }
                } else if (squares[row + 1][col - 1].contains.owner == this.enemy){
                    either.push(squares[row + 1][col - 1])   
                    step = 5
                } else {
                    step = 5
                }
            }

            for (var rows = 0; rows < squares.length; rows++){
                 for (var cols = 0; cols < squares[rows].length; cols++){
                     if (squares[rows][cols].contains == null && squares[rows][cols].legal == false){
                        if(!either.includes(squares[rows][cols])){
                            moves.push(squares[rows][cols])
                        }
                     }
                 }
             }

        }  
        return [either,moves] 
    }

    check(r,c){
        var row = this.row
        var col = this.col
        var step = 1
        if(row == rowMax || col == colMax){
            step = 2
        }
        while (step == 1){
            if (row + 1 == r && col + 1 == c){
                return true
            }
            if (squares[row + 1][col + 1].contains == null){
                row += 1
                col += 1
                if(row == rowMax || col == colMax){
                    step = 2
                }
            } else {
                step = 2
            }
        }
        
        row = this.row
        col = this.col
        if(row == 0 || col == 0){
            step = 3
        }
        while (step == 2){
            if (row - 1 == r && col - 1 == c){
                return true
            }
            if (squares[row - 1][col - 1].contains == null){
                row -= 1
                col -= 1
                if(row == 0 || col == 0){
                    step = 3
                }
            } else {
                step = 3
            }
        }

        col = this.col
        row = this.row
        if(col == colMax || row == 0){
            step = 4
        }
        while (step == 3){
            if (row - 1 == r && col + 1 == c){
                return true
            }
            if (squares[row - 1][col + 1].contains == null){
                col += 1
                row -= 1
                if(col == colMax || row == 0){
                    step = 4
                }
            } else {
                step = 4
            }
        }

        col = this.col
        row = this.row
        if(col == 0 || row == rowMax){
            step = 5
        }
        while (step == 4){
            if (row + 1 == r && col - 1 == c){
                return true
            }
            if (squares[row + 1][col-1].contains == null){
                col -= 1
                row += 1
                if(col == 0 || row == rowMax){
                    step = 5
                }
            } else {
                step = 5
            }
        }
        return false

    }

    checkStuck(){
        var row = this.row
        var col = this.col
        var step = 1
        if(row == rowMax||col == colMax){
            step = 2
        }
        while (step == 1){
            if (squares[row + 1][col + 1].contains == null){
                if (squares[row + 1][col + 1].checkMove(this)){
                    return false
                }
                row += 1
                col += 1
                if(row == rowMax||col == colMax){
                    step = 2
                }
            } else if (squares[row + 1][col + 1].contains.owner == this.enemy){
                if (squares[row + 1][col + 1].checkMove(this)){
                    return false
                } 
                step = 2
            } else {
                step = 2
            }
        }
        
        row = this.row
        col = this.col
        if(row == 0 || col == 0){
            step = 3
        }
        while (step == 2){
            if (squares[row - 1][col - 1].contains == null){
                if (squares[row - 1][col - 1].checkMove(this)){
                    return false
                } 
                row -= 1
                col -= 1
                if(row == 0 || col == 0){
                    step = 3
                }
            } else if (squares[row - 1][col - 1].contains.owner == this.enemy){
                if (squares[row - 1][col - 1].checkMove(this)){
                    return false
                } 
                step = 3
            } else {
                step = 3
            }
        }

        col = this.col
        row = this.row
        if(col == colMax || row == 0){
            step = 4
        }
        while (step == 3){
            if (squares[row - 1][col + 1].contains == null){
                if (squares[row - 1][col + 1].checkMove(this)){
                    return false
                }  
                col += 1
                row -= 1
                if(col == colMax || row == 0){
                    step = 4
                }
            } else if (squares[row - 1][col + 1].contains.owner == this.enemy){
                if (squares[row - 1][col + 1].checkMove(this)){
                    return false
                }  
                step = 4
            } else {
                step = 4
            }
        }

        col = this.col
        row = this.row
        if(col == 0 || row == rowMax){
            step = 5
        }
        while (step == 4){
            if (squares[row + 1][col - 1].contains == null){
                if (squares[row + 1][col - 1].checkMove(this)){
                    return false
                }  
                col -= 1
                row += 1
                if(col == 0 || row == rowMax){
                    step = 5
                }
            } else if (squares[row + 1][col - 1].contains.owner == this.enemy){
                if (squares[row + 1][col - 1].checkMove(this)){
                    return false
                }  
                step = 5
            } else {
                step = 5
            }
        }

        for (var rows = 0; rows < squares.length; rows++){
            for (var cols = 0; cols < squares[rows].length; cols++){
                if (squares[rows][cols].contains == null && squares[rows][cols].checkMove(this)){
                    return false
                }  
            }
        }

        return true
    }   
}

class RedSquire extends Squire{
    constructor(row,col,square){
        super(row,col,"redBitchop.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueSquire extends Squire{
    constructor(row,col,square){
        super(row,col,"blueBitchop.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Chicken extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "chicken"
        this.dog = false
        this.getTypes()
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            this.getTypes()
            var arr = this.move1()
            this.moveLoop(arr[0],arr[1],arr[2],arr[3])
        }
    }

    getLegalMoves(){
        this.getTypes()
        var arr = this.move1()
        var moves = []
        for(var i = 0; i < arr.length; i++){
            for(var j = 0; j < arr[i].length; j++){
                if (arr[i][j].contains == null){
                    if (!moves.includes(arr[i][j])){
                        moves.push(arr[i][j])
                    }
                } else if (arr[i][j].contains.owner == this.enemy){
                    if (!moves.includes(arr[i][j])){
                        moves.push(arr[i][j])
                    }
                }
            }
        }
        return moves
    }

    getTypes(){
        var types = []
        var sizes = []
        if (this.row != 0){
            for (var i = -1; i<= 1; i++){
                if (this.col + i <= colMax && this.col + i >= 0){
                    var square = squares[this.row - 1][this.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "blob"){
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        }
                    }
                }
            }
        }

        
        for (var i = -1; i<= 1; i += 2){
            if (this.col + i <= colMax && this.col + i >= 0){
                var square = squares[this.row][this.col + i]
                if (square.contains != null){
                    if (types.includes(square.contains.type) == false){
                        types.push(square.contains.type)
                    }
                    if (square.contains.type == "blob"){
                        if (sizes.includes(square.contains.size) == false){
                            sizes.push(square.contains.size)
                        }
                    }
                }
            }
        }

        
        if (this.row != rowMax){
            for (var i = -1; i<= 1; i++){
                if (this.col + i <= colMax && this.col + i >= 0){
                    var square = squares[this.row + 1][this.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "blob"){
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        }
                    }
                }
            }
        }

        this.types = types
        this.sizes = sizes
    }

    move1(){
       var sizes = this.sizes
       var types = this.types

        var squaresMove = []
        var squaresTake = []
        var squaresTakeTrue = []
        var squaresEither = []

        if (types.includes("opawn")){
            if(this.owner == "red"){
                if (this.row != rowMax){
                    squaresMove.push(squares[this.row+1][this.col])
                    if (this.col != colMax){
                        if(squares[this.row + 1][this.col + 1].contains != null){
                            if(squares[this.row + 1][this.col + 1].contains.owner == this.enemy){
                                squaresTake.push(squares[this.row+1][this.col+1])
                            }
                        }
                    }
                    if(this.col != 0){
                        if(squares[this.row + 1][this.col - 1].contains != null){
                            if(squares[this.row + 1][this.col - 1].contains.owner == this.enemy){
                                squaresTake.push(squares[this.row+1][this.col - 1])
                            }
                        }
                    }
                }
                
                if(this.col != colMax && this.row != 0){
                    if(squares[this.row][this.col + 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row][this.col + 1])
                    } else if(squares[this.row - 1][this.col + 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row - 1][this.col + 1])
                    }
                }
        
                if(this.col != 0 && this.row != 0){
                    if(squares[this.row][this.col - 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row][this.col - 1])
                    } else if(squares[this.row - 1][this.col - 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row - 1][this.col - 1])
                    }
                }
            } else {
                if (this.row != 0){
                    squaresMove.push(squares[this.row - 1][this.col])
                    if (this.col != colMax){
                        if(squares[this.row - 1][this.col + 1].contains != null){
                            if(squares[this.row - 1][this.col + 1].contains.owner == this.enemy){
                                squaresTake.push(squares[this.row+1][this.col+1])
                            }
                        }
                    }
                    if(this.col != 0){
                        if(squares[this.row - 1][this.col - 1].contains != null){
                            if(squares[this.row - 1][this.col - 1].contains.owner == this.enemy){
                                squaresTake.push(squares[this.row+1][this.col - 1])
                            }
                        }
                    }
                }
                
                if(this.col != colMax && this.row != rowMax){
                    if(squares[this.row][this.col + 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row][this.col + 1])
                    } else if(squares[this.row + 1][this.col + 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row + 1][this.col + 1])
                    }
                }
        
                if(this.col != 0 && this.row != rowMax){
                    if(squares[this.row][this.col - 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row][this.col - 1])
                    } else if(squares[this.row + 1][this.col - 1].contains == enpassantablePiece){
                        squaresTakeTrue.push(squares[this.row + 1][this.col - 1])
                    }
                }

            }
        }

        if (types.includes("rook")){
            var row = this.row
            var step = 1
            if(row == rowMax){
                step = 2
            }
            while (step == 1){
                if (squares[row + 1][this.col].contains == null){
                    squaresMove.push(squares[row + 1][this.col])
                    row += 1
                    if(row == rowMax){
                        step = 2
                    }
                } else if (squares[row+1][this.col].contains.owner == this.enemy){
                    squaresTake.push(squares[row+1][this.col])
                    step = 2
                } else {
                    step = 2
                }
            }
            
            row = this.row
            if(row == 0){
                step = 3
            }
            while (step == 2){
                if (squares[row - 1][this.col].contains == null){
                    squaresMove.push(squares[row - 1][this.col])
                    row -= 1
                    if(row == 0){
                        step = 3
                    }
                } else if (squares[row - 1][this.col].contains.owner == this.enemy){
                    squaresTake.push(squares[row - 1][this.col])
                    step = 3
                } else {
                    step = 3
                }
            }

            var col = this.col
            if(col == colMax){
                step = 4
            }
            while (step == 3){
                if (squares[this.row][col+1].contains == null){
                    squaresMove.push(squares[this.row][col + 1])
                    col += 1
                    if(col == colMax){
                        step = 4
                    }
                } else if (squares[this.row][col+1].contains.owner == this.enemy){
                    squaresTake.push(squares[this.row][col + 1])
                    step = 4
                } else {
                    step = 4
                }
            }

            col = this.col
            if(col == 0){
                step = 5
            }
            while (step == 4){
                if (squares[this.row][col-1].contains == null){
                    squaresMove.push(squares[this.row][col - 1])
                    col -= 1
                    if(col == 0){
                        step = 5
                    }
                } else if (squares[this.row][col-1].contains.owner == this.enemy){
                    squaresTake.push(squares[this.row][col - 1])
                    step = 5
                } else {
                    step = 5
                }
            }
        }

        if (types.includes("king") || types.includes("panda") || (sizes.includes(2))){
            if (this.row != 0){
                for (var i = -1; i<= 1; i++){
                    if (this.col + i < colMax && this.col + i > 0){
                        var square = squares[this.row - 1][this.col + i]
                        squaresEither.push(square)
                    }
                }
            }

            
            for (var i = -1; i<= 1; i += 2){
                if (this.col + i < colMax && this.col + i > 0){
                    var square = squares[this.row][this.col + i]
                    squaresEither.push(square)
                }
            }

            
            if (this.row != rowMax){
                for (var i = -1; i<= 1; i++){
                    if (this.col + i < colMax && this.col + i > 0){
                        var square = squares[this.row + 1][this.col + i]
                        squaresEither.push(square)
                    }
                }
            }
            
        }

        if (types.includes("dog")){
            this.dog = true
            if (this.row < rowMax - 1){
                for(var i=-1;i<2;i+=2){
                    if(this.col + i >= 0 && this.col + i < 10){
                        squaresEither.push(squares[this.row + 2][this.col + i])
                    }
                }
            }
            
            if (this.row < rowMax){
                for(var i=-2;i<4;i+=4){
                    if(this.col + i >= 0 && this.col + i < 10){
                        squaresEither.push(squares[this.row + 1][this.col + i])
                    }
                }
            }

            
            if (this.row > 0){
                for(var i=-2;i<4;i+=4){
                    if(this.col + i >= 0 && this.col + i < 10){
                        squaresEither.push(squares[this.row - 1][this.col + i])
                    }
                }
            }
            
            if (this.row > 1){
                for(var i=-1;i<2;i+=2){
                    if(this.col + i >= 0 && this.col + i < 10){
                        squaresEither.push(squares[this.row - 2][this.col + i])
                    }
                }
            }
        } else {
            this.dog = false
        }

        if (sizes.includes(0)){
            if (this.row < (rowMax - 1)){
                for (var i = -1; i < 2; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        squaresEither.push(squares[this.row + 2][this.col + i])
                    }
                }
            }

            if (this.row < rowMax){
                for (var i = -2; i < 3; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        squaresEither.push(squares[this.row + 1][this.col + i])
                    }
                }
            }
            
            for (var i = -2; i < 3; i++){
                if(i != 0){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        squaresEither.push(squares[this.row][this.col + i])
                    }
                }
            }
            
            if (this.row > 0){
                for (var i = -2; i < 3; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        squaresEither.push(squares[this.row - 1][this.col + i])
                    }
                }
            }

            if (this.row > 1){
                for (var i = -1; i < 2; i++){
                    if (this.col + i >= 0 && this.col + i <= colMax){
                        squaresEither.push(squares[this.row - 2][this.col + i])
                    }
                }
            }
        }

        if (sizes.includes(1)){
            if(this.row < rowMax){
                var square = squares[this.row + 1][this.col]
                squaresEither.push(square)
                if (square.contains == null){     
                    if(this.row < rowMax - 1){
                        squaresEither.push(squares[this.row + 2][this.col])
                    }
                }
            }

            if(this.row > 0){
                var square = squares[this.row - 1][this.col]
                squaresEither.push(square)
                if (square.contains == null){
                    if(this.row > 1){
                        squaresEither.push(squares[this.row - 2][this.col])
                    }
                }
            }

            if(this.col < colMax){
                var square = squares[this.row][this.col + 1]
                squaresEither.push(square)
                if (square.contains == null){  
                    if(this.col < colMax - 1){
                        squaresEither.push(squares[this.row][this.col + 2])
                    }
                }
            }

            if(this.col > 0){
                var square = squares[this.row][this.col - 1]
                squaresEither.push(square)
                if (square.contains == null){
                    if(this.col > 1){
                        squaresEither.push(squares[this.row][this.col - 2])
                    }
                }
            }

            for (var r = -1; r < 2; r += 2){
                for (var c = -1; c < 2; c += 2){
                    if (this.row + r >= 0 && this.row + r <= rowMax && this.col + c >= 0 && this.col + c <= colMax){
                        squaresEither.push(squares[this.row + r][this.col + c])
                    }
                }
            }
        }

        if (sizes.includes(3)){
            if (this.row != rowMax){
                squaresEither.push(squares[this.row + 1][this.col])
            }

            
            for(var k=-1;k<2;k+=2){
                if(this.col + k >= 0 && this.col + k < 10){
                    squaresEither.push(squares[this.row][this.col + k])
                }
                
            }
            
            if (this.row != 0){ 
                squaresEither.push(squares[this.row - 1][this.col])
            }
        }

        if (types.includes("frog")){
            for(var i=-2;i<3;i++){
                if(this.row + i >= 0 && this.row + i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                    squaresEither.push(squares[this.row + i][this.col + i])
                }
            }
            
            for(var i=-2;i<3;i++){
                if(this.row - i >= 0 && this.row - i < 10 && this.col + i >= 0 && this.col + i < 10 && i != 0){
                    squaresEither.push(squares[this.row - i][this.col + i])
                }
            }
            
            for(var i=-3;i<4;i++){
                if(this.col + i >= 0 && this.col + i < 10 && i != 0){
                    squaresEither.push(squares[this.row][this.col + i])
                }
            }
            
            for(var i=-3;i<4;i++){
                if(this.row + i >= 0 && this.row + i < 10 && i != 0){
                    squaresEither.push(squares[this.row + i][this.col])
                }
            } 
        }

        if (types.includes("squire")){
            var row = this.row
            var col = this.col
            var step = 1
            if(row == rowMax||col == colMax){
                step = 2
            }
            while (step == 1){
                squaresEither.push(squares[row + 1][col + 1])
                if (squares[row + 1][col + 1].contains == null){
                    row += 1
                    col += 1
                    if(row == rowMax||col == colMax){
                        step = 2
                    }
                }  else {
                    step = 2
                }
            }
            
            row = this.row
            col = this.col
            if(row == 0 || col == 0){
                step = 3
            }
            while (step == 2){
                squaresEither.push(squares[row - 1][col - 1])
                if (squares[row - 1][col - 1].contains == null){
                    row -= 1
                    col -= 1
                    if(row == 0 || col == 0){
                        step = 3
                    }
                } else {
                    step = 3
                }
            }

            col = this.col
            row = this.row
            if(col == colMax || row == 0){
                step = 4
            }
            while (step == 3){
                squaresEither.push(squares[row - 1][col + 1])
                if (squares[row - 1][col + 1].contains == null){  
                    col += 1
                    row -= 1
                    if(col == colMax || row == 0){
                        step = 4
                    }
                } else {
                    step = 4
                }
            }

            col = this.col
            row = this.row
            if(col == 0 || row == rowMax){
                step = 5
            }
            while (step == 4){
                squaresEither.push(squares[row + 1][col - 1])
                if (squares[row + 1][col - 1].contains == null){   
                    col -= 1
                    row += 1
                    if(col == 0 || row == rowMax){
                        step = 5
                    }
                } else {
                    step = 5
                }
            }
        }

        return [squaresMove,squaresTake,squaresTakeTrue,squaresEither]
    }

    moveLoop(squaresMove,squaresTake,squaresTakeTrue,squaresEither){
        var squaresMoved = []
        var squaresTook = []
        for (var i = 0; i < squaresEither.length; i++){
            if (squaresEither[i].contains == null){
                if (!squaresMoved.includes(squaresEither[i])){
                    squaresMoved.push(squaresEither[i])
                    squaresEither[i].legalMove()
                }
            } else if (squaresEither[i].contains.owner == this.enemy) {
                if (!squaresTook.includes(squaresEither[i])){
                    squaresTook.push(squaresEither[i])
                    squaresEither[i].legalTake()
                }
            }
        }

        for (var i = 0; i < squaresMove.length; i++){
            if (!squaresMoved.includes(squaresMove[i])){
                squaresMoved.push(squaresMove[i])
                if (squaresMove[i].contains == null){
                    squaresMove[i].legalMove()
                }
            }
        }

        for (var i = 0; i < squaresTake.length; i++){
            if (!squaresTook.includes(squaresTake[i])){
                squaresTook.push(squaresTake[i])
                if (squaresTake[i].contains != null){
                    if (squaresTake[i].contains.owner == this.enemy){
                        squaresTake[i].legalTake()
                    }
                }
            }
        }

        for (var i = 0; i < squaresTakeTrue.length; i++){
            squaresTakeTrue[i].legalTake(true)
        }
        this.dog == false
    }

    check(r,c){
        
        var arr = this.move1()
        var squaresTake = arr[1]
        var squaresEither = arr[3]

        for (var i = 0; i < squaresEither.length; i++){
            if (squaresEither[i] == squares[r][c]){
                return true
            } 
        }


        for (var i = 0; i < squaresTake.length; i++){
            if (squaresTake[i] == squares[r][c]){
                return true
            } 
        }

        return false
    }

    checkStuck(){
        var squaresMoved = []
        var squaresTook = []

        var arr = this.move1()
        var squaresMove = arr[0]
        var squaresTake = arr[1]
        var squaresTakeTrue = arr[2]
        var squaresEither = arr[3]

        for (var i = 0; i < squaresEither.length; i++){
            if (squaresEither[i].contains == null){
                if (!squaresMoved.includes(squaresEither[i])){
                    squaresMoved.push(squaresEither[i])
                    if (squaresEither[i].checkMove(this)){
                        return false
                    } 
                }
            } else if (squaresEither[i].contains.owner == this.enemy) {
                if (!squaresTook.includes(squaresEither[i])){
                    squaresTook.push(squaresEither[i])
                    if (squaresEither[i].checkMove(this)){
                        return false
                    } 
                }
            }
        }

        for (var i = 0; i < squaresMove.length; i++){
            if (!squaresMoved.includes(squaresMove[i])){
                squaresMoved.push(squaresMove[i])
                if (squaresMove[i].contains == null){
                    if (squaresMove[i].checkMove(this)){
                        return false
                    } 
                }
            }
        }

        for (var i = 0; i < squaresTake.length; i++){
            if (!squaresTook.includes(squaresTake[i])){
                squaresTook.push(squaresTake[i])
                if (squaresTake[i].contains != null){
                    if (squaresTake[i].contains.owner == this.enemy){
                        if (squaresTake[i].checkMove(this)){
                            return false
                        } 
                    }
                }
            }
        }

        for (var i = 0; i < squaresTakeTrue.length; i++){
            if (squaresEitherTrue[i].checkMove(this)){
                return false
            } 
        }

        return true
    }  

    getTypes2(squareToCheck){
        var types = []
        var sizes = []
        if (squareToCheck.row != 0){
            for (var i = -1; i<= 1; i++){
                if (squareToCheck.col + i <= colMax && squareToCheck.col + i >= 0){
                    var square = squares[squareToCheck.row - 1][squareToCheck.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "blob"){
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        }
                    }
                }
            }
        }

        
        for (var i = -1; i<= 1; i += 2){
            if (squareToCheck.col + i <= colMax && squareToCheck.col + i >= 0){
                var square = squares[squareToCheck.row][squareToCheck.col + i]
                if (square.contains != null){
                    if (types.includes(square.contains.type) == false){
                        types.push(square.contains.type)
                    }
                    if (square.contains.type == "blob"){
                        if (sizes.includes(square.contains.size) == false){
                            sizes.push(square.contains.size)
                        }
                    }
                }
            }
        }

        
        if (squareToCheck.row != rowMax){
            for (var i = -1; i<= 1; i++){
                if (squareToCheck.col + i <= colMax && squareToCheck.col + i >= 0){
                    var square = squares[squareToCheck.row + 1][squareToCheck.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "blob"){
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        }
                    }
                }
            }
        }

        this.types2 = types
        this.sizes2 = sizes
    }
    
    move2(square){
        var sizes = this.sizes2
        var types = this.types2
 
         var squaresMove = []
         var squaresTake = []
         var squaresTakeTrue = []
         var squaresEither = []
 
         if (types.includes("opawn")){
             if(this.owner == "red"){
                 if (square.row != rowMax){
                     squaresMove.push(squares[square.row+1][square.col])
                     if (square.col != colMax){
                         squaresTake.push(squares[square.row+1][square.col+1])
                     }
                     if(square.col != 0){
                         squaresTake.push(squares[square.row+1][square.col-1])
                     }
                 }
                 
                 if(square.col != colMax && square.row != 0){
                     if(squares[square.row][square.col + 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row][square.col + 1])
                     } else if(squares[square.row - 1][square.col + 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row - 1][square.col + 1])
                     }
                 }
         
                 if(square.col != 0 && square.row != 0){
                     if(squares[square.row][square.col - 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row][square.col - 1])
                     } else if(squares[square.row - 1][square.col - 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row - 1][square.col - 1])
                     }
                 }
             } else {
                 if (square.row != 0){
                     squaresMove.push(squares[square.row - 1][square.col])
                     if (square.col != colMax){
                         squaresTake.push(squares[square.row - 1][square.col + 1])
                     }
                     if(square.col != 0){
                         squaresTake.push(squares[square.row - 1][square.col - 1])
                     }
                 }
                 
                 if(square.col != colMax && square.row != rowMax){
                     if(squares[square.row][square.col + 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row][square.col + 1])
                     } else if(squares[square.row + 1][square.col + 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row + 1][square.col + 1])
                     }
                 }
         
                 if(square.col != 0 && square.row != rowMax){
                     if(squares[square.row][square.col - 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row][square.col - 1])
                     } else if(squares[square.row + 1][square.col - 1].contains == enpassantablePiece){
                         squaresTakeTrue.push(squares[square.row + 1][square.col - 1])
                     }
                 }
 
             }
         }
 
         if (types.includes("rook")){
             var row = square.row
             var step = 1
             if(row == rowMax){
                 step = 2
             }
             while (step == 1){
                 if (squares[row + 1][square.col].contains == null){
                     squaresMove.push(squares[row + 1][square.col])
                     row += 1
                     if(row == rowMax){
                         step = 2
                     }
                 } else if (squares[row+1][square.col].contains.owner == square.enemy){
                     squaresTake.push(squares[row+1][square.col])
                     step = 2
                 } else {
                     step = 2
                 }
             }
             
             row = square.row
             if(row == 0){
                 step = 3
             }
             while (step == 2){
                 if (squares[row - 1][square.col].contains == null){
                     squaresMove.push(squares[row - 1][square.col])
                     row -= 1
                     if(row == 0){
                         step = 3
                     }
                 } else if (squares[row - 1][square.col].contains.owner == square.enemy){
                     squaresTake.push(squares[row - 1][square.col])
                     step = 3
                 } else {
                     step = 3
                 }
             }
 
             var col = square.col
             if(col == colMax){
                 step = 4
             }
             while (step == 3){
                 if (squares[square.row][col+1].contains == null){
                     squaresMove.push(squares[square.row][col + 1])
                     col += 1
                     if(col == colMax){
                         step = 4
                     }
                 } else if (squares[square.row][col+1].contains.owner == square.enemy){
                     squaresTake.push(squares[square.row][col + 1])
                     step = 4
                 } else {
                     step = 4
                 }
             }
 
             col = square.col
             if(col == 0){
                 step = 5
             }
             while (step == 4){
                 if (squares[square.row][col-1].contains == null){
                     squaresMove.push(squares[square.row][col - 1])
                     col -= 1
                     if(col == 0){
                         step = 5
                     }
                 } else if (squares[square.row][col-1].contains.owner == square.enemy){
                     squaresTake.push(squares[square.row][col - 1])
                     step = 5
                 } else {
                     step = 5
                 }
             }
         }
 
         if (types.includes("king") || types.includes("panda") || (sizes.includes(2))){
             if (square.row != 0){
                 for (var i = -1; i<= 1; i++){
                     if (square.col + i < colMax && square.col + i > 0){
                         var square = squares[square.row - 1][square.col + i]
                         squaresEither.push(square)
                     }
                 }
             }
 
             
             for (var i = -1; i<= 1; i += 2){
                 if (square.col + i < colMax && square.col + i > 0){
                     var square = squares[square.row][square.col + i]
                     squaresEither.push(square)
                 }
             }
 
             
             if (square.row != rowMax){
                 for (var i = -1; i<= 1; i++){
                     if (square.col + i < colMax && square.col + i > 0){
                         var square = squares[square.row + 1][square.col + i]
                         squaresEither.push(square)
                     }
                 }
             }
             
         }
 
         if (types.includes("dog")){
             if (square.row < rowMax - 1){
                 for(var i=-1;i<2;i+=2){
                     if(square.col + i >= 0 && square.col + i < 10){
                         squaresEither.push(squares[square.row + 2][square.col + i])
                     }
                 }
             }
             
             if (square.row < rowMax){
                 for(var i=-2;i<4;i+=4){
                     if(square.col + i >= 0 && square.col + i < 10){
                         squaresEither.push(squares[square.row + 1][square.col + i])
                     }
                 }
             }
 
             
             if (square.row > 0){
                 for(var i=-2;i<4;i+=4){
                     if(square.col + i >= 0 && square.col + i < 10){
                         squaresEither.push(squares[square.row - 1][square.col + i])
                     }
                 }
             }
             
             if (square.row > 1){
                 for(var i=-1;i<2;i+=2){
                     if(square.col + i >= 0 && square.col + i < 10){
                         squaresEither.push(squares[square.row - 2][square.col + i])
                     }
                 }
             }
         }
 
         if (sizes.includes(0)){
             if (square.row < (rowMax - 1)){
                 for (var i = -1; i < 2; i++){
                     if (square.col + i >= 0 && square.col + i <= colMax){
                         squaresEither.push(squares[square.row + 2][square.col + i])
                     }
                 }
             }
 
             if (square.row < rowMax){
                 for (var i = -2; i < 3; i++){
                     if (square.col + i >= 0 && square.col + i <= colMax){
                         squaresEither.push(squares[square.row + 1][square.col + i])
                     }
                 }
             }
             
             for (var i = -2; i < 3; i++){
                 if(i != 0){
                     if (square.col + i >= 0 && square.col + i <= colMax){
                         squaresEither.push(squares[square.row][square.col + i])
                     }
                 }
             }
             
             if (square.row > 0){
                 for (var i = -2; i < 3; i++){
                     if (square.col + i >= 0 && square.col + i <= colMax){
                         squaresEither.push(squares[square.row - 1][square.col + i])
                     }
                 }
             }
 
             if (square.row > 1){
                 for (var i = -1; i < 2; i++){
                     if (square.col + i >= 0 && square.col + i <= colMax){
                         squaresEither.push(squares[square.row - 2][square.col + i])
                     }
                 }
             }
         }
 
         if (sizes.includes(1)){
             if(square.row < rowMax){
                 var square = squares[square.row + 1][square.col]
                 squaresEither.push(square)
                 if (square.contains == null){     
                     if(square.row < rowMax - 1){
                         squaresEither.push(squares[square.row + 2][square.col])
                     }
                 }
             }
 
             if(square.row > 0){
                 var square = squares[square.row - 1][square.col]
                 squaresEither.push(square)
                 if (square.contains == null){
                     if(square.row > 1){
                         squaresEither.push(squares[square.row - 2][square.col])
                     }
                 }
             }
 
             if(square.col < colMax){
                 var square = squares[square.row][square.col + 1]
                 squaresEither.push(square)
                 if (square.contains == null){  
                     if(square.col < colMax - 1){
                         squaresEither.push(squares[square.row][square.col + 2])
                     }
                 }
             }
 
             if(square.col > 0){
                 var square = squares[square.row][square.col - 1]
                 squaresEither.push(square)
                 if (square.contains == null){
                     if(square.col > 1){
                         squaresEither.push(squares[square.row][square.col - 2])
                     }
                 }
             }
 
             for (var r = -1; r < 2; r += 2){
                 for (var c = -1; c < 2; c += 2){
                     if (square.row + r >= 0 && square.row + r <= rowMax && square.col + c >= 0 && square.col + c <= colMax){
                         squaresEither.push(squares[square.row + r][square.col + c])
                     }
                 }
             }
         }
 
         if (sizes.includes(3)){
             if (square.row != rowMax){
                 squaresEither.push(squares[square.row + 1][square.col])
             }
 
             
             for(var k=-1;k<2;k+=2){
                 if(square.col + k >= 0 && square.col + k < 10){
                     squaresEither.push(squares[square.row][square.col + k])
                 }
                 
             }
             
             if (square.row != 0){ 
                 squaresEither.push(squares[square.row - 1][square.col])
             }
         }
 
         if (types.includes("frog")){
             for(var i=-2;i<3;i++){
                 if(square.row + i >= 0 && square.row + i < 10 && square.col + i >= 0 && square.col + i < 10 && i != 0){
                     squaresEither.push(squares[square.row + i][square.col + i])
                 }
             }
             
             for(var i=-2;i<3;i++){
                 if(square.row - i >= 0 && square.row - i < 10 && square.col + i >= 0 && square.col + i < 10 && i != 0){
                     squaresEither.push(squares[square.row - i][square.col + i])
                 }
             }
             
             for(var i=-3;i<4;i++){
                 if(square.col + i >= 0 && square.col + i < 10 && i != 0){
                     squaresEither.push(squares[square.row][square.col + i])
                 }
             }
             
             for(var i=-3;i<4;i++){
                 if(square.row + i >= 0 && square.row + i < 10 && i != 0){
                     squaresEither.push(squares[square.row + i][square.col])
                 }
             } 
         }
 
         if (types.includes("squire")){
             var row = square.row
             var col = square.col
             var step = 1
             if(row == rowMax||col == colMax){
                 step = 2
             }
             while (step == 1){
                 squaresEither.push(squares[row + 1][col + 1])
                 if (squares[row + 1][col + 1].contains == null){
                     row += 1
                     col += 1
                     if(row == rowMax||col == colMax){
                         step = 2
                     }
                 }  else {
                     step = 2
                 }
             }
             
             row = square.row
             col = square.col
             if(row == 0 || col == 0){
                 step = 3
             }
             while (step == 2){
                 squaresEither.push(squares[row - 1][col - 1])
                 if (squares[row - 1][col - 1].contains == null){
                     row -= 1
                     col -= 1
                     if(row == 0 || col == 0){
                         step = 3
                     }
                 } else {
                     step = 3
                 }
             }
 
             col = square.col
             row = square.row
             if(col == colMax || row == 0){
                 step = 4
             }
             while (step == 3){
                 squaresEither.push(squares[row - 1][col + 1])
                 if (squares[row - 1][col + 1].contains == null){  
                     col += 1
                     row -= 1
                     if(col == colMax || row == 0){
                         step = 4
                     }
                 } else {
                     step = 4
                 }
             }
 
             col = square.col
             row = square.row
             if(col == 0 || row == rowMax){
                 step = 5
             }
             while (step == 4){
                 squaresEither.push(squares[row + 1][col - 1])
                 if (squares[row + 1][col - 1].contains == null){   
                     col -= 1
                     row += 1
                     if(col == 0 || row == rowMax){
                         step = 5
                     }
                 } else {
                     step = 5
                 }
             }
         }
 
         return [squaresMove,squaresTake,squaresTakeTrue,squaresEither]
    }

    checkStuck2(square){
        this.getTypes2(square)
        var arr = this.move2(square)
        var squaresTotal = arr[0].concat(arr[1]).concat(arr[2]).concat(arr[3])
        for(var i = 0;i < squaresTotal.length;i++){
            if (squaresTotal[i].checkMove(square,square) == true){
                return false
            }
            }
        }
}


class Chicken2 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "chicken"
        this.dog = false
        this.blob = false
        this.getTypes()
    }

    move(){
        if (this.moveCheck() == true){
            clearLegals()
            current = this
            var legalMoves = this.getLegalMoves()

            var moves = legalMoves[0]
            moves = [...new Set(moves)];
            var enpMoves = legalMoves[1]
            enpMoves = [...new Set(enpMoves)];
            var enpTakes = legalMoves[2]
            enpTakes = [...new Set(enpTakes)];
            var squireMoves = legalMoves[3]
            squireMoves = [...new Set(squireMoves)];
            
            if (this.types.includes("blob")){
                var blob = true
            } else {
                var blob = false
            }

            for (var i = 0; i < moves.length; i++){
                if(moves[i].contains == null){
                    moves[i].legalMove()
                } else {
                    if (this.types.includes("dog") && dogTime != this){
                            moves[i].legalDogTake(false,blob)
                    } else {
                        moves[i].legalTake(false,blob)
                    }
                }
            }
            for (var i = 0; i < enpMoves.length; i++){
                enpMoves[i].legalMove(true)
            }
            for (var i = 0; i < enpTakes.length; i++){
                for (var i = 0; i < enpTakes.length; i++){
                    if (this.types.includes("dog") && dogTime != this){
                        enpTakes[i].legalDogTake(true,blob)
                    } else {
                        enpTakes[i].legalTake(true,blob)
                    }
                }
            }
            for (var i = 0; i < squireMoves.length; i++){
                if(squireMoves[i].legal == false){
                    squireMoves[i].legalSquireMove()
                }
            }
        }
    }

    getLegalMoves(chickens = []){
        this.getTypes(chickens)
        var moves1 = []
        var moves2 = []
        var moves3 = []
        var moves4 = []
        var types = this.types
        var sizes = this.sizes
        var piece
        if(types.includes("opawn")){
            if (this.owner == "red"){   
                piece = new RedPawn(this.row,this.col,this.square)
            } else {
                piece = new BluePawn(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
            moves2 = moves2.concat(pieceMoves[1])
            moves3 = moves3.concat(pieceMoves[2])
        }
        if(types.includes("rook")){
            if (this.owner == "red"){   
                piece = new RedRook(this.row,this.col,this.square)
            } else {
                piece = new BlueRook(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
        }
        if(types.includes("panda") || types.includes("king")|| (types.includes("blob") && this.sizes.includes(2))){
            if (this.owner == "red"){   
                piece = new RedPanda(this.row,this.col,this.square)
            } else {
                piece = new BluePanda(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
        }
        if(types.includes("dog")){
            if (this.owner == "red"){   
                piece = new RedDog(this.row,this.col,this.square)
            } else {
                piece = new BlueDog(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
        }
        if(types.includes("blob") && sizes.includes(0)){
            if (this.owner == "red"){   
                piece = new RedBlob0(this.row,this.col,this.square)
            } else {
                piece = new BlueBlob0(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves)
        }
        if(types.includes("blob") && sizes.includes(1)){
            if (this.owner == "red"){   
                piece = new RedBlob1(this.row,this.col,this.square)
            } else {
                piece = new BlueBlob1(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves)
        }
        if(types.includes("blob") && sizes.includes(3)){
            if (this.owner == "red"){   
                piece = new RedBlob1(this.row,this.col,this.square)
            } else {
                piece = new BlueBlob1(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves)
        }
        if(types.includes("frog")){
            if (this.owner == "red"){   
                piece = new RedFrog(this.row,this.col,this.square)
            } else {
                piece = new BlueFrog(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
        }
        if(types.includes("squire")){
            if (this.owner == "red"){   
                piece = new RedSquire(this.row,this.col,this.square)
            } else {
                piece = new BlueSquire(this.row,this.col,this.square)
            }
            var pieceMoves = piece.getLegalMoves()
            moves1 = moves1.concat(pieceMoves[0])
            moves4 = moves4.concat(pieceMoves[1])
        }
        var moves5 = []
        for (var i =0; i < moves3.length; i++){
            if(this.owner == "blue"){
                moves5.push(squares[moves3[i].row-1][moves3[i].col])
            }
        }
        for (var i = 0; i < moves5.length; i++){
            if(moves1.includes(moves5[i])){
                var n = moves1.indexOf(moves5[i]);
                moves1.splice(n, 1);
            }
        }
        

        return [moves1,moves2,moves3,moves4]
    }

    getTypes(chickens = []){
        var types = []
        var sizes = []
        if (this.row != 0){
            for (var i = -1; i<= 1; i++){
                if (this.col + i <= colMax && this.col + i >= 0){
                    var square = squares[this.row - 1][this.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false && square.contains.type != "chicken"){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "chicken"){
                            if (chickens.includes(square.contains) == false){
                                var piece = square.contains
                                if (chickens == []){
                                    piece.getTypes([this])
                                } else {
                                    var newArr = chickens
                                    newArr.push(this)
                                    piece.getTypes(newArr)
                                }
                                for(var t = 0; t < piece.types.length; t++){  
                                    if (types.includes(piece.types[t]) == false){
                                        types.push(piece.types[t])
                                    }
                                }
                            }
                        }
                        if (square.contains.type == "blob"){
                            this.blob = true
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        } else {
                            this.blob = false
                        }
                    }
                }
            }
        }

        
        for (var i = -1; i<= 1; i += 2){
            if (this.col + i <= colMax && this.col + i >= 0){
                var square = squares[this.row][this.col + i]
                if (square.contains != null){
                    if (types.includes(square.contains.type) == false && square.contains.type != "chicken"){
                        types.push(square.contains.type)
                    }
                    if (square.contains.type == "chicken"){
                        if (chickens.includes(square.contains) == false){
                            var piece = square.contains
                            if (chickens == []){
                                piece.getTypes([this])
                            } else {
                                var newArr = chickens
                                newArr.push(this)
                                piece.getTypes(newArr)
                            }
                            for(var t = 0; t < piece.types.length; t++){  
                                if (types.includes(piece.types[t]) == false){
                                    types.push(piece.types[t])
                                }
                            }
                        }
                    }
                    if (square.contains.type == "blob"){
                        this.blob = true
                        if (sizes.includes(square.contains.size) == false){
                            sizes.push(square.contains.size)
                        }
                    } else {
                        this.blob = false
                    }
                }
            }
        }

        
        if (this.row != rowMax){
            for (var i = -1; i<= 1; i++){
                if (this.col + i <= colMax && this.col + i >= 0){
                    var square = squares[this.row + 1][this.col + i]
                    if (square.contains != null){
                        if (types.includes(square.contains.type) == false && square.contains.type != "chicken"){
                            types.push(square.contains.type)
                        }
                        if (square.contains.type == "chicken"){
                            if (chickens.includes(square.contains) == false){
                                var piece = square.contains
                                if (chickens == []){
                                    piece.getTypes([this])
                                } else {
                                    var newArr = chickens
                                    newArr.push(this)
                                    piece.getTypes(newArr)
                                }
                                for(var t = 0; t < piece.types.length; t++){  
                                    if (types.includes(piece.types[t]) == false){
                                        types.push(piece.types[t])
                                    }
                                }
                            }
                        }
                        if (square.contains.type == "blob"){
                            this.blob = true
                            if (sizes.includes(square.contains.size) == false){
                                sizes.push(square.contains.size)
                            }
                        } else {
                            this.blob = false
                        }
                    }
                }
            }
        }

        this.types = types
        this.sizes = sizes
    }

    
    check(r,c){
        if(this.getLegalMoves()[0].includes(squares[r][c])){
            return true
        }
        if(this.getMoreLegals()[0].includes(squares[r][c])){
            return true
        }
        return false
    }

    checkStuck(){
        if (this.getLegalMoves.length == 0){
            return true
        } else {
            return false
        }
    }  

    checkStuck2(){
        var moves = this.getMoreLegals()
        if (moves[0].length + moves[1].length + moves[2] == 0){
            return true
        } else {
            return false
        }
    }

    getMoreLegals(){
        var moves = this.getLegalMoves()
        var newMoves1 = []
        var newMoves2 = []
        var newMoves3 = []
        if (this.types.includes("dog")){
            for(var i = 0; i < moves[0].length; i++){
                if(moves[0][i].contains != null && moves[0][i].contains != immortal){
                    if (moves[0][i].contains.owner != this.owner){
                        if (this.owner == "red"){   
                            var piece = new RedChicken(moves[0][i].row,moves[0][i].col,moves[0][i])
                        } else {
                            var piece = new BlueChicken(moves[0][i].row,moves[0][i].col,moves[0][i])
                        }
                        var pieceMoves = piece.getLegalMoves([this])
                        newMoves1 = newMoves1.concat(pieceMoves[0])
                        newMoves2 = newMoves2.concat(pieceMoves[1])
                        newMoves3 = newMoves3.concat(pieceMoves[2])
                    }
                } 
            }
        }
        return[newMoves1,newMoves2,newMoves3]
    }
}

class RedChicken extends Chicken2{
    constructor(row,col,square){
        super(row,col,"redChicken.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueChicken extends Chicken2{
    constructor(row,col,square){
        super(row,col,"blueChicken.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Egg0 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "egg0"
        eggs.push(this)
        this.size = 0
    }

    getLegalMoves(){
        return []
    }
}

class RedEgg0 extends Egg0{
    constructor(row,col,square){
        super(row,col,"redEgg0.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueEgg0 extends Egg0{
    constructor(row,col,square){
        super(row,col,"blueEgg0.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Egg1 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "egg1"
        eggs.push(this)
        this.size = 1
    }

    getLegalMoves(){
        return []
    }
}

class RedEgg1 extends Egg1{
    constructor(row,col,square){
        super(row,col,"redEgg1.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueEgg1 extends Egg1{
    constructor(row,col,square){
        super(row,col,"blueEgg1.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Egg2 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "egg2"
        eggs.push(this)
        this.size = 2
    }

    getLegalMoves(){
        return []
    }
}

class RedEgg2 extends Egg2{
    constructor(row,col,square){
        super(row,col,"redEgg2.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueEgg2 extends Egg2{
    constructor(row,col,square){
        super(row,col,"blueEgg2.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Egg3 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "egg3"
        eggs.push(this)
        this.size = 3
    }

    getLegalMoves(){
        return []
    }
}

class RedEgg3 extends Egg3{
    constructor(row,col,square){
        super(row,col,"redEgg3.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueEgg3 extends Egg3{
    constructor(row,col,square){
        super(row,col,"blueEgg3.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

class Egg4 extends Piece{
    constructor(row,col,img,square){
        super(row,col,img,square)
        this.type = "egg4"
        eggs.push(this)
        this.size = 4
    }

    getLegalMoves(){
        return []
    }
}

class RedEgg4 extends Egg4{
    constructor(row,col,square){
        super(row,col,"redEgg4.png",square)
        this.owner = "red"
        this.enemy = "blue"
    }
}

class BlueEgg4 extends Egg4{
    constructor(row,col,square){
        super(row,col,"blueEgg4.png",square)
        this.owner = "blue"
        this.enemy = "red"
    }
}

function crackEggs(){
    var eggsToCrack = []
    for (var egg = 0; egg< eggs.length; egg++){
        eggsToCrack.push(eggs[egg])
    }
    eggs = []
    for (var egg = 0; egg< eggsToCrack.length; egg++){
        var eggColor = eggsToCrack[egg].owner
        var eggSquare = eggsToCrack[egg].square
        eggsToCrack[egg].removePiece()
        if (eggsToCrack[egg].type == "egg0"){
            if(eggColor == "red"){
                eggSquare.placePiece(new RedEgg1(0,0,eggSquare))
            } else {
                eggSquare.placePiece(new BlueEgg1(0,0,eggSquare))
            }
        }else if (eggsToCrack[egg].type == "egg1"){
            if(eggColor == "red"){
                eggSquare.placePiece(new RedEgg2(0,0,eggSquare))
            } else {
                eggSquare.placePiece(new BlueEgg2(0,0,eggSquare))
            }
        }else if (eggsToCrack[egg].type == "egg2"){
            if(eggColor == "red"){
                eggSquare.placePiece(new RedEgg3(0,0,eggSquare))
            } else {
                eggSquare.placePiece(new BlueEgg3(0,0,eggSquare))
            }
        }else if (eggsToCrack[egg].type == "egg3"){
            if(eggColor == "red"){
                eggSquare.placePiece(new RedEgg4(0,0,eggSquare))
            } else {
                eggSquare.placePiece(new BlueEgg4(0,0,eggSquare))
            }
        }else if (eggsToCrack[egg].type == "egg4"){
            if(eggColor == "red"){
                eggSquare.placePiece(new RedChicken(0,0,eggSquare))
            } else {
                eggSquare.placePiece(new BlueChicken(0,0,eggSquare))
            }
        }
    }
}

for (var i = 0; i < 10; i++){
    row = document.createElement("div")
    row.id = "r" + i
    row.className = "row"
    rowA = []
    for (var j = 0; j<10; j++){
        square = document.createElement("div")
        square.id = "r" + i + "c" + j
        square.style.top = 64 * i + "px"
        square.style.left = 64 * j + "px"
        sClass = new Square(i,j,square)
        rowA.push(sClass)
        var r = i
        var c = j 
        if ((i + j) % 2 == 1){
            square.className = "light square"
        } else {
            square.className = "dark square"
        }
        row.appendChild(square)
    }
    squares.push(rowA)
    document.getElementById("board").appendChild(row)
}

function clearIfNull(r,c){
    unmark()
    if(squares[r][c].contains == null){
        clearLegals()
    } else if (squares[r][c].contains.owner == turnEnemy){
        clearLegals()
    }
}

function markSquare(r,c){
    square = squares[r][c]
    if(square.marked == false){
        square.marked = true
        if ((square.row + square.col) % 2 == 1){
            square.div.className = "square markedlight"
        } else {
            square.div.className = "square markeddark"
        }
    } else {
        square.marked = false
        if ((square.row + square.col) % 2 == 1){
            square.div.className = "light square"
        } else {
            square.div.className = "dark square"
        }
    }
    return false
}

function unmark(){
    for (var r = 0; r<10;r++){
        for (var c = 0; c<10;c++){
            square = squares[r][c]
            square.marked = false
            if ((square.row + square.col) % 2 == 1){
                square.div.className = "light square"
            } else {
                square.div.className = "dark square"
            }
        }
    }
    for(var i = 0; i < arrows.length; i++){
        arrows[i].remove()
    }
}

function clearLegals(){
    for (var i = 0; i<10;i++){
        for (var j = 0; j<10;j++){
            squares[i][j].clear()
        }
    }
}

function checkChecks(king){
    for (var r = 0; r<10;r++){
        for (var c = 0; c<10;c++){
            if (squares[r][c].contains != null){
                if (squares[r][c].contains.enemy == king.owner){
                    if (squares[r][c].contains.check(king.row,king.col) == true){
                        isCheck = true
                        return true
                    }
                }
            }
        }
    }
    return false
}

function listTurns(){
    for (var i = 0; i < turns.length; i++){
        console.log(turns[i].algebraic)
    }
}

function highlightSquares(){
    unhighlight()
    for (var i = 0; i < highlightedSquares.length;i++){
        var square = highlightedSquares[i]
        if (square.marked == false){
            if ((square.row + square.col) % 2 == 1){
                square.div.className = "square highlight"
            } else {
                square.div.className = "square highdark"
            }
        }
    }
}

function unhighlight(){
    for (var r = 0; r<10;r++){
        for (var c = 0; c<10;c++){
            square = squares[r][c]
            if (square.marked == false){
                if ((square.row + square.col) % 2 == 1){
                    square.div.className = "light square"
                } else {
                    square.div.className = "dark square"
                }
            }

        }
    }
}

function reset(){
    over = false
    turn = "blue"
    immortal = null
    enpassantablePiece = false
    for(var i = 0; i < 10; i++){
        squares[1][i].placePiece(new RedPawn(0,0,squares[1][i]))
        squares[8][i].placePiece(new BluePawn(0,0,squares[8][i]))
    }
    squares[0][0].placePiece(new RedRook(0,0,squares[0][0]))
    squares[9][0].placePiece(new BlueRook(0,0,squares[9][0]))

    squares[0][1].placePiece(new RedDog(0,0,squares[0][1]))
    squares[9][1].placePiece(new BlueDog(0,0,squares[9][1]))

    squares[0][2].placePiece(new RedChicken(0,0,squares[0][2]))
    squares[9][2].placePiece(new BlueChicken(0,0,squares[9][2]))

    squares[0][3].placePiece(new RedBlob0(0,0,squares[0][3]))
    squares[9][3].placePiece(new BlueBlob0(0,0,squares[9][3]))


    squares[0][4].placePiece(new RedPanda(0,0,squares[0][4]))
    squares[9][4].placePiece(new BluePanda(0,0,squares[9][4]))

    squares[0][5].placePiece(new RedKing(0,0,squares[0][5]))
    squares[9][5].placePiece(new BlueKing(0,0,squares[9][5]))

    squares[0][6].placePiece(new RedSquire(0,0,squares[0][6]))
    squares[9][6].placePiece(new BlueSquire(0,0,squares[9][6]))

    squares[0][7].placePiece(new RedFrog(0,0,squares[0][7]))
    squares[9][7].placePiece(new BlueFrog(0,0,squares[9][7]))

    squares[0][8].placePiece(new RedDog(0,0,squares[0][8]))
    squares[9][8].placePiece(new BlueDog(0,0,squares[9][8]))

    squares[0][9].placePiece(new RedRook(0,0,squares[0][9]))
    squares[9][9].placePiece(new BlueRook(0,0,squares[9][9]))
}
          
function endGame(){
    over = false
    turn = "blue"
    immortal = null
    enpassantablePiece = false
    squares[1][5].placePiece(new RedPawn(0,0,squares[1][i]))
    squares[0][1].placePiece(new RedDog(0,0,squares[0][4]))
    squares[0][5].placePiece(new RedKing(0,0,squares[0][5]))
    squares[9][5].placePiece(new BlueKing(0,0,squares[9][5]))
    squares[9][9].placePiece(new BlueBlob1(0,0,squares[9][4]))
}
          
          
function endGame2(){
    over = false
    turn = "blue"
    immortal = null
    enpassantablePiece = false
    squares[1][5].placePiece(new RedPawn(0,0,squares[1][5]))
    squares[8][5].placePiece(new BluePawn(0,0,squares[1][5]))
    squares[0][4].placePiece(new RedPanda(0,0,squares[0][4]))
    squares[0][5].placePiece(new RedKing(0,0,squares[0][5]))
    squares[9][5].placePiece(new BlueKing(0,0,squares[9][5]))
    squares[9][4].placePiece(new BlueSquire(0,0,squares[9][4]))
}

function endGame3(){
    over = false
    turn = "blue"
    immortal = null
    enpassantablePiece = false
    squares[8][4].placePiece(new RedPawn(0,0,squares[1][5]))
    squares[9][5].placePiece(new RedKing(0,0,squares[0][5]))
    squares[1][6].placePiece(new BluePawn(0,0,squares[1][5]))
    squares[0][5].placePiece(new BlueKing(0,0,squares[9][5]))
}