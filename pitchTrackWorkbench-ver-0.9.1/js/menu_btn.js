

$(document).ready(function() {

	  document.getElementById("left-nav-menu").style.left = "-18vw";

  $('#menu-btn').click(function(e) {
			console.log("Event object of menu:", e.target.parentNode);
    if (document.getElementById("left-nav-menu").style.left == "-18vw") {
      document.getElementById("left-nav-menu").style.left = "0px";
      $(".main").get(0).addEventListener("click", closeLeftMenu, true);
      // $(".nav-close").get(1).addEventListener("click", closeLeftMenu, true);

    }
    else {

      closeAllMenus();
    }



  });

  $('#edit-roster').click((e)=>{
       window.location.href = "season_selection_menu.php";
  });
	$('#cubs-pitch').click((e)=>{
       window.location.href = "main.php";
  });

    $('#pitch-tracker').click((e)=>{
       window.location.href = "../../index.html";
  });

  function closeAllMenus() {
    document.getElementById("left-nav-menu").style.left = "-18vw";
    removeMenuListeners();

  }

  function closeLeftMenu(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById("left-nav-menu").style.left = "-18vw";
    removeMenuListeners();
  }

  function removeMenuListeners() {
    $(".main").get(0).removeEventListener("click", closeLeftMenu, true);
    // $(".nav-close").get(1).removeEventListener("click", closeLeftMenu, true);

  }


});
