PlageAstreinte = {
  module : "astreintes",

  showForUser: function(user_id) {
    new Url("astreintes", "ajax_plage_astreinte").
      addParam("user_id", user_id).
      popup(400, 300);
  },

  loadUser: function(user_id, plage_id) {
    var url = new Url("astreintes", "ajax_plage_astreinte");
    url.addParam("plage_id", plage_id);
    url.addParam("user_id", user_id);
    url.requestUpdate("vw_user");

    var user = $("u"+user_id);
    if (user) user.addUniqueClassName("selected");
  },

  // Select plage and open form
  edit: function(plage_id, user_id) {

    var url = new Url("astreintes", "ajax_edit_plage_astreinte");
    url.addParam("plage_id", plage_id);
    url.addParam("user_id", user_id);
    url.requestUpdate("edit_plage");

    var plage = $("p"+plage_id);
    if (plage) plage.addUniqueClassName("selected");
  },

  content: function() {
    var url = new Url("astreintes", "vw_planning_astreinte");
    url.addParam("affiche_nom", 0);
    url.requestUpdate("planningconge");
  },

  modal: function(plage_id, user_id) {
    var url = new Url("astreintes", "ajax_edit_plage_astreinte");
    url.addParam("plage_id", plage_id);
    url.addParam("user_id", user_id);
    url.requestModal();
    url.modalObject.observe("afterClose", function(){
      if(changedate){
        changedate();
      }
    });
  },

  modaleastreinteForDay: function(date) {
    var url = new Url("astreintes", "ajax_list_day_astreinte");
    url.addParam("date", date);
    url.requestModal(300,200);
  }
}
