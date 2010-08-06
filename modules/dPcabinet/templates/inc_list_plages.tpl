    <table class="tbl">
      <tr>
        <th style="width: 7em;">Date</th>
        <th>Praticien</th>
        <th>Libelle</th>
        <th colspan="2">Etat</th>
      </tr>
      {{foreach from=$listPlage item=_plage}}
      <tr {{if $_plage->_id == $plageconsult_id}}class="selected"{{/if}} id="plage-{{$_plage->_id}}">
        <td>
          {{include file=inc_plage_etat.tpl}}
        </td>
        <td class="text">
          <div class="mediuser" style="border-color: #{{$_plage->_ref_chir->_ref_function->color}};">
            {{$_plage->_ref_chir->_view}}
          </div>
        </td>
        <td class="text">
          {{$_plage->libelle}}
        </td>
        <td>
          {{$_plage->_affected}}/{{$_plage->_total|string_format:"%.0f"}}
        </td>
        <td>
          {{if $_plage->_consult_by_categorie|@count}}
            {{foreach from=$_plage->_consult_by_categorie item=curr_categorie}}
              {{$curr_categorie.nb}}
              <img alt="{{$curr_categorie.nom_categorie}}" title="{{$curr_categorie.nom_categorie}}" src="modules/dPcabinet/images/categories/{{$curr_categorie.nom_icone}}"  style="vertical-align: middle;" />
            {{/foreach}}
          {{/if}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="5">{{tr}}CPlageconsult.none{{/tr}}</td>
      </tr>
      {{/foreach}}
    </table>