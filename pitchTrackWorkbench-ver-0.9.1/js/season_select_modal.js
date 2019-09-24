$(document).ready(()=>{

  $('.season-delete-modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var id = button.data('id')
    var subject = button.data('title')
    var modal = $(this)
    console.log(modal);
    modal.find('.modal-title').text('Are you sure you want to Delete ' + subject+'? ')
    modal.find('.delete-season').attr('data-id', id);

    var params = 'team_id='+id;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var modalBodyHtml = "<table class='game-stat table table-striped table-border'>"+
                            "<thead><tr><th scope='col'>Pitcher Name</th>"+
                            "</tr> </thead><tbody>";




        JSON.parse(this.responseText).forEach((item)=>{
          modalBodyHtml+="<tr><td>"+item.pitcher_name+"</td></tr>";
        });
        modalBodyHtml+="</tbody></table>";
        console.log(modalBodyHtml);
        console.log("RESPONSE",this.responseText);
        modal.find('.modal-body').html(modalBodyHtml);



      }
    };

    xhttp.open("POST", "get-roster.php", true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhttp.send(params);

  });

  $('.delete-season').click((e)=>{
  var id = e.target.getAttribute('data-id');
  window.open("delete-team.php?id="+id);


  });


});
