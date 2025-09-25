<html>
    <head>
        <title>Unres Leaderboard as a Train Departure Board</title>
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
                width: 100%;
                max-width: 1200px; /* optional: limit max width */
            }

            .image-container img {
                width: 100%;
                height: auto;
                display: block;
            }

            .overlay {
                position: absolute;
                top: 40%;   /* % of image height */
                left: 30%;  /* % of image width */
                width: 100px;
                height: 100px;
                background: rgba(255, 0, 0, 0.5);
                pointer-events: none; /* so clicks pass through */
            }

        </style>
    </head>
    <body>

        <div class="image-container">
            <img src="bg.jpg" alt="Background">
            <div class="overlay">
        </div>

    </body>
</html>