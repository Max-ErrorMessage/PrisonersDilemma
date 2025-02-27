let currentArrow = null;
let arrowing = false;
let startSquareForArrow = null;
let currentLine = null
let arrows = []
let same = false
let lastEnd = null

function createHoverHandler(square) {
  return function() {
    if (arrowing && currentArrow) {
    if (currentLine != null) {
        currentLine.remove();
    }
    if (square == startSquareForArrow){
        same = true
    } else {
        same = false
    }
    lastEnd = square
    var endX = square.col * 64 + 32;
    var endY = square.row * 64 + 32;
    var startX = startSquareForArrow.col * 64 + 32;
    var startY = startSquareForArrow.row * 64 + 32;
    if(flipped == true){
      endX = 640 - endX
      endY = 640 - endY
      startX = 640 - startX
      startY = 640 - startY
    }
    const line = document.createElementNS(
    "http://www.w3.org/2000/svg",
    "line"
    );
    line.setAttribute("x1", startX);
    line.setAttribute("y1", startY);
    line.setAttribute("x2", endX);
    line.setAttribute("y2", endY);
    line.setAttribute("stroke", "gold");
    line.setAttribute("stroke-width", "6");
    currentArrow.appendChild(line);
    currentLine = line

    var marker = document.createElementNS("http://www.w3.org/2000/svg", "marker");
    marker.setAttribute("id", "arrowhead");
    marker.setAttribute("viewBox", "0 0 10 10");
    marker.setAttribute("refX", "8");
    marker.setAttribute("refY", "5");
    marker.setAttribute("markerWidth", "6");
    marker.setAttribute("markerHeight", "4");
    marker.setAttribute("orient", "auto-start-reverse");

    var path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", "M 0 0 L 10 5 L 0 10 z");
    path.setAttribute("fill", "gold");
    marker.appendChild(path);
    if (!same){
        line.setAttribute("marker-end", "url(#arrowhead)");
    }
    currentArrow.appendChild(marker);
    }
  };
}

function createMouseDownHandler(square, event) {
  if (event.button === 2) {
    same = true
    const board = document.getElementById("board");
    currentArrow = document.createElementNS(
      "http://www.w3.org/2000/svg",
      "svg"
    );
    currentArrow.setAttribute("id", "arrow");
    currentArrow.setAttribute("width", "100%");
    currentArrow.setAttribute("height", "100%");
    currentArrow.setAttribute("viewBox", "0 0 640 640");
    board.appendChild(currentArrow);
    currentArrow.style.pointerEvents = "none";
    arrowing = true;
    startSquareForArrow = square;
  }
}

function createMouseUpHandler() {
  if (arrowing) {
    arrowing = false;
    arrows.push(currentArrow)
    currentLine = null
    currentArrow = null;
    startSquareForArrow = null;
    if(same == false){
        markSquare(lastEnd.row,lastEnd.col)
    }
  }
}

const board = document.getElementById("board");

for (let r = 0; r < squares.length; r++) {
  for (let c = 0; c < squares[r].length; c++) {
    const square = squares[r][c];
    const div = square.div;

    div.addEventListener(
      "mousedown",
      function(event) {
        createMouseDownHandler(square, event);
      },
      false
    );

    div.addEventListener("mouseup", createMouseUpHandler, false);

    div.addEventListener(
      "mouseover",
      createHoverHandler(square),
      false
    );
  }
}