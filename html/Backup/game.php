<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            $(document).ready(function(){
                for(var i = 0; i < 10; i++){
                    var row = document.createElement("div")
                    row.id = "row" + i
                    row.className = "row"
                    for(var j = 0; j < 10; j++){
                        var square = document.createElement("div")
                        var num = ((i*10)+j)
                        square.id = "square" + num;
                        (function (i, j) {
                            square.onclick = function () {
                                var xml = new XMLHttpRequest();
                                xml.open('GET', 'Script.php?square=' + i + '' + j, true);
                                xml.send();
                            };
                        })(i, j);
                         if ((num + i) % 2 !== 0){
                            square.className = "light square"
                         } else {
                            square.className = "dark square"
                         }
                        row.appendChild(square)
                    }
                    document.body.appendChild(row)
                }
            })
        </script>
        <style>
            .square{
                width:60px;
                height:60px;
            }
            .light{
                background-color:#840;
            }
            .dark{
                background-color:#420;
            }
            .row{
                display:flex;
            }
        </style>
    </head>
    <body>

    </body>
</html>