{{mb_script module="system" script="object_selector"}}

<script type="text/javascript">
  editId400 = function(idSante400_id) {
    var url = new Url('sante400', 'ajax_edit_identifiant');
    url.addParam('idSante400_id'  , idSante400_id);
    url.addParam('object_class'   , '{{$filter->object_class}}');
    url.addParam('object_id'      , '{{$filter->object_id}}');
    url.addParam('tag'            , '{{$filter->tag}}');
    url.addParam('id400'          , '{{$filter->id400}}');
    url.addParam('dialog'         , 1);
    url.requestModal();
  }

  refreshListId400 = function(idSante400_id) {
    {{if !$dialog}}
      refreshListFilter();
    {{else}}
      var url = new Url('sante400', "ajax_list_identifiants");
    
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
    var form = getForm('filterFrm');
    var url = new Url('sante400', 'ajax_list_identifiants');
    url.addElement(form.object_class);
    url.addElement(form.object_id);
    url.addElement(form.tag);
    url.addElement(form.id400);
    url.addElement('dialog', '{{$dialog}}');
    url.requestUpdate("list_identifiants");
    return false;
  }
  
  reloadId400 = function(idSante400_id) {
    refreshListId400(idSante400_id);
  }
  
  Main.add(refreshListId400);
</script>

{{if $canSante400->edit}}
<a class="button new" onclick="editId400(0);">
  Création d'un identifiant
</a>
{{/if}}

{{if !$dialog}}
  {{mb_include template=inc_filter_identifiants}}
{{/if}}

<div id="list_identifiants">
</div>
