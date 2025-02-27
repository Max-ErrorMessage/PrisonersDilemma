squares2 = []
for (var i = 0; i < 12; i++){
    row = document.createElement("div")
    row.id = "r" + i + "b2"
    row.className = "row"
    rowA = []
    for (var j = 0; j<12; j++){
        square = document.createElement("div")
        square.id = "r" + i + "c" + j + "b2"
        square.style.top = 10 + 64 * i + "px"
        square.style.left = 10 + 64 * j + "px"
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
    squares2.push(rowA)
    document.getElementById("board2").appendChild(row)
}

function reset2(){
    over2 = false
    turn2 = "blue"
    immortal2 = null
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
   