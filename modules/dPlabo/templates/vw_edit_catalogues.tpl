<script type="text/javascript">
var Catalogue = {
  select: function(id) {
    var url = new Url;
    url.setModuleTab("{{$m}}", "{{$tab}}");
    url.addParam("catalogue_labo_id", id);
    url.redirect();
  }
};

function checkRefFunction(pere) {
  oForm = document.editCatalogue;
  if(pere) {
    oForm.function_id.value = "";
    oForm.function_id.disabled = "disabled";
    oForm.function_id.hide();
  } else {
    oForm.function_id.disabled = "";
    oForm.function_id.show();
  }
}

Main.add(function () {
  PairEffect.initGroup('tree-content');
});
</script>

<table class="main">
  <tr>
    <!-- Affichage des catalogues -->
    <td class="halfPane">      
      {{assign var="catalogue_id" value=$catalogue->_id}}
      {{foreach from=$listCatalogues item="_catalogue"}}
      {{include file="tree_catalogues.tpl"}}
      {{/foreach}}
    </td>
    
    <!-- Edition des catalogues --> 
    <td class="halfPane">
      {{if $can->edit}}
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id=0">
        Ajouter un nouveau catalogue
      </a>
      <form name="editCatalogue" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_catalogue_aed" />
      <input type="hidden" name="catalogue_labo_id" value="{{$catalogue->_id}}" />
      <input type="hidden" name="_locked" value="{{$catalogue->_locked}}" />
      <input type="hidden" name="del" value="0" />

      <table class="form">
        <tr>
          {{if $catalogue->_id}}
          <th class="title modify" colspan="10">
            <div class="idsante400" id="{{$catalogue->_class_name}}-{{$catalogue->_id}}" ></div>
            <a style="float:right;" href="#nothing" onclick="view_log('{{$catalogue->_class_name}}', {{$catalogue->_id}})">
              <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
            </a>
            Modification du catalogue {{$catalogue->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">Création d'un catalogue</th>
          {{/if}}
        </tr>

        {{if !$catalogue->_locked}}
        <tr>
          <th>{{mb_label object=$catalogue field="function_id"}}</th>
          <td>
            <select name="function_id" {{if $catalogue->pere_id}}disabled="disabled" style="display: none;"{{/if}}>
              <option value="">&mdash; Toutes</option>
              {{foreach from=$functions item="_function"}}
              <option value="{{$_function->_id}}" {{if $function_id == $_function->_id}}selected="selected"{{/if}}>
                {{$_function->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$catalogue field="pere_id"}}</th>
          <td>
            <select name="pere_id" onchange="checkRefFunction(this.value);">
              <option value="">&mdash; Catalogue racine</option>
              {{assign var="selected_id" value=$catalogue->pere_id}}
              {{assign var="exclude_id" value=$catalogue->_id}}
              {{foreach from=$listCatalogues item="_catalogue"}}
              {{if !$_catalogue->_locked}}
              {{include file="options_catalogues.tpl"}}
              {{/if}}
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}

        <tr>
          <th>{{mb_label object=$catalogue field="identifiant"}}</th>
          <td>{{mb_field object=$catalogue field="identifiant"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$catalogue field="libelle"}}</th>
          <td>{{mb_field object=$catalogue field="libelle"}}</td>
        </tr>

        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $catalogue->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form, {
                typeName:'le catalogue',
                objName:'{{$catalogue->_view|smarty:nodefaults|JSAttribute}}'
              } )">
                Supprimer
              </button>
            {{/if}}
          </td>
        </tr>
      </table>

      </form>
      
      {{if $catalogue->_id}}
      <!-- Liste des exmanens du catalogue sélectionné -->
      {{assign var="examens" value=$catalogue->_ref_examens_labo}}
      {{assign var="examen_id" value=""}}
      {{include file="list_examens.tpl"}}
      {{/if}}
      
      {{/if}}
    </td>
  </tr>
</table>