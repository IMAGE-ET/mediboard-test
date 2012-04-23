<script type="text/javascript">
  Ressource = {
    editRessource: function(ressource_id, type_ressource_id) {
      var url = new Url("dPbloc", "ajax_edit_ressource");
      url.addParam("ressource_id", ressource_id);
      url.addParam("type_ressource_id", type_ressource_id);
      url.requestModal();
    },
    
    afterEditRessource: function(ressource_id) {
      Control.Modal.close();
      TypeRessource.refreshListTypeRessources();
    }
  };
  
  TypeRessource = {
    editTypeRessource: function(type_ressource_id) {
      var url = new Url("dPbloc", "ajax_edit_type_ressource");
      url.addParam("type_ressource_id", type_ressource_id);
      url.requestModal(400);
    },
    
    refreshListTypeRessources: function() {
      var url = new Url("dPbloc", "ajax_list_type_ressources");
      url.requestUpdate("list_type_ressources");
    },
    
    afterEditTypeRessource: function(type_ressource_id) {
      Control.Modal.close();
      this.refreshListTypeRessources();
    },
  };
  
  updateSelected = function(table_name, tr) {
    $(table_name).select('tr').each(function(elt) {
      elt.removeClassName('selected');
    });
    if (tr) {
      tr.addClassName('selected');
    }
  }
  
  Main.add(function() {
    TypeRessource.refreshListTypeRessources();
    
    new Control.Tabs.create("edit_ressource_type", true);
  })
</script>

<table class="main">
  <tr>
    <td id="list_type_ressources" style="width: 50%"></td>
  </tr>
</table>
