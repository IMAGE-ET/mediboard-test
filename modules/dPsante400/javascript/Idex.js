// $Id: $

Idex = {
  edit: function(object_guid, tag) {
    var parts = object_guid.split("-");

    new Url('sante400', 'ajax_edit_identifiant')
      .addParam("object_class", parts[0])
      .addParam("object_id"   , parts[1])
      .addParam('tag'         , tag)
      .addParam('load_unique' , 1)
      .addParam('dialog'      , 1)
      .requestModal(400);
  }
}
