<script type="text/javascript">
Main.add(function () {
  // Initialisation des onglets du menu
  new Control.Tabs('tabs-modeles');
});

function setClose(modele_id, object_id) {
  if (window.opener) {
    var oSelector = window.opener.modeleSelector[{{$target_id}}];
    oSelector.set(modele_id, object_id);
  }
  window.close();
}
</script>

<h1>Modèles pour </h1>

<ul id="tabs-modeles" class="control_tabs">
{{foreach from=$modelesCompat key=class item=modeles}}
  <li><a href="#{{$class}}">{{tr}}{{$class}}{{/tr}}</a></li>
{{/foreach}}
  <li class="separator"></li>
{{foreach from=$modelesNonCompat key=class item=modeles}}
  <li><a href="#{{$class}}" class="minor">{{tr}}{{$class}}{{/tr}}</a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$modelesCompat key=class item=modeles}}
  {{include file="inc_vw_list_models.tpl"}}
{{/foreach}}

{{foreach from=$modelesNonCompat key=class item=modeles}}
  {{include file="inc_vw_list_models.tpl"}}
{{/foreach}}


<div class="little-info">
  Cliquez sur un modèle pour l'utiliser !
  <br />
  Les section grisées contiennent des champs potentiellement incompatibles avec le contexte courant
</div>