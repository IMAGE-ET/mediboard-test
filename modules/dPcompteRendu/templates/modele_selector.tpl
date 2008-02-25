<script type="text/javascript">
function pageMain(){
  // Initialisation des onglets du menu
  new Control.Tabs('main_tab_group');
}

function setClose(modele_id, object_id) {
  var oSelector = window.opener.ModeleSelector;
  oSelector.set(modele_id, object_id);
    window.close();
  
}
</script>

<ul id="main_tab_group" class="control_tabs">
{{foreach from=$templateClasses key=class item=id}}
  {{if $modeles.$class.prat|@count || $modeles.$class.func|@count}}<li><a href="#{{$class}}">{{tr}}{{$class}}{{/tr}}</a></li>{{/if}}
{{/foreach}}
</ul>
<hr class="control_tabs" />    

{{foreach from=$templateClasses key=class item=id}}
  {{include file="inc_vw_list_models.tpl" 
	  object_id=$id
	  object_class=$class
	  list_modeles=$modeles.$class}}
{{/foreach}}