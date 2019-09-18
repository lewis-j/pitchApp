console.log("it ran");

$(document).ready(function() {


  $('.edit-player').click((e)=>{

      console.log("Target", e.target.parentElement.parentElement.getAttribute("data-id"));


      var pitcherName = e.target.parentElement.parentElement.getAttribute("data-name")
      var pitcher_id = e.target.parentElement.parentElement.getAttribute("data-id")
      var team_id = e.target.parentElement.parentElement.getAttribute("data-team-id")
e.target.parentElement.parentElement.innerHTML =
"<form method='post' action='edit-save.php'>"+
"<input value='"+pitcherName+"' name='pitcher_name' id='pitcher_name'>"+
"<input value='"+pitcher_id+"' name='id' type='hidden'>"+
"<input value='"+team_id+"' name='team_id' type='hidden'>"+
"<input type='submit' value='Save'></form>";





});




  });
