<!DOCTYPE html>
<html lang="en">
<head>
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
      width: 800px; /* match image size */
    }
    .image-container img {
      width: 100%;
      display: block;
    }
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      transform-origin: top left;
      background: rgba(255, 0, 0, 0.2);
      color: white;
      font-size: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
    <div class="image-container">
        <img src="bg.jpg" alt="Background">
        <div class="overlay">Hi</div>
    </div>

    <script>
        // Image size
        const w = 800, h = 425;

        // Target quadrilateral corners (x, y)
        const corners = [
            [143, 139], // top-left
            [624, 126], // top-right
            [628, 262], // bottom-right
            [147, 275]  // bottom-left
        ];

        const overlay = document.querySelector(".overlay");

        // Compute homography transform
        function getTransform(from, to) {
            // from = source rectangle: [ [0,0], [w,0], [w,h], [0,h] ]
            // to = destination quadrilateral
            const A = [];
            for (let i = 0; i < 4; i++) {
                const [x, y] = from[i], [X, Y] = to[i];
                A.push([x, y, 1, 0, 0, 0, -X*x, -X*y]);
                A.push([0, 0, 0, x, y, 1, -Y*x, -Y*y]);
            }
            const b = to.flat();
            const hVec = numeric.solve(A, b).concat(1); // solve linear system
            const H = [
                [hVec[0], hVec[1], hVec[2]],
                [hVec[3], hVec[4], hVec[5]],
                [hVec[6], hVec[7], hVec[8]]
            ];
            return H;
        }

        function matrix3dFromHomography(H, w, h) {
            return `matrix3d(${H[0][0]}, ${H[1][0]}, 0, ${H[2][0]},
                           ${H[0][1]}, ${H[1][1]}, 0, ${H[2][1]},
                           0, 0, 1, 0,
                           ${H[0][2]}, ${H[1][2]}, 0, ${H[2][2]})`;
        }

        // Source rectangle (overlay div in its natural state)
        const from = [[0,0], [w,0], [w,h], [0,h]];
        const H = getTransform(from, corners);
        overlay.style.transform = matrix3dFromHomography(H, w, h);
    </script>

    <!-- numeric.js is needed for solving linear systems -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>
</html>