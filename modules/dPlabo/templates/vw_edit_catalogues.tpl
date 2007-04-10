<script type="text/javascript">

function pageMain() {
  PairEffect.initGroup('content');
}
</script>
  

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th colspan="2">Identifiant</th>
          <th>Libellé</th>
          <th>Examens</th>
        </tr>
        {{foreach from=$listCatalogues item="curr_catalogue"}}
        <tr {{if $curr_catalogue->_id == $catalogue->_id }} class="selected" {{/if}}>
          <td>+</td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id={{$curr_catalogue->_id}}">
              {{$curr_catalogue->identifiant}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id={{$curr_catalogue->_id}}">
              {{$curr_catalogue->libelle}}
            </a>
          </td>
          <td>
            {{$curr_catalogue->_ref_examens_labo|@count}}
            <a class="buttonedit action" href="index.php?m={{$m}}&amp;tab=vw_edit_examens&amp;catalogue_labo_id={{$curr_catalogue->_id}}">
              Editer
            </a>
          </td>
        </tr>
        {{foreach from=$curr_catalogue->_ref_catalogues_labo item="curr_sub_catalogue"}}
        <tr>
          <td></td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id={{$curr_sub_catalogue->_id}}">
              {{$curr_sub_catalogue->identifiant}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id={{$curr_sub_catalogue->_id}}">
              {{$curr_sub_catalogue->libelle}}
            </a>
          </td>
          <td>
            {{$curr_sub_catalogue->_ref_examens_labo|@count}}
            <a class="buttonedit action" href="?m={{$m}}&amp;tab=vw_edit_examens&amp;catalogue_labo_id={{$curr_sub_catalogue->_id}}">
              Editer
            </a>
          </td>
        </tr>
        {{/foreach}}
        {{/foreach}}
      </table>*
      
      {{foreach from=$listCatalogues item="_catalogue"}}
      {{include file="tree_catalogues.tpl"}}
      {{/foreach}}
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;catalogue_labo_id=0">
        Ajouter un nouveau catalogue
      </a>
      <form name="editCatalogue" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
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
              <option value="">&mdash; Aucun</option>
              {{foreach from=$listCatalogues item="curr_catalogue"}}
              {{if $catalogue->_id != $curr_catalogue->_id}}
              <option value="{{$curr_catalogue->_id}}" {{if $catalogue->pere_id == $curr_catalogue->_id}}selected="selected"{{/if}}>
                {{$curr_catalogue->_view}}
              </option>
              {{/if}}
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
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le catalogue',objName:'{{$catalogue->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
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