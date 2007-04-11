<script type="text/javascript">

var Catalogue = {
  select: function(id) {
    var url = new Url;
    url.setModuleTab("{{$m}}", "{{$tab}}");
    url.addParam("catalogue_labo_id", id);
    url.redirect();
  }
};

function pageMain() {
  PairEffect.initGroup('tree-content');
}
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
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id=0">
        Ajouter un nouveau catalogue
      </a>
      <form name="editCatalogue" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_catalogue_aed" />
      <input type="hidden" name="catalogue_labo_id" value="{{$catalogue->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $catalogue->_id}}
          <th class="title modify" colspan="2">Modification du catalogue {{$catalogue->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un catalogue</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$catalogue field="pere_id"}}</th>
          <td>
            <select name="pere_id">
              <option value="">&mdash; Catalogue racine</option>
              {{assign var="pere_id" value=$catalogue->pere_id}}
              {{foreach from=$listCatalogues item="_catalogue"}}
              {{include file="options_catalogues.tpl"}}
              {{/foreach}}
            </select>
          </td>
        </tr>
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
      
      <!-- Liste des exmanens du catalogue sélectionné -->
      {{if $catalogue->_id}}
      <table class="tbl">
        <tr>
          <th colspan="6" class="title">Liste des examens</th>
        </tr>
        <tr>
          <th>Identifiant</th>
          <th>Libelle</th>
          <th>Type</th>
          <th>Unité</th>
          <th>Min</th>
          <th>Max</th>
        </tr>
        {{foreach from=$catalogue->_ref_examens_labo item="curr_examen"}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->identifiant}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{tr}}{{$curr_examen->libelle}}{{/tr}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->type}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->unite}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->min}} {{$curr_examen->unite}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->max}} {{$curr_examen->unite}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      {{/if}}
      {{/if}}
    </td>
  </tr>
</table>