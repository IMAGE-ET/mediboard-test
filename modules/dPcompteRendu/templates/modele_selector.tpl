<script type="text/javascript">
function pageMain(){
  // Initialisation des onglets du menu
  new Control.Tabs('tabs-modeles');
}

function setClose(modele_id, object_id) {
  if (window.opener) {
    var oSelector = window.opener.modeleSelector[{{$target_id}}];
    oSelector.set(modele_id, object_id);
  }
  window.close();
}
</script>

<ul id="tabs-modeles" class="control_tabs">
{{foreach from=$modelesCompat key=class item=modeles}}
  {{if $modeles.prat|@count || $modeles.func|@count}}
    <li><a href="#{{$class}}">{{tr}}{{$class}}{{/tr}}</a></li>
  {{/if}}
{{/foreach}}
  <li class="separator"></li>
{{foreach from=$modelesNonCompat key=class item=modeles}}
  {{if $modeles.prat|@count || $modeles.func|@count}}
    <li><a href="#{{$class}}" class="minor">{{tr}}{{$class}}{{/tr}}</a></li>
  {{/if}}
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$modelesCompat key=class item=modeles}}
  {{if $modeles.prat|@count || $modeles.func|@count}}
    {{include file="inc_vw_list_models.tpl"}}
  {{/if}}
{{/foreach}}

{{foreach from=$modelesNonCompat key=class item=modeles}}
  {{if $modeles.prat|@count || $modeles.func|@count}}
    {{include file="inc_vw_list_models.tpl"}}
  {{/if}}
{{/foreach}}
