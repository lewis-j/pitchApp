console.log("it ran");

$(document).ready(function() {


  $('.edit-player').click((e)=>{

      console.log("Target", e.target.parentElement.parentElement.getAttribute("data-id"));


      var pitcherName = e.target.parentElement.parentElement.getAttribute("data-name")
      var pitcher_id = e.target.parentElement.parentElement.getAttribute("data-id")
      var team_id = e.target.parentElement.parentElement.getAttribute("data-team-id")

      var f = document.createElement("form");
          f.setAttribute('method',"post");
          f.setAttribute('action',"edit-save.php");
          var i = document.createElement("input"); //input element, text
            i.setAttribute('name',"pitcher_name");
            i.setAttribute('value',pitcherName);
            i.setAttribute('id',"pitcher_name");

          var i2 = document.createElement("input"); //input element, text
            i2.setAttribute('name',"id");
            i2.setAttribute('value',pitcher_id);
            i2.setAttribute('type',"hidden");

          var i3 = document.createElement("input"); //input element, text
            i3.setAttribute('name',"team_id");
            i3.setAttribute('value',team_id);
            i3.setAttribute('type',"hidden");

            var s = document.createElement("input"); //input element, Submit button
            s.setAttribute('type',"submit");
            s.setAttribute('value',"Save");


          f.appendChild(i);
          f.appendChild(i2);
          f.appendChild(i3);
          f.appendChild(s);
          // e.target.parentElement.parentElement.appendChild(f);
          e.target.parentElement.parentElement.childNodes[1].innerText = "";
          e.target.parentElement.parentElement.childNodes[1].appendChild(f);
          console.log(e.target.parentElement.parentElement.childNodes[1].innerText);






});




  });
