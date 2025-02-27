<?php
$turns = []
class Turn {
    public $piece
    public $from
    public $to
    public $inhabitant
    public $takes
    public $check
    public $checkmate
    public $failed
    public $algebraic
    function __construct($piece,$from,$to,$inhabitant,$takes,$check, $checkmate, $failed,$algebraic){
        $this->piece =$piece
        $this->from = $from
        $this->to = $to
        $this->inhabitant = $inhabitant
        $this->takes = $takes
        $this->check = $check
        $this->checkmate = $checkmate
        $this->failed = $failed
        $this->algebraic = $algebraic
        turns.push(this)
        if (this.piece.type == "opawn"){
            $this->algebraic = ""
        } else {
            $this->algebraic = this.piece.type[0].toUpperCase()
        }
        if ($takes == true){
            if (this.piece.type == "opawn"){
                $this->algebraic += files[this.from.col]
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
?>