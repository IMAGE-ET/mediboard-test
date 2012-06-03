<script type="text/javascript">
  editPrestation = function(prestation_id, object_class) {
    var url = new Url('dPhospi', 'ajax_edit_prestation');
    url.addParam("prestation_id", prestation_id);
    url.addParam("object_class" , object_class);
    url.requestUpdate('edit_prestation');
  }
  
  refreshList = function(prestation_guid) {
    var url = new Url('dPhospi', 'ajax_list_prestations');
    url.addParam('prestation_guid', prestation_guid);
    url.requestUpdate('list_prestations');
  }
  
  afterEditPrestation = function(id, obj) {
    editPrestation(id, obj._class);
    refreshList(obj._guid)
  }
  
  editItem = function(item_id, object_class, object_id, rank) {
    var url = new Url("dPhospi", "ajax_edit_item_prestation");
    url.addParam("item_id", item_id);
    if (!Object.isUndefined(object_class) && !Object.isUndefined(object_id)) {
      url.addParam("object_class", object_class);
      url.addParam("object_id", object_id);
    }
    
    if (!Object.isUndefined(rank)) {
      url.addParam("rank", rank);
    }
    
    url.requestUpdate("edit_item");
  }
  
  refreshItems = function(object_class, object_id, item_id) {
    var url = new Url("dPhospi", "ajax_list_items_prestation");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("item_id", item_id);
    url.requestUpdate("list_items");
  }
  
  afterEditItem = function(id, obj) {
    editItem(id);
    refreshItems(obj.object_class, obj.object_id, id);
  }
  
  updateSelected = function(guid, classname) {
    removeSelected(classname);
    var tr = $(classname+"_" +guid);
    tr.addClassName("selected");
  }
  
  removeSelected = function(classname) {
    var tr = $$("tr."+classname+".selected")[0];
    if (tr) {
      tr.removeClassName("selected");
    }
  }
  
  reorderItem = function(item_id_move, direction) {
    var url = new Url("dPhospi", "ajax_reorder_item");
    url.addParam("item_id_move", item_id_move);
    url.addParam("direction", direction);
    url.requestUpdate("list_items");
  }
  
  Main.add(function() {
    editPrestation('{{$prestation_id}}', '{{$object_class}}');
    refreshList('{{$object_class}}-{{$prestation_id}}');
  });
</script>

{{mb_include template=inc_warning_config_prestations wanted=expert}}

<!-- Formulaire fictif pour récupérer le type de prestation -->
<form name="new_prestation" method="get">
  <button type="button" class="new" onclick="removeSelected('prestation'); editPrestation(0, $V(this.form.type_prestation))">
    Création de prestation
  </button>
  <label>
    <input type="radio" name="type_prestation" id="type_prestation" value="CPrestationPonctuelle" checked="checked" /> Ponctuelle
  </label>
  <label>
    <input type="radio" name="type_prestation" id="type_prestation" value="CPrestationJournaliere" /> Journalière
  </label>
</form>

<table class="main">
  <tr>
    <td id="list_prestations" style="width: 50%;"></td>
    <td id="edit_prestation"></td>
  </tr>
</table>
