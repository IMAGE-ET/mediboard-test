

PlageConge = {
	showForUser: function(user_id) {
    new Url("dPpersonnel", "ajax_plage_conge").
      addParam("user_id", user_id).
      popup(400, 300);
	},

	loadUser: function(user_id, plage_id) {
	  var url = new Url("dPpersonnel", "ajax_plage_conge");
	  url.addParam("plage_id", plage_id);
	  url.addParam("user_id", user_id);
	  url.requestUpdate("vw_user");

    var user = $("u"+user_id);
    if (user) user.addUniqueClassName("selected");
	},

  // Select plage and open form
	edit: function(plage_id, user_id) {

	  var url = new Url("dPpersonnel", "ajax_edit_plage_conge");
	  url.addParam("plage_id", plage_id);
	  url.addParam("user_id", user_id);
	  url.requestUpdate("edit_plage");
		
	  var plage = $("p"+plage_id);
	  if (plage) plage.addUniqueClassName("selected");
	},
	
  content: function() {
	  var url = new Url("dPpersonnel", "vw_planning_conge");
    url.addParam("affiche_nom", 0);
    url.requestUpdate("planningconge");
  }
}
