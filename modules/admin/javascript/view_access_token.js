ViewAccessToken = {
  list: function() {
    var url = new Url("admin", "ajax_list_tokens");
    url.requestUpdate("token-list");
  },
  edit: function(token_id) {
    var url = new Url("admin", "ajax_edit_token");
    url.addParam("token_id", token_id);
    url.requestUpdate("token-edit");
  },
  refreshAll: function(token_id) {
    ViewAccessToken.edit(token_id);
    ViewAccessToken.list();
  }
};
