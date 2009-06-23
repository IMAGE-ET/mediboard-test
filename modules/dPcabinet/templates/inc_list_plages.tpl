    <table class="tbl">
      <tr>
        <th>Date</th>
        <th>Praticien</th>
        <th>Libelle</th>
        <th>Etat</th>
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
          {{$_plage->_affected}} / {{$_plage->_total|string_format:"%.0f"}}
        </td>
      </tr>
      {{/foreach}}
    </table>