<script>
  refreshFraisDivers = function(){
    var url = new Url('dPccam', 'ajax_refresh_add_frais_divers');
    url.addParam('object_guid', '{{$object->_guid}}');
    url.requestUpdate('editFraisDivers-{{$object->_guid}}');
  };
  Main.add(refreshFraisDivers);
</script>

<div id="editFraisDivers-{{$object->_guid}}"></div>