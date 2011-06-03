{{mb_script module="system" script="object_selector"}}

<script type="text/javascript">
  editId400 = function(idSante400_id) {
    var url = new Url('dPsante400', 'ajax_edit_identifiant');
    url.addParam('idSante400_id'  , idSante400_id);
    url.addParam('object_class'   , '{{$filter->object_class}}');
    url.addParam('object_id'      , '{{$filter->object_id}}');
    url.addParam('tag'            , '{{$filter->tag}}');
    url.addParam('id400'          , '{{$filter->id400}}');
    url.addParam('dialog'         , 1);
    url.requestUpdate('edit_id400');
  }

  refreshListId400 = function(idSante400_id) {
    {{if !$dialog}}
      refreshListFilter();
    {{else}}
      var url = new Url('dPsante400', "ajax_list_identifiants");
    
      url.addParam('object_class' , '{{$filter->object_class}}');
      url.addParam('object_id'    , '{{$filter->object_id}}');
      url.addParam('tag'          , '{{$filter->tag}}');
      url.addParam('id400'        , '{{$filter->id400}}');
      url.addParam('idSante400_id', idSante400_id);
      url.addParam("dialog"       , '{{$dialog}}');
      url.requestUpdate("list_identifiants");  
    {{/if}}
    
  }

  refreshListFilter = function() {
    var oForm = getForm('filterFrm');
    var url = new Url('dPsante400', 'ajax_list_identifiants');
    url.addParam('object_class', $V(oForm.object_class));
    url.addParam('object_id'   , $V(oForm.object_id));
    url.addParam('tag'         , $V(oForm.tag));
    url.addParam('id400'       , $V(oForm.id400));
    url.addParam("dialog"      , '{{$dialog}}');
    url.requestUpdate("list_identifiants");
  }
  
  reloadId400 = function(idSante400_id) {
    refreshListId400(idSante400_id);
    editId400(idSante400_id);
  }

  toggleSelected = function(tr) {
    var elts = $('list_identifiants').select('tr');

    elts.each(function(tr) {
      tr.removeClassName('selected');
    });

    tr.addClassName('selected');
  }
  
  Main.add(function() {
    reloadId400('{{$idSante400_id}}');
  });
</script>

<table class="main">
  <tr>
    <td style="width: 50%">
      {{if $canSante400->edit}}
      <a class="button new" onclick="editId400(0);">
        Création d'un identifiant
      </a>
      {{/if}}
      {{if !$dialog}}
        {{include file="inc_filter_identifiants.tpl"}}
      {{/if}}
      <span id="list_identifiants">
      </span>
    </td>
    {{if $canSante400->edit}}
      <td id="edit_id400" style="width: 50%">
      </td>
    {{/if}}
  </tr>
</table>