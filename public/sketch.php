<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Stress Free - Drawing Page</title>
  <link rel="stylesheet" href="style5.css">
  <link rel="shortcut icon" type="image/png" href="favicon.png">

</head>
<body>
<!-- partial:index.partial.html -->
<link href='https://fonts.googleapis.com/css?family=Patrick+Hand' rel='stylesheet' type='text/css'>
<center>
  <p style="color:white;margin:0px 0px 25px;font-family:'Patrick Hand',cursive ;line-height:20px;font-size:45px">SKETCH</p>
  <canvas id="canvas"></canvas>
  <br>
  <button style="background:mediumseagreen" onclick="Restore()">Undo</button>
  <button style="background:goldenrod" onclick="Clear()">Clear</button>
  
  <br>
  <div onclick="strokeCOLOR(this)" style="background:coral" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:teal" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:orchid" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:deeppink" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:springgreen" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:royalblue" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:chocolate" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:gold" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:red" class="strokeColor"></div>
  <input type="color" oninput="strokeColor = this.value" style="height:35px" placeholder=" Other colours">
  </div>
  <br>
  <div onclick="strokeCOLOR(this)" style="background:orange" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:pink" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:purple" class="strokeColor"></div>
  <div onclick="strokeCOLOR(this)" style="background:maroon" class="strokeColor"></div>
  <input type="range" min="1" max="100" oninput="strokeWidth =this.value">
</center>
<!-- partial -->
  <script  src="./script5.js"></script>

</body>
</html>
