/*global $*/
/*global PouchDB*/
/*global d3*/
$(document).ready(function() {
   //colors for pitch radio buttons and cirlce elements
  // [FASTBALL, CHANGEUP, SLIDER, CURVEBALL, OTHER ]
  const pitchColors = ["red", "blue", "green", "purple", "orange"];

document.body.style.setProperty('--FB-color',pitchColors[0]);
document.body.style.setProperty('--CH-color',pitchColors[1]);
document.body.style.setProperty('--SL-color',pitchColors[2]);
document.body.style.setProperty('--CB-color',pitchColors[3]);
document.body.style.setProperty('--OT-color',pitchColors[4]);

  // object structure for sql database
  var PlayerData = {
    // game session data(persist through game)
    objType: "1",
    /* jad */
    startingPitcher: true,
    //        player_id: "sql index"
    //        gameType : "GAME, InterSquad, OR BULLPEN ",
    //        date : "CURRENT DATE",
    //        time :"Current Time",
    //        team: "SRJC TEAM",
    //        opponent: "RIVAL TEAM",
    //        playerName : "PLAYER NAME",
  }

  // data to collect for each pitch
  var pitchData = [
    //     {
    //        objType: "2", /* jad */
    //        play: "BALL,STRIKE,HIT,MISS",
    //        pitchType: "CHANGEUP, SLIDER, FASTBALL, CURVEBALL",
    //        pitchSpeed:"MPH",
    //        xCoord: "GRAPHS X COORDINATE IN PERCENT",
    //        yCoord: "GRAPHS Y COORDINATE IN PERCENT",
    //        firstPitch: "BOOLEAN FOR FIRST PITCH AT EACH UP AT BAT",
    //        pitchCount: "CURRENT PITCH COUNT",
    //        endPlay:"WALK, STRIKEOUT, HIT",
    //        batterHandedness: "LEFT OR RIGHT HANDED"
    //    }
  ];

  //set dynamic style rules
  document.getElementById("left-nav-menu").style.left = "-17vw";
  $('.clear-header').css("height", $('#pitch-zone').height());


  /* Begin jad */
  var gl_newPitcher = false; /*switching new pitch to boolean 0=false 1=true*/
  /* End jad */
  var gameCount = { totalStrikes: 0, totalBalls: 0, pitchCount: 0, ballCount: 0, strikeCount: 0 };
  var db = new PouchDB("pitch");
  var myCircle;
  var newCircle = true;
  var redoHandlerInit = false;
  var mphValue = 0;
  var redoPitch = [];
  //pitcher id for pouchDB index
  var pitcher_id;
  var arcs, arc, pie, paths, strikePerc;

  renderPie([0,0]);
  change([0,1]);


  pouchHasPitcher().then((hasPitcher)=>{
      if(!hasPitcher){
        console.log("no data");
      }
  });








  $('#mySVG').click((evt) => {

    var event = evt.target.getBoundingClientRect();
    var outerRect = document.getElementById("rect").getBoundingClientRect();
    var x = (((evt.clientX - outerRect.left) / (outerRect.right - outerRect.left)) * 100).toString() + "%";
    var y = (((evt.clientY - outerRect.top) / (outerRect.bottom - outerRect.top)) * 100).toString() + "%";
    var svgNS = "http://www.w3.org/2000/svg";
    if (event.left === outerRect.left) {
      document.getElementById("switch_ball").checked = true;
         if (gameCount.ballCount == 3) {
          // document.getElementById('label_w').classList.add('warning_indicator');

      }
    }
    else {
      document.getElementById("switch_stk").checked = true;
      if (gameCount.strikeCount >= 2) {

        // document.getElementById('label_sOut').classList.add('warning_indicator');
      }
    }

          if (redoHandlerInit) {
        $('#data-entry').text("Enter");
        $('#data-entry').off('click');
        $('#data-entry').on('click', collectData);

        redoHandlerInit = false;
        newCircle = true;
        redoPitch = [];

      }



    if (newCircle) {
      myCircle = document.createElementNS(svgNS, "circle"); //to create a circle. for rectangle use "rectangle"
      myCircle.setAttributeNS(null, "class", "mycircle");
      myCircle.setAttributeNS(null, "cx", x);
      myCircle.setAttributeNS(null, "cy", y);
      myCircle.setAttributeNS(null, "r", 8);
      myCircle.setAttributeNS(null, "fill", "black");
      // myCircle.setAttributeNS(null, "stroke", "black");
      // myCircle.setAttributeNS(null, "stroke-width", "1px");
      myCircle.savedToGraph = false;

      document.getElementById("mySVG").appendChild(myCircle);
      newCircle = false;

        if (document.getElementById('mph-dropdown').classList.value != "mph-slide") {
      document.getElementById('mph-dropdown').classList.toggle('mph-slide');
    }
    }
    else {
      myCircle.setAttributeNS(null, "cx", x);
      myCircle.setAttributeNS(null, "cy", y);
    }




  });

  $('#data-entry').on("click", collectData);


  function collectData() {


    var pitchObject = {};

    pitchObject.objType = "2"; /* jad */

    if (isValid()) {
      pitchObject.xCoord = myCircle.getAttributeNS(null, "cx");
      pitchObject.yCoord = myCircle.getAttributeNS(null, "cy");

      if (pitchObject.firstPitch = document.getElementById('first-pitch').checked) {
        document.getElementById('first-pitch').checked = false;
      }

      if (document.getElementById("switch_lhh").checked) {
        pitchObject.batterHandedness = "Left";
      }
      else if (document.getElementById("switch_rhh").checked) {
        pitchObject.batterHandedness = "Right";
      }

      var radioEle = document.getElementsByName("switch_play");
      for (var i = 0; i < radioEle.length; i++) {
        if (radioEle[i].checked) {
          radioEle[i].checked = false;
          pitchObject.play = radioEle[i].getAttribute('value');
          if (pitchObject.play == "Strike") {
            gameCount.totalStrikes++;
            gameCount.strikeCount++;



          }
          else {
            gameCount.totalBalls++;
            gameCount.ballCount++;
            if (gameCount.ballCount == 3) {
              console.log("ball Count:", gameCount.ballCount);
            }
          }
        }
      }


      var radioEle = document.getElementsByName("switch_pitch");
      for (var i = 0; i < radioEle.length; i++) {
        if (radioEle[i].checked) {
          radioEle[i].checked = false;
          pitchObject.pitchType = radioEle[i].getAttribute('value');

          //assign colors to circles based on pitch selected

          pitchObject.pitchColor = pitchColors[i];


        }

      }

      var radioEle = document.getElementsByName("switch_end");
      pitchObject.endPlay = "continue";
      for (var i = 0; i < radioEle.length; i++) {
        if (radioEle[i].checked) {
          radioEle[i].checked = false;
          pitchObject.endPlay = radioEle[i].getAttribute('value');
          document.getElementById('first-pitch').checked = true;
          resetBatterStance();
          gameCount.ballCount = 0;
          gameCount.strikeCount = 0;
          // document.getElementById('label_sOut').classList.remove("warning_indicator");
          // document.getElementById('label_w').classList.remove("warning_indicator");

        }
      }

      pitchObject.pitchSpeed = mphValue;
      document.getElementById('mph-text').innerHTML = "00";
      mphValue = 0;
      myCircle.savedToGraph = true;
      gameCount.pitchCount++;

      pitchObject.gameCount = gameCount;
      pitchObject.date = getTodaysDate("yyyy/mm/dd");
      pitchObject.timeStamp = getCurrentTime();
      myCircle.setAttributeNS(null, "fill", pitchObject.pitchColor);
      newCircle = true;
      console.log("PLayer DATA:",PlayerData);
      /* Begin jad */
      if (gl_newPitcher) /*switching new pitch to boolean 0=false 1=true*/ {
        gl_newPitcher = false;
        pitchObject._id = new Date();
        // pitchObject.pitcher_id = PlayerData.pitcher_id;
        PlayerData._id = new Date();


        storePouch(PlayerData).then(function(res) {
          return storePouch(pitchObject);
        }).catch(function(err) {
          console.log(err)
        });

        // storeData(PlayerData, function(){
        //   storeData(pitchObject, null);
        // });


      }
      else {
        pitchObject._id = new Date();
        pitchObject.pitcher_id = PlayerData._id;
        console.log("pitch object:", pitchObject);
        storePouch(pitchObject).catch((err) => {
          console.log(err);
        });
        // storeData(pitchObject, null);
        //   console.log("Entering ONLY pitchObject.objType: " + pitchObject.objType);
      }
      /* End jad */
      pitchData.push(pitchObject);

      //updatedata on left panel ui
      updateGameCountUI(gameCount);



    }







  }

  function isValid() {
    var valid = true;
    if (newCircle) {
      alert('No coordinate point added to pitch zone! Add a coordinate to continue.');
      valid = false;
    }
    if (mphValue == 0) {
      document.getElementById('mph-ui').classList.add('ui_invalid');
      valid = false;
    }

    var batterUI = document.querySelectorAll("#batter-handedness label");
    if ((!document.getElementById("switch_lhh").checked) && (!document.getElementById("switch_rhh").checked)) {
      batterUI.forEach(function(item) {
        item.classList.remove("ui_invalid");
        setTimeout(function() {
          item.classList.add("ui_invalid");
        }, 1);

      });
      valid = false;
    }
    else {
      batterUI.forEach(function(item) { item.classList.remove("ui_invalid"); });
    }
    var radioEle = document.getElementsByName("switch_pitch");
    var wasChecked = false;
    for (var i = 0; i < radioEle.length; i++) {
      if (radioEle[i].checked) {
        wasChecked = true;
      }
    }
    if (!wasChecked) {
      valid = false;
      var pitchSelectUI = document.querySelectorAll("#pitch-fields label");
      pitchSelectUI.forEach(function(item) {
        item.classList.add('ui_invalid');

      });
    }

    return valid;




  }
  //Remove last entered pitch data
  $('#undo-entry').click((event) => {
    console.log("pitchData local length:", pitchData.length);
    if (pitchData.length > 0) {
      var circle = document.getElementsByClassName('mycircle');
      var lastElement = circle[circle.length - 1];
      if (lastElement.savedToGraph) {
        db.allDocs({ include_docs: true, startkey: '_', limit: 2, descending: true })
          .then(res => {
            console.log("pitchData length: ", pitchData.length);
            // console.log("response: ", res.rows);
            //     //place deleted pitch data in temporary array
            if (pitchData.length == 0) {

              updateGameCountUI(zeroGameCount(gameCount));
            }
            else {
              console.log("----------------response rows: ",res.rows);
              gameCount = res.rows[1].doc.gameCount;
              updateGameCountUI(gameCount);

            }

            return db.get(res.rows[0].id);



          }).then((doc) => {

            redoPitch.push({ circle: lastElement, pitchData: doc });

            return db.remove(doc);

          }).catch((err) => {
            console.log(err);
          });

        pitchData.pop();


      }
       if (!redoHandlerInit) {
          $('#data-entry').text("Redo");
          $('#data-entry').off('click');
          $('#data-entry').on('click', redoEntry);
          redoHandlerInit = true;

        }
      //Remove data from array and erase circle
      lastElement.parentNode.removeChild(lastElement);
    }
  });

  function redoEntry() {
    if (redoPitch.length > 0) {


      delete redoPitch[redoPitch.length - 1].pitchData._rev;


      gameCount = redoPitch[redoPitch.length - 1].pitchData.gameCount;
      updateGameCountUI(gameCount);

      pitchData.push(redoPitch[redoPitch.length - 1].pitchData);

      storePouch(redoPitch[redoPitch.length - 1].pitchData, "pitch").catch((err) => {
        console.log(err);

      });
      document.getElementById("mySVG").appendChild(redoPitch[redoPitch.length - 1].circle);
      redoPitch.pop();

    }




  }

  function zeroGameCount(gameCount) {

    gameCount.ballCount = 0;
    gameCount.strikeCount = 0;
    gameCount.totalBalls = 0;
    gameCount.totalStrikes = 0;
    gameCount.pitchCount = 0;

    return gameCount;
  }

  function getCurrentTime() {

    var today = new Date();
    return today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
  }

  function getTodaysDate(format) {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10) { dd = '0' + dd; }
    if (mm < 10) { mm = '0' + mm; }

    if (format === "yyyy/mm/dd") {
      return (yyyy + '/' + mm + '/' + dd);
    }
    else {
      return (mm + '/' + dd + '/' + yyyy);
    }

  }

  function resetBatterStance() {

    //    document.getElementById('right-batter').style.backgroundColor = "white";
    //    document.getElementById('right-batter').style.color = "black";
    //    document.getElementById('left-batter').style.backgroundColor = "white";
    //    document.getElementById('left-batter').style.color = "black";
    document.getElementById("switch_rhh").checked = false;
    document.getElementById("switch_lhh").checked = false;
    document.getElementById('right-batter').style.display = "none";
    document.getElementById('left-batter').style.display = "none";
  }

  var pitchersData = [];

  getPouchRoster().then((res) => {
    console.log("items", res);
    res.forEach((item) => {
      pitchersData.push(item);
      $('#pitcher-name').append("<option>" + item.pitcher_name + "</option");

    });


  }).catch((e) => {
    console.log("Error:", e)
  });




  var opposingTeams = ["SRJC", "Chabot", "De Anza", "Marin", "Laney", "Canada", "Solano", "Contra Costa", "Cabrillo", "San Juaquin Delta", "Modesto", "Diablo Valley", "Folsom Lake", "American River", "Cosumnes River", "Monterey Peninsula", "Sacromento City", "Seirra"];

  opposingTeams.forEach((team) => {
    $('#op-team-name').append(`<option>${team}</option`);

  });



  var mphFirstClick = true;
  var pitcherSelectInit = false;
  var styledCircle;



  // Open numerical keybaord
  $('#HUD-2 g ').click(function(event) {

    //Assign value from first click
    var value = $(this)[0].lastElementChild.innerHTML;
    if (mphFirstClick) {
      mphValue = 0;
      mphValue = value;
      mphFirstClick = false;
      styledCircle = this.children[0].style;
      styledCircle.fill = "red";

    }
    else {
      //Assign value from second click
      mphValue += value;
      document.getElementById('mph-dropdown').classList.toggle('mph-slide');
      mphFirstClick = true;
      styledCircle.fill = "white";
      document.getElementById('mph-text').innerHTML = mphValue;
      document.getElementById("mph-ui").classList.remove("ui_invalid");
    }

  });
  $('#mph-ui').click(function(event) {
    document.getElementById('mph-dropdown').classList.toggle('mph-slide');


  });
  $('#batter-handedness input').click((e) => {

    var backgrnClr = "#A90714";

    var batterUI = document.querySelectorAll("#batter-handedness label");
    batterUI.forEach(function(item) {
      item.classList.remove("ui_invalid");

    });

    if (e.target.id === "switch_lhh") {
      document.getElementById('right-batter').style.display = "none";
      document.getElementById('left-batter').style.display = "block";
    }
    else {
      document.getElementById('left-batter').style.display = "none";
      document.getElementById('right-batter').style.display = "block";
    }

  });
  $('#pitch-fields input').click(() => {
    var pitchSelectUI = document.querySelectorAll("#pitch-fields label");
    pitchSelectUI.forEach(function(item) {
      item.classList.remove("ui_invalid");

    });

  });

  $("#new-btn").click(()=>{
      $('#opening-menu').css('display', 'none');
      $('#select-menu').css('display', 'block');



  });

  $('#database-btn').click(()=>{

    window.location.href = "pitchTrackWorkbench-ver-0.9.1/php/main.php";
  });



  $('#start-btn').click(() => {
var valid = true;
  if((document.getElementById("pitcher-name").selectedIndex-1)!= -1){

    PlayerData.playerName = pitchersData[document.getElementById("pitcher-name").selectedIndex-1].pitcher_name;
    PlayerData.player_id = pitchersData[document.getElementById("pitcher-name").selectedIndex-1]._id;

    if((document.getElementById("game-type").selectedIndex-1)!= -1){
    PlayerData.gameType = $('#game-type').val();
    if($('#game-type').val() == "Game"){
      if((document.getElementById("op-team-name").selectedIndex-1)!= -1){
        PlayerData.opponent = $('#op-team-name').val();
        PlayerData.gameNum = $('#game-num').val()
      }else{
         document.getElementById("op-team-name").classList.add('ui_invalid');
         valid = false;
      }
    }else{
      PlayerData.opponent = "SRJC";
      PlayerData.gameNum = "1";
    }

    }else{
      document.getElementById("game-type").classList.add('ui_invalid');
    valid = false;
    }
  }else{
    document.getElementById("pitcher-name").classList.add('ui_invalid');
    valid = false;
  }

    if(valid){

    gl_newPitcher = true; /* jad */
    $('#select-menu').css('width', '0');
    document.getElementById('first-pitch').checked = true;
    PlayerData.objType = "1";
    PlayerData.date = getTodaysDate("yyyy/mm/dd");
    PlayerData.timeStamp = getCurrentTime();
    updateLeftPanel(PlayerData, { gameCount });
    }

  });

  $('#pitcher-name').change((e)=>{
    $('#game-type-group').css('opacity','1');
    $('#game-type-group').css('visibility','visible');
  });
  $('#pitcher-name').click((e)=>{
    document.getElementById("pitcher-name").classList.remove('ui_invalid');
  });

  $('#game-type').change((e)=>{
 if( e.target.value == "Game"){
   $('#op-team-group').css('opacity','1');
   $('#op-team-group').css('visibility','visible');
 }else{
   $('#op-team-group').css('opacity','0');
   $('#game-num-group').css('opacity','0');
   $('#op-team-group').css('visibility','hidden');
   $('#game-num-group').css('visibility','hidden');
 }
});

 $('#game-type').click((e)=>{
    document.getElementById("game-type").classList.remove('ui_invalid');
  });

$('#op-team-name').change((e)=>{
  $('#game-num-group').css('opacity','1');
  $('#game-num-group').css('visibility','visible');
});

 $('#op-team-name').click((e)=>{
    document.getElementById("op-team-name").classList.remove('ui_invalid');
  });


  $('#load-btn').click(() => {
    var tempPitcher;
    getPouchPitcher().then((res) => {
      tempPitcher = res;
      $('#start-background').css('width', '0');
      PlayerData._id = res._id;
      return getPouchPitches(res._id);
    }).then((res) => {

      var pitchesArray = pitchData = res.docs;

      updateLeftPanel(tempPitcher, pitchesArray[pitchesArray.length - 1]);
      gameCount = pitchesArray[pitchesArray.length - 1].gameCount;

      var svgNS = "http://www.w3.org/2000/svg";
      pitchesArray.forEach((item) => {

        myCircle = document.createElementNS(svgNS, "circle"); //to create a circle. for rectangle use "rectangle"
        myCircle.setAttributeNS(null, "class", "mycircle");
        myCircle.setAttributeNS(null, "cx", item.xCoord);
        myCircle.setAttributeNS(null, "cy", item.yCoord);
        myCircle.setAttributeNS(null, "r", 8);
        myCircle.setAttributeNS(null, "fill", item.pitchColor);
        // myCircle.setAttributeNS(null, "stroke", "black");
        // myCircle.setAttributeNS(null, "stroke-width", "1px");
        myCircle.savedToGraph = true;

        document.getElementById("mySVG").appendChild(myCircle);
        newCircle = true;
      });

    }).catch((err) => {
      console.log(err);
    });



  });

  function updateLeftPanel(pitcherObj, pitchObj) {

    $('#date').text(pitcherObj.date);
    $('#game-type-dspy').text(pitcherObj.gameType);
    $('#pitcher-name-dspy').text(pitcherObj.playerName);
    $('#op-team-name-dspy').text(pitcherObj.opponent);

    updateGameCountUI(pitchObj.gameCount);

  }

  function renderPie(data){


    var svg = d3.select("#strike-pie"),
         width = svg.attr("width"),
         height = svg.attr("height"),
        radius = Math.min(width, height) / 2,
        g = svg.append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        g.append("circle")
                          .attr("cx", 0)
                          .attr("cy", 0)
                          .attr("r", radius)
                          .attr("fill", "#F1F1F1");


        strikePerc = svg.append("text").attr(
									"transform", "translate(" + width/2 + "," + height/5 + ")")

   .attr('dy', '2em')
     .attr("text-anchor", "middle")
     .style("font-size", "15px")
     .style("text-decoration", "bold")
   .text("$");
        console.log("chart size: ", height);

    var color = d3.scaleOrdinal(['#4daf4a','#377eb8']);

    // Generate the pie
     pie = d3.pie();

    // Generate the arcs
     arc = d3.arc()
                .innerRadius(radius * .7)
                .outerRadius(radius);

    //Generate groups
    arcs = g.selectAll("arc")
                .data(pie(data))
                .enter()
                .append("g")
                .attr("class", "arc");

        arcs.transition()
      .duration(500)
      .attr("fill", function(d, i) { return color(i); })
      .attr("d", arc)
      .each(function(d) { this._current = d; }); // store the initial angles

    //Draw arc paths
    arcs.append("path")
        .attr("fill", function(d, i) {
            return color(i);
        })
        .attr("d", arc);

    paths = svg.selectAll('path');
  }
function change(data){
    var perc = 0;
    var total = data[0] + data[1];
    paths.data(pie(data));
    paths.transition().duration(750).attrTween("d", arcTween); // redraw the arcs
    if(total!=0){
    perc = ((data[0] / total) * 100);
    }

    strikePerc.text(perc.toFixed(1) + "%");


}

// Store the displayed angles in _current.
// Then, interpolate from _current to the new angles.
// During the transition, _current is updated in-place by d3.interpolate.
function arcTween(a) {
  console.log('arctween',a);
  var i = d3.interpolate(this._current, a);
  this._current = i(0);
  return function(t) {
    return arc(i(t));
  };
}
  function updateGameCountUI(x) {
    $('#pitch-count').text(x.pitchCount);
    $('#total-balls').text(x.totalBalls);
    $('#total-strikes').text(x.totalStrikes);
    $('#balls').text(x.ballCount);
    $('#strikes').text(x.strikeCount);
     change([x.totalStrikes, x.totalBalls]);
  }


  $('#switch_stk').click(()=>{
    if(gameCount.strikeCount >= 2){

      // document.getElementById('label_sOut').classList.add('warning_indicator');
    }

  });

  $('[name="switch_end"]').on("click", (e) => {
    if (e.target.clickedOnce) {
      e.target.clickedOnce = false;
      e.target.checked = false;
    }
    else {
      $('[name="switch_end"]').each((index) => {
        $('[name="switch_end"]')[index].clickedOnce = false;
      });
    }
    if (e.target.checked) {
      e.target.clickedOnce = true;
    }
  });

  $('#switch-pitcher').click(function() {



    $('#select-pitcher-screen').css('width', '83vw');
    $('#select-pitcher-screen').css('left', '17vw');

    if(!pitcherSelectInit){
      pitcherSelectInit = true;
      pitchersData.forEach((pitcher) => {
        $('#new-pitcher-select').append(`<option>${pitcher.pitcher_name}</option`);
    });
    }


  });

  $('#transfer-data').click(() => {
    $('#transfer-data').text("Starting transfer!");
    transferPouchToSql().then((res) => {
      console.log(res);

      $('#transfer-data').text("Transfer finished!");

      window.open("#");

    });

  });

  $('#view-data').click(()=>{
    window.location.href = "pitchTrackWorkbench-ver-0.9.1/php/main.php";
  });

  $('#enter-new-pitcher').click(() => {
    PlayerData.playerName = pitchersData[document.getElementById("new-pitcher-select").selectedIndex].pitcher_name;
    PlayerData.pitcher_id =  pitchersData[document.getElementById("new-pitcher-select").selectedIndex]._id;
    PlayerData.startingPitcher = false;

    //clear pitch graph
    var circles = document.getElementsByClassName('mycircle');
    if (circles.length > 0) {
      for (var i = circles.length - 1; i >= 0; i--) {
        document.getElementById('mySVG').removeChild(circles[i]);

      }
      newCircle = true;
    }




    //close nav menus
    document.getElementById("left-nav-menu").style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.width = "0px";
    removeMenuListeners();
    setTimeout(() => { document.getElementById('select-pitcher-screen').style.left = "17vw"; }, 1000);

    //update UI
    resetBatterStance();
    $('#pitcher-name-dspy').text(PlayerData.playerName);
    gl_newPitcher = true; /* jad */
    updateGameCountUI(zeroGameCount(gameCount));
    document.getElementById('first-pitch').checked = true;

  });

  $('#menu-btn').click(function() {

    if (document.getElementById("left-nav-menu").style.left == "-17vw") {
      document.getElementById("left-nav-menu").style.left = "0px";
      $(".nav-close").get(0).addEventListener("click", closeLeftMenu, true);
      $(".nav-close").get(1).addEventListener("click", closeLeftMenu, true);

    }
    else {

      closeAllMenus();
    }

  });

  $(".close-btn").click(function() {
    document.getElementById("left-nav-menu").style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.width = "0px";
    removeMenuListeners();
    setTimeout(() => { document.getElementById('select-pitcher-screen').style.left = "17vw"; }, 1000);

  });

  function closeAllMenus() {
    document.getElementById("left-nav-menu").style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.left = "-17vw";
    document.getElementById('select-pitcher-screen').style.width = "0px";
    removeMenuListeners();
    setTimeout(() => { document.getElementById('select-pitcher-screen').style.left = "17vw"; }, 1000);

  }

  function closeLeftMenu(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById("left-nav-menu").style.left = "-17vw";
    removeMenuListeners();
  }

  function removeMenuListeners() {
    $(".nav-close").get(0).removeEventListener("click", closeLeftMenu, true);
    $(".nav-close").get(1).removeEventListener("click", closeLeftMenu, true);

  }

  //definition for full screen mode
  function GoInFullscreen(element) {
    console.log(element);
	if(element.requestFullscreen)
		element.requestFullscreen();
	else if(element.mozRequestFullScreen)
		element.mozRequestFullScreen();
	else if(element.webkitRequestFullscreen)
		element.webkitRequestFullscreen();
	else if(element.msRequestFullscreen)
		element.msRequestFullscreen();
}
//definitijon for full screen exit
function GoOutFullscreen() {
	if(document.exitFullscreen)
		document.exitFullscreen();
	else if(document.mozCancelFullScreen)
		document.mozCancelFullScreen();
	else if(document.webkitExitFullscreen)
		document.webkitExitFullscreen();
	else if(document.msExitFullscreen)
		document.msExitFullscreen();
}

//open app in full screen mode using body elemnt
$('#fullscreen').click((e)=>{
  if(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || null){
    console.log("true");
     GoOutFullscreen($('body').get(0));
e.target.classList.remove("fa-compress");
e.target.classList.add("fa-expand");

  }else{
    GoInFullscreen($('body').get(0));
    e.target.classList.remove("fa-expand");
    e.target.classList.add("fa-compress");
  }
});




});
