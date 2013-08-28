PlageAstreinte = {
  module : "astreintes",
  lastList : "",
  user_id : "",

  showForUser: function(user_id) {
    new Url("astreintes", "ajax_plage_astreinte").
      addParam("user_id", user_id).
      popup(600, 300);  //popup is better
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

  refreshList: function(target_id, user_id) {
    if (this.lastList || target_id) {
      if (user_id != null) {this.user_id = user_id;}
      if (target_id != null) {this.lastList = target_id;}
      var url = new Url("astreintes", "vw_idx_plages_astreinte");
      url.addParam("user_id", this.user_id);
      url.requestUpdate(this.lastList);
    }
  },

  content: function() {
    var url = new Url("astreintes", "vw_planning_astreinte");
    url.addParam("affiche_nom", 0);
    url.requestUpdate("planningconge");
  },

  modal: function(plage_id, date, hourstart, minutestart, callback) {
    var url = new Url("astreintes", "ajax_edit_plage_astreinte");
    url.addParam("plage_id", plage_id);
    url.addParam("date", date);
    url.addParam("hour", hourstart);
    url.addParam("minutes", minutestart);
    url.requestModal(500,300);
    if (callback) {
      url.modalObject.observe('afterClose', callback);
    }

  },

  modaleastreinteForDay: function(date) {
    var url = new Url("astreintes", "ajax_list_day_astreinte");
    url.addParam("date", date);
    url.requestModal(500,500);
  }
};
