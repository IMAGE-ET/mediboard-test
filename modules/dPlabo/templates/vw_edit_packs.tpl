{{* $Id$*}}

<table class="main">
  <tr>
    <td class="halfPane">

      <!-- Liste des packs disponibles -->
      <table class="tbl">
        <tr>
          <th>Libelle</th>
          <th>Analyses</th>
        </tr>
        {{foreach from=$listPacks item="curr_pack"}}
        <tr {{if $curr_pack->_id == $pack->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;pack_examens_labo_id={{$curr_pack->_id}}">
              {{$curr_pack->libelle}}
            </a>
          </td>
          <td>{{$curr_pack->_ref_items_examen_labo|@count}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>

    <td class="halfPane">

      <!-- Edition du pack sélectionné -->
      {{if $can->edit}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;pack_examens_labo_id=0">
        Ajouter un nouveau pack
      </a>
      
      <form name="editPack" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_pack_aed" />
      <input type="hidden" name="pack_examens_labo_id" value="{{$pack->_id}}" />
      <input type="hidden" name="_locked" value="{{$pack->_locked}}" />
      <input type="hidden" name="del" value="0" />

      <table class="form">
        <tr>
          {{if $pack->_id}}
          <th class="title modify" colspan="2">
          <div class="idsante400" id="{{$pack->_class_name}}-{{$pack->_id}}" ></div>
            <a style="float:right;" href="#nothing" onclick="view_log('{{$pack->_class_name}}', {{$pack->_id}})">
              <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
            </a>
            Modification du pack {{$pack->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">Création d'un pack</th>
          {{/if}}
        </tr>

        <tr>
          <th>{{mb_label object=$pack field="function_id"}}</th>
          <td>
            <select name="function_id">
              <option value="">&mdash; Aucune</option>
              {{foreach from=$listFunctions item="curr_function"}}
              <option value="{{$curr_function->_id}}" {{if $pack->function_id == $curr_function->_id}}selected="selected"{{/if}}>
                {{$curr_function->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
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
      {{/if}}

      {{if $pack->_id}}
      <!-- Liste des exmanens du packsélectionné -->
      {{assign var="examens" value=$pack->_ref_examens_labo}}
      {{assign var="examen_id" value=""}}
      {{include file="list_examens.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>