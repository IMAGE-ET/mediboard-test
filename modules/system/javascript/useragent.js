UserAgent = window.UserAgent = {
  edit: function(id) {
    var url = new Url("system", "ajax_edit_user_agent");
    url.addParam("user_agent_id", id);
    url.requestModal(600, 350, {
      onClose: function(){
        UserAgent.refreshUALine(id);
      }
    });
  },

  openAuthentications : function(id) {
    var url = new Url("system", "vw_user_authentications");
    url.addParam("user_agent_id", id);
    url.requestModal(600, 350);
  },

  refreshUALine : function(id) {
    var url = new Url("system", "ajax_refresh_user_agent");
    url.addParam("user_agent_id", id);
    url.requestUpdate("user_agent_" + id);
  },

  updateName: function(select, field){
    var form = select.form;
    $V(form[field], $V(select));
    select.selectedIndex = 0;
  }
};
