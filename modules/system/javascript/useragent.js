UserAgent = window.UserAgent = {
  edit: function(id) {
    var url = new Url("system", "ajax_edit_user_agent");
    url.addParam("user_agent_id", id);
    url.requestModal(600, 350, {
      onClose: function(){
        document.location.reload();
      }
    });
  },

  updateName: function(select, field){
    var form = select.form;
    $V(form[field], $V(select));
    select.selectedIndex = 0;
  }
};
