<div id="disabledEtape3" class="chargementMask" style="position:absolute;display:none;"></div>
<table class="main">
  <tr>
    <th>
      <label for="_nb_pat" title="Nombre de patients à créer">Nombre de patients</label>
    </th>
    <td>
      {{html_options name="_nb_pat" options=$list_50}}
    </td>
    {{if $creation_prat}}
    <th>
      <label for="_nb_prat" title="Nombre de praticiens à créer par cabinet">Nombre de praticiens</label>
    </th>
    <td class="halfPane">
      {{html_options name="_nb_prat" options=$list_10}}
    </td>
    {{else}}
    <input type="hidden" name="_nb_prat" value="0" />
    {{/if}}
  </tr>
  
  {{if $listPrat}}
  <tr>
    {{if $creation_prat}}
    <td colspan="2"></td>
    {{/if}}
    <th>
      <label for="prat_selected[]" title="Praticiens disponibles">Praticiens disponible</label>
    </th>
    <td>
      <select name="prat_selected[]" size="15" multiple>
      {{foreach from=$listPrat item=curr_prat}}
      <option value="{{$curr_prat->_id}}">
        {{$curr_prat->_view}}
      </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}

  <tr>
    <th colspan="4" id="vwButtonEtap4">
      <a class="button" href="#" onclick="goto_etape4()">
        Etape Suivante <img align="top" src="images/icons/next.png" alt="Etape Suivante" />
      </a>
    </th>
  </tr>
</table>