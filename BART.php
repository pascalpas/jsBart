
<html>
<head>
    <title>jsTASKS: BART</title>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="../include/jquery-ui.min.js"></script>
<link href="../include/bootstrap-4.1.3-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<style>
.hidden {
  display: none;
}
body {
  background-color: #ddd;

}
.container {
    margin: 0px;
    padding: 0px;
}
.instructions {
    margin: 15px;
    padding: 10px;
}
.balloon {
    width: 50px;
    height: 50px; 
    opacity: 1; 
    top: 580px;
    left: 300px;
    position:absolute; 
    padding:20px;
    border-radius:50%; 
}
.balloon_blue {
    background-color: #ff0000;
    background: -webkit-radial-gradient(65% 15%, circle, white 1px, #049bf2 3%, darkblue 60%, #049bf2 100%); 
    background: -moz-radial-gradient(65% 15%, circle, white 1px, #049bf2 3%, darkblue 60%, #049bf2 100%); 
    background: -o-radial-gradient(65% 15%, circle, white 1px, #049bf2 3%, darkblue 60%, #049bf2 100%);
    background: radial-gradient(circle at 65% 15%, white 1px, #049bf2 3%, darkblue 60%, #049bf2 100%);  
}
.balloon_orange {
    background-color: #ff0000;
    background: -webkit-radial-gradient(65% 15%, circle, white 1px, #ffa500 3%, #ff4500 60%, orange 100%); 
    background: -moz-radial-gradient(65% 15%, circle, white 1px, #ffa500 3%, #ff4500 60%, orange 100%); 
    background: -o-radial-gradient(65% 15%, circle, white 1px, #ffa500 3%, #ff4500 60%, orange 100%);
    background: radial-gradient(circle at 65% 15%, white 1px, #ffa500 3%, #ff4500 60%, orange 100%);  
}
.balloon_yellow {
    background-color: #ff0000;
    background: -webkit-radial-gradient(65% 15%, circle, white 1px, #ffee00 3%, #b2a70e 60%, yellow 100%); 
    background: -moz-radial-gradient(65% 15%, circle, white 1px, #ffee00 3%, #b2a70e 60%, yellow 100%); 
    background: -o-radial-gradient(65% 15%, circle, white 1px, #ffee00 3%, #b2a70e 60%, yellow 100%);
    background: radial-gradient(circle at 65% 15%, white 1px, #ffee00 3%, #b2a70e 60%, yellow 100%);  
}
.ground{
  background-color: gray;
  opacity: 0.3;
  position : absolute ;
  left : 290px;
  top : 760px;
  width: 50px;
  height: 20px;
  border-radius: 100%;
}
.box {
    width: 700px;
    height: 700px;
    border-color: #777; 
    border-style: solid;
    float:left;
    margin:5px;
    background-color: #eee;
    -webkit-box-shadow: inset 1px 1px 5px 1px rgba(0,0,0,0.75);
    -moz-box-shadow: inset 1px 1px 5px 1px rgba(0,0,0,0.75);
    box-shadow: inset 1px 1px 5px 1px rgba(0,0,0,0.75);
}
.banner {
    height:40px;
}
.scoreboard {
  font-size: 30px;
  margin-left: 70%;
}
.btns {
  margin-top: 400px;
  font-size: 30px;
  margin-left: 70%;
}
.btns-top {
  margin-top: 40px;
  font-size: 30px;
  margin-left: 70%;
}
.explosion {
  position: absolute; 
  width: 600px;
  height: 600px;
  pointer-events: none; /
  .particle {
    position: absolute; 
    width: 10px;
    height: 10px;
    border-radius: 50%;
    animation: pop 1s reverse forwards; 
  }
}
</style>
</head>
<body id="mainArea">
<div class="container" id="mainContent">
  
</div>
</body>
<script>

<?php
  $seqColor = array();
  $seqPop = array();
  $seqPoints = array();

    if ($_GET['seq']) {
        $subID = $_GET['subjectID']; 
        $seqFile = 'sequences/' . $_GET['seq'];
    } else {
       $subID = $_POST['subjectID']; 
       $seqFile = $_POST['seq'];
    }
   
    $file = fopen($seqFile,"r"); 

  if ($file) {
    while (($line = fgets($file)) !== false) {
        $vals = preg_split('/\s+/', $line);
        array_push($seqColor, $vals[0]);
        array_push($seqPop, $vals[1]);
        array_push($seqPoints, $vals[2]);
    }
  }
  $nPracticeTrials = (array_sum($seqPractice) - 1); 
  fclose($file);
  $seqmode = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($seqFile));
  $expFile = preg_replace('/\_training/', '', basename($seqFile));
  
  echo  "var subjectID = '" . $subID . "'; \n";
  echo  "var seqMode = '" . $seqmode . "'; \n";
  echo  "var cleanMode = '" . $_GET['clean'] . "'; \n";

  if (strpos($seqmode,'training') !== false) {
    echo "var isTraining = 1;";
  } else {
    echo "var isTraining = 0;";
  }
?>

// write sequence vars as javascript
var seqColor = [<?php foreach($seqColor as $val) { echo $val . ','; } ?>];
var seqPop = [<?php foreach($seqPop as $val) { echo $val . ','; } ?>];
var seqPoints = [<?php foreach($seqPoints as $val) { echo $val . ','; } ?>];

var trialStart = 0;
var expStartT = 0;

var maxSize = 0;
var isSaved = 0;
var currCash = 0;
var currBank = 0;
var currTrial = 0;

var nBalloons = seqColor.length;

// create data storage variables
var dataRT = new Array;
var dataCumulRT = new Array;
var dataColor = new Array;
var dataCash = new Array;
var dataBank = new Array;
var dataPop = new Array;
var dataTrial = new Array;
var dataSize = new Array;

var dataIsSaved = new Array;
var dataIsPopped = new Array;
var dataIsMax = new Array;
var dataIsPractice = new Array;

var maxBalloonSize = 32;

function startExp() {
    $(".totalTrial").text(nBalloons);
    expStartT = (new Date).getTime();
    deflate();
}

// progress variables
var puffRT = 0;
var cummulPuffRT = 0;

function saveData() {
  $.ajax({
    type: 'POST',
    url: 'saveLog.php',                
    data: {seqMode:seqMode,subjectID:subjectID,dataTrial:dataTrial,dataSize:dataSize,dataRT:dataRT,dataCumulRT:dataCumulRT,dataColor:dataColor,dataPop:dataPop,dataCash:dataCash,dataBank:dataBank,dataIsSaved:dataIsSaved,dataIsPopped:dataIsPopped,dataIsMax:dataIsMax},
  success: function(data) {}  
  })
}

function expFinished() {
    saveData();
    closeFullscreen();
    document.getElementById("mainContent").innerHTML = `<div class="parent">
  <div class="instructions">
    <h1>Einde Experiment.</h1><hr>
      <p>...</p>
      <hr><button onclick="window.location.href='#'" class="btn btn-secondary" id="btnStart"></button></div>`;
}

function practiceFinished() {
    saveData();
    document.getElementById("mainContent").innerHTML = `<div class="parent">
  <div class="instructions">
    <h1>Einde Oefening.</h1><hr>
      <p>Klik op 'starten' om aan het echte experiment te beginnen.</p>
      <hr><button onclick="window.location.href='BART.php?subjectID=<?php echo $subID; ?>&seq=<?php echo $expFile; ?>&clean=1'" class="btn btn-secondary" id="btnStart">Starten</button></div>`;
}

// LOGGING
function addToLog(scoreType) {
    if (scoreType > 0) {
        dataTrial.push(getCurrTrial());
        dataSize.push(getCurrSize());
        dataRT.push(puffRT);
        dataCumulRT.push(cumulPuffRT);
        dataColor.push(getColor());
        dataPop.push(getPop());
        dataCash.push(parseFloat($(".score").text()));
        dataBank.push(parseFloat($(".tscore").text()));
        dataIsSaved.push(isSaved);
        if (getPop() == getCurrSize()) { 
            dataIsPopped.push(1);
        } else {
            dataIsPopped.push(0);
        }

        if (getCurrSize() == maxBalloonSize) { 
            dataIsMax.push(1);
        } else {
            dataIsMax.push(0);
        }
        
    } else { // final push
        dataTrial.push(NaN);
        dataSize.push(NaN);
        dataRT.push(NaN);
        dataCumulRT.push(NaN);
        dataColor.push(NaN);
        dataPop.push(NaN);
        dataCash.push(NaN);
        dataBank.push(parseFloat($(".tscore").text()));
        dataIsSaved.push(NaN);
        dataIsPopped.push(NaN);
        dataIsMax.push(NaN);
    }
}

// SEQUENCE FUNCTIONS
function getPop() { 
    var iPop = seqPop[getCurrTrial() - 1];
    console.log('Will pop at ' + iPop);
    return iPop;
}
function getColor() {
    var iCol = seqColor[getCurrTrial() - 1];
    return iCol;
}
function getPoints() {
    var iPoints = seqPoints[getCurrTrial() - 1];
    return iPoints;
}
function getPractice() { 
    var iPractice = seqPractice[getCurrTrial() - 1];
    return iPractice;
}
function getCurrTrial() { // read scoreboard
    var $currTrial = parseInt($(".iTrial").text());
    return $currTrial;
}

// LOGGING AND DISPLAY
function remTrial() { // remember trial progress, add to scoreboard
    var $currTrial = parseInt($(".iTrial").text()) + 1;
    $(".iTrial").text($currTrial);
    $(".pumpMoney").text(getPoints());

    console.log('Trial: ' + $currTrial);
    if ($currTrial > nBalloons) {
        if (isTraining == 1) {
            practiceFinished(); // when no trials are left in practice
        } else {
            expFinished(); // when no trials are left in sequence
        }
    }
}

function getCurrSize() { // read scoreboard
    var $currSize = parseInt($(".BalloonSize").text());
    if (isSaved == 1) { $currSize = $currSize + 1; }
    return $currSize;
}
function remSize() { // remember number of puffs,  add to scoreboard
    var $currSize = getCurrSize() + 1;
    $(".BalloonSize").text($currSize);
    console.log('Balloon Size: ' + $currSize);
}

function scoreAdd() { // update scoreboard
    var $newScore = parseFloat($(".score").text()) + getPoints();
    $(".score").text($newScore);
}

function manScoreSave() {
    isSaved = 1;
    scoreSave();
}

function scoreSave() {
    if (getCurrSize() > 0) { 
        addToLog(1);
        disableBtn("btnSave");
        if (document.getElementById("btnInflate").disabled == false) {
            disableBtn("btnInflate");
        }
        var $cumulScore = parseFloat($(".score").text()) + parseFloat($(".tscore").text());
        $(".tscore").text($cumulScore);
        console.log("Bank: " + $cumulScore);
        $(".score").text("0");
        $("#balloon").animate({
            opacity: 0,
        }, 100, "linear");
        setTimeout(function() {
            deflate();
        }, (350));
    }
}
function scoreClear() {
    $(".score").text("0");
}
function disableBtn(id) {
    var zbtn = document.getElementById(id);
    zbtn.disabled = true;
    setTimeout(function() {
        zbtn.disabled = false;
    }, (1000));
}
function disableBtnPerm(id) {
    var zbtn = document.getElementById(id);
    zbtn.disabled = true;
}
function enableBtn(id) {
    var zbtn = document.getElementById(id);
    zbtn.disabled = false;
}

function checkMax() { 
    var $SizeToBe = parseInt($(".BalloonSize").text()) + 1;
    if ($SizeToBe == getPop()) {
        setTimeout(function() {
            remSize();
            scoreClear();
            addToLog(1);
            pop();
        }, (100));
    } else if ($SizeToBe == maxBalloonSize) {
        remSize();
        scoreAdd();
        disableBtnPerm("btnInflate");
        setTimeout(function() {
            scoreSave();
        }, (100));
    } else {
        return false;
    }
}

// ANIMATIONS
function inflate() {
    puffRT = (new Date).getTime() - trialStart;
    cumulPuffRT = (new Date).getTime() - expStartT;

    var ibtn = document.getElementById("btnInflate");
    ibtn.disabled = true;

    var btn = document.getElementById("btnSave");
    btn.disabled = false;

    // balloon
    var p = $("#balloon");
    $newWidth = 16 + p.outerWidth();
    $newHeight = 16 + p.outerHeight();
    $newLeft = $("#balloon").offset().left - 7.5;
    $newTop = $("#balloon").offset().top - 23;
    $balloonPos = $newTop;
    $("#balloon").animate({
        left: $newLeft + "px",
        top: $newTop + "px",
        width: $newWidth + "px",
        height: $newHeight + "px",
        opacity: 0.7
    }, 150);

    // shadow
    var p = $(".ground");
    $newWidth = 14 + p.outerWidth();
    $newHeight = 1 + p.outerHeight();
    $newLeft = $(".ground").offset().left - 7;
    $newTop = $(".ground").offset().top - 1;
    $(".ground").animate({
        left: $newLeft + "px",
        width: $newWidth + "px",
        height: $newHeight + "px",
        top: $newTop + "px"
    }, 150);

    // flutter
    $newTop = $balloonPos + 5;
    $("#balloon").animate({
        top: $newTop + "px",
        opacity: 1
    }, 200);

    if (checkMax() == false) {
        setTimeout(function() {
            remSize();
            addToLog(1);
            scoreAdd();
            ibtn.disabled = false;
        }, (200));
    };  
};

function pop() {
    disableBtnPerm("btnInflate");
    disableBtnPerm("btnSave");

    var p = $("#balloon");
    $newWidth = 14.5 + p.outerWidth();
    $newHeight = 14.5 + p.outerHeight();
    $newLeft = $("#balloon").offset().left - 7.5;
    $newTop = $("#balloon").offset().top - 20;
    $balloonPos = $newTop;
    $("#balloon").animate({
        left: $newLeft + "px",
        top: $newTop + "px",
        width: $newWidth + "px",
        height: $newHeight + "px",
        opacity: 0
    }, 30);
    setTimeout(function() {
        $(".ground").animate({
            opacity: 0
        }, 30);
    }, (200));
    $centerLeft = $("#balloon").offset().left + (p.outerWidth() / 2);
    $centerTop = $("#balloon").offset().top + (p.outerHeight() / 2);
    setTimeout(function() {
        explode($centerLeft, $centerTop);
    }, (300));
    setTimeout(function() {
        deflate();
    }, (2000));
};

function deflate() { // start of each trial, return balloon to original position
    trialStart = (new Date).getTime();
    isSaved = 0;
    maxSize = 0;
    remTrial();

    $("#balloon").animate({
        width: "50px",
        height: "50px",
        opacity: 0,
        top: "580px",
        left: "300px"
    }, 1);

    setTimeout(function() {
        if (getColor() == 1) {
            document.getElementById("balloon").className = "balloon balloon_blue";
        } else if (getColor() == 2) {
            document.getElementById("balloon").className = "balloon balloon_orange";
        } else {
            document.getElementById("balloon").className = "balloon balloon_yellow";
        }
    }, (150));

    setTimeout(function() {
         $("#balloon").animate({
            width: "50px",
            height: "50px",
            opacity: 1,
            top: "580px",
            left: "300px"
        }, 100, "linear");
    }, (50));

    var btn = document.getElementById("btnSave");
    btn.disabled = true;

    disableBtn("btnInflate");
    // reset position and size

    setTimeout(function() {
         $(".ground").animate({
            width: "50px",
            height: "20px",
            top: "660px",
            left: "300px",
            opacity: 0.3
        }, 60);
    }, (75));

    // reset size counter
    $(".BalloonSize").text('0');
};

function flutter() {
    if (document.getElementById("btnInflate").disabled == false) {
        disableBtn("btnInflate",600);
        disableBtn("btnSave",600);
        // up
        $newTop = $("#balloon").offset().top - 10;
        $("#balloon").animate({
            top: $newTop + "px"
        }, 600);
        var p = $(".ground");
        $newWidth = p.outerWidth() - 5;
        $(".ground").animate({
            width: $newWidth + "px"
        }, 600);

        // down
        $newTop = $newTop + 10;
        $("#balloon").animate({
            top: $newTop + "px"
        }, 600);
        $newWidth = $newWidth + 5;
        $(".ground").animate({
            width: $newWidth + "px"
        }, 600);
    }
};
const bubbles = 80;
const explode = (x, y) => {
    let particles = [];
    let ratio = window.devicePixelRatio;
    let c = document.createElement('canvas');
    let ctx = c.getContext('2d');

    c.style.position = 'absolute';
    c.style.left = (x - 800) + 'px';
    c.style.top = (y - 800) + 'px';
    c.style.pointerEvents = 'none';
    c.style.width = 1600 + 'px';
    c.style.height = 1600 + 'px';
    c.style.zIndex = 100;
    c.width = 300 * ratio;
    c.height = 300 * ratio;
    document.body.appendChild(c);

    if (getColor() == 1) {
        colors = ['blue', 'darkblue','#68b1ff'];
    } else if (getColor() == 2) {
        colors = ['orange', 'DarkOrange','#ba7c00'];
    } else {
        colors = ['yellow', 'darkyellow', '#d8ad31'];
    }

    for (var i = 0; i < bubbles; i++) {
        particles.push({
            x: c.width / 2,
            y: c.height / 2,
            radius: r(20, 30),
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: r(0, 360, true),
            speed: r(8, 82),
            friction: 0.9,
            opacity: r(0, 0.5, true),
            yVel: 0,
            gravity: 0.1
        });
    }

    render(particles, ctx, c.width, c.height);
    setTimeout(() => document.body.removeChild(c), 500);
}

const render = (particles, ctx, width, height) => {
    requestAnimationFrame(() => render(particles, ctx, width, height));
    ctx.clearRect(0, 0, width, height);

    particles.forEach((p, i) => {
        p.x += p.speed * Math.cos(p.rotation * Math.PI / 180);
        p.y += p.speed * Math.sin(p.rotation * Math.PI / 180);

        p.opacity -= 0.01;
        p.speed *= p.friction;
        p.radius *= p.friction;
        p.yVel += p.gravity;
        p.y += p.yVel;

        if (p.opacity < 0 || p.radius < 0) return;

        ctx.beginPath();
        ctx.globalAlpha = p.opacity;
        ctx.fillStyle = p.color;
        ctx.arc(p.x, p.y, p.radius, 0, 2 * Math.PI, false);
        ctx.fill();
    });

    return ctx;
}
const r = (a, b, c) => parseFloat((Math.random() * ((a ? a : 1) - (b ? b : 0)) + (b ? b : 0)).toFixed(c ? c : 0));

// INSTRUCTIONS
function changeEnglish() {
    isEnglish = true;
    console.log('blaaa');
    firstInstructions();
}

function firstInstructions() {
    if (typeof isEnglish == 'undefined') {
       document.getElementById("mainContent").innerHTML = `<div class="parent">
        <div class="instructions">
        <h1>Instructies.</h1><hr><p>Deze taak werkt het beste in volledig-scherm. Mocht je browser dit niet ondersteunen, ga dan verder in het huidige venster. Zorg er wel voor dat je het venster van de browser zo groot mogelijk maakt.</p>
        <hr>
       <button onclick="changeEnglish()" class="btn btn-default" id="btnStart">English instructions</button>
        <button onclick="openFullscreen(1)" class="btn btn-secondary" id="btnStart">Ga verder in volledig-scherm modus</button> <button onclick="openFullscreen(0)" class="btn btn-secondary" id="btnStart">Ga verder in huidig venster</button></div></div>`; 
      } else {
              document.getElementById("mainContent").innerHTML = `<div class="parent">
        <div class="instructions">
        <h1>Instructions.</h1><hr><p>This tasks works best full-screen. If your browser doesn't support this, you can continue in windowed-mode. Make sure the browser window is as large as possible.</p>
        <hr>
        <button onclick="openFullscreen(1)" class="btn btn-secondary" id="btnStart">Continue in full-screen mode</button> <button onclick="openFullscreen(0)" class="btn btn-secondary" id="btnStart">Continue in current window</button></div></div>`; 
      }
}
    

function showInstructions() {
if (typeof isEnglish == 'undefined') {
    document.getElementById("mainContent").innerHTML = `<div class="parent">
      <div class="instructions">
        <h1>Instructies.</h1><hr>
            <p>In het experiment zult u `+ nBalloons +` ballonnen oppompen. U kunt met de muis op ‘Pomp’ klikken. Hiermee kunt u punten verdienen. Deze komen in een tijdelijk register.</p>
         <p>De ballonnen kunnen voortijdig knappen. Het is aan u om te beslissen hoeveel u elke ballon wilt oppompen. U kunt elk moment stoppen en de punten in het tijdelijk register verzilveren door op ‘Verzilver punten’ te klikken. Als de ballon knapt voordat u de punten heeft verzilverd zullen de punten verloren gaan.</p>
         <p>Als een ballon knapt of u verzilverd de punten, verschijnt de volgende ballon op uw scherm. <strong>Uw doel is om zoveel mogelijk punten te verzamelen</strong>.</p>
         <p>Er zullen verschillende kleuren ballonnen langskomen. <strong>Blauw</strong>, <strong>oranje</strong> en <strong>geel</strong>. Sommige kleuren leveren meer punten per pomp op, maar zij hebben ook meer kans om vroegtijdig te knappen.</p>
         <p>Let op: Sommige ballonnen kunnen na slechts één keer pompen ploffen. Andere ballonnen kunnen volledig opgepompt worden.</p>
         <p>Als u klaar bent om aan het experiment te beginnen, klik op <em>Start</em>..</p>

          <hr>
          <button onclick="changeEnglish()" class="btn btn-secondary" id="btnStart">English instructions</button>
          <button onclick="setupTask()" class="btn btn-secondary" id="btnStart">Start</button></div></div>`;
    } else {
    document.getElementById("mainContent").innerHTML = `<div class="parent">
      <div class="instructions">
        <h1>Instructions.</h1><hr>
            <p>In this experiment you’ll pump `+ nBalloons +` balloons. You can click on “Pomp” with the mouse. This will result in getting you points. These points will be saved in a temporary register. However, these balloons can blow up/pop prematurely. It’s on you to decide when to stop pumping every balloon.</p>

    <p>You can stop at any moment and cash in the points you have saved up in your temporary register. But if you have not cashed out the points in the temporary register and a balloon pops prematurely as described above, all the points you have earned and have saved up in the temporary register until then will be removed. The next balloon will be shown when you decide to cash out or when a balloon pops.</p>

    <p>There are a few different colored balloons you will see in this experiment. In particular the colors <strong>blue</strong>,  <strong>orange</strong> and  <strong>yellow</strong>. The different colors result in different points per pump, some give fewer points per pump, some more. However, these balloons also have a higher chance to pop prematurely. </p>

    <p>Warning: Some balloons may pop after clicking just once at “Pomp”, others can be pumped infinitely. </p>

    <p>If you understand the above instructions and are ready to begin the experiment, click on <em>Start</em>.</p>

    <hr>
          <button onclick="setupTask()" class="btn btn-secondary" id="btnStart">Start</button></div></div>`;
      };
}

     
function setupTask() {
    document.getElementById("mainContent").innerHTML = `<div class=box></div><div class="banner"></div><div class=scoreboard>
    <span class="badge badge-warning" style="min-width:300px; border-radius: .25rem .25rem 0 0">Huidige score:  <span id="score" class="score">0</span></span>
    <span id="currScore" class="badge badge-primary" style="min-width:300px; border-radius: 0 0 .25rem .25rem">Totaal: <span id="tscore" class="tscore">0</span></span>
    </div>

    <div class=scoreboard><small>
    <span class="badge badge-light" style="min-width:145px;">Trial: <span id="iTrial" class="iTrial">0</span> / <span id="totalTrial" class="totalTrial">0</span></span>
    <span class="badge badge-light" style="min-width:145px;">Pompjes: <span id="BalloonSize" class="BalloonSize">0</span> / 32</span>
    </small>
    </div>

    <div class=btns-top>
         <div class="btn-group">
         <button class="btn btn-light" disabled><span id="pumpMoney" class="pumpMoney"></span> punt per pomp</button>
            <button onclick="manScoreSave()" class="btn btn-secondary" id ="btnSave">Verzilver huidige score <span class="fa fa-piggy-bank"></span></button>
        </div>
    </div>

    <div class=btns>
        <button onclick="inflate()" class="btn btn-secondary btn-lg btn-block" id="btnInflate">Pomp <span class="fa fa-plus-circle"></span></button>
   </div>
    <div id=balloon class="balloon balloon_red"></div>
    <div class=ground style="display:none"></div>`;
    startExp();
}
firstInstructions();
if (cleanMode == 1) {
    showFullScreenNotification(); 
} 
var elem = document.documentElement;
function openFullscreen(yesNo) {
	if (yesNo == 1) {
		  if (elem.requestFullscreen) { //
		    elem.requestFullscreen();
		    elem.mozRequestFullScreen();
		  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
		    elem.webkitRequestFullscreen();
		  } else if (elem.msRequestFullscreen) { /* IE/Edge */
		    elem.msRequestFullscreen();
		  }
	}
    if (cleanMode == 1) {
        setupTask(); // if practice trials were used, don't show instructions again
    } else {
        showInstructions();
    }

}

function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) { /* Firefox */
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE/Edge */
    document.msExitFullscreen();
  }
}

</script>