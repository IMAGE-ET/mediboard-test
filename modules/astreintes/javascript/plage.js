PlageAstreinte = {
  module : "astreintes",
  lastList : "",
  user_id : "",

  modalList: null,

  showForUser: function(user_id) {
    new Url("astreintes", "ajax_plage_astreinte").
      addParam("user_id", user_id).
      popup(800, 300);  //popup is better
  },

  loadUser: function(user_id, plage_id) {
    new Url("astreintes", "ajax_plage_astreinte")
      .addParam("plage_id", plage_id)
      .addParam("user_id", user_id)
      .requestUpdate("vw_user");

    var user = $("u"+user_id);
    if (user) user.addUniqueClassName("selected");
  },

  // Select plage and open form
  edit: function(plage_id, user_id) {
    new Url("astreintes", "ajax_edit_plage_astreinte")
      .addParam("plage_id", plage_id)
      .addParam("user_id", user_id)
      .requestUpdate("edit_plage");

    var plage = $("p"+plage_id);
    if (plage) plage.addUniqueClassName("selected");
  },

  refreshList: function(target_id, user_id) {
    if (PlageAstreinte.lastList || target_id) {
      if (user_id != null) {PlageAstreinte.user_id = user_id;}
      if (target_id != null) {PlageAstreinte.lastList = target_id;}
      var url = new Url("astreintes", "vw_idx_plages_astreinte");
      url.addParam("user_id", PlageAstreinte.user_id);
      url.requestUpdate(PlageAstreinte.lastList);
    }
  },

  content: function() {
    new Url("astreintes", "vw_planning_astreinte")
      .addParam("affiche_nom", 0)
      .requestUpdate("planningconge");
  },

  modal: function(plage_id, date, hourstart, minutestart, callback) {
    var url = new Url("astreintes", "ajax_edit_plage_astreinte");
    url.addParam("plage_id", plage_id);
    url.addParam("date", date);
    url.addParam("hour", hourstart);
    url.addParam("minutes", minutestart);
    url.requestModal(700,300);
    url.modalObject.observe("afterClose", function() {
      if (callback) {
        callback();
      }
      else {
        location.reload();
      }
    });
  },

  modaleastreinteForDay: function(date) {
    var url = new Url("astreintes", "ajax_list_day_astreinte");
    if (date) {
      url.addParam("date", date);
    }
    url.requestModal(700);
  }
};
