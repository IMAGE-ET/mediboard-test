<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th>Libelle</th>
          <th>Examens</th>
        </tr>
        {{foreach from=$listPacks item="curr_pack"}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;pack_examens_labo_id={{$curr_pack->_id}}">
              {{$curr_pack->libelle}}
            </a>
          </td>
          <td>{{$curr_pack->_ref_examens_labo|@count}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;pack_examens_labo_id=0">
        Ajouter un nouveau pack
      </a>
      <form name="editPack" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_pack_aed" />
      <input type="hidden" name="pack_examens_labo_id" value="{{$pack->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $pack->_id}}
          <th class="title modify" colspan="2">Modification du pack {{$pack->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un pack</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$pack field="libelle"}}</th>
          <td>{{mb_field object=$pack field="libelle"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $pack->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le pack',objName:'{{$pack->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
      {{if $pack->_id}}
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
        {{foreach from=$pack->_ref_examens_labo item="curr_examen"}}
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