<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unres Leaderboard as a Train Station Departure Board</title>
  <style>
    body {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #000;
    }
    .image-container {
      position: relative;
      width: 800px;
    }
    .image-container img {
      width: 100%;
      display: block;
    }

    .overlay{
        position:absolute;
        width:482px;
        height:136px;
        left:145px;
        top:135px;
        transform: rotate(-1.6deg);
    }

    .overlay{
        background-color: #221;
        background-image:
        /*linear-gradient(to bottom, rgba(0,0,0,0.2), transparent),*/
        radial-gradient(rgba(200,255,155,0.1) 2px, transparent 2px);
        background-size: 6px 4px;
    }

    .overlay::after{
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image:
        linear-gradient(to bottom, rgba(0,0,0,0.2), transparent),
        linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, transparent 10%),
        linear-gradient(to left, rgba(0,0,0,0.2), transparent),
        linear-gradient(to left, rgba(0,0,0,0.6) 0%, transparent 5%);
    }
    @font-face {
    font-family: 'Ucka';
    src: url('ucka.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
    }

    .row{
        margin-top:12px;
        margin-left:12px;
        font-family: 'Ucka';
        letter-spacing: 2px;
        font-size:10px;

        color: #e80;
        text-shadow: 0 0 1px #fc1, 0 0 3px #fc1;
    }

  </style>
</head>
<body>
    <div class="image-container">
        <img src="bg.jpg" alt="Background">
        <div class="overlay">
                <div class="row">
                    <span>1st 11:83 Black Stompy</span>
                </div>


                <div class="row">
                    <span>2nd 11:45 Lurrus Vault</span>
                </div>


                <div class="row">
                    <span>3rd 11:34 Aggro Shops</span>
                </div>
        </div>
    </div>
</body>
</html>