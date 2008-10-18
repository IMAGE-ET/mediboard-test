<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Retours Noemie du {{$_date_min|date_format:"%d/%m/%Y"}}
              {{if $_date_max != $_date_min}}
              au {{$_date_max|date_format:"%d/%m/%Y"}}
              {{/if}}
            </a>
          </th>
        </tr>
        {{if $chirSel->user_id}}
        <tr>
          <th>Dr {{$chirSel->_view}}</th>
        </tr>
        {{else}}
        {{foreach from=$listPrat item=curr_prat}}
        <tr>
          <th>Dr {{$curr_prat->_view}}</th>
        </tr>
        {{/foreach}}
        {{/if}}
      </table>
    </td>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">Réglements concernés</th>
        </tr>
        <tr>
          <th class="category">Nombre</th>
          <td>{{$total.nb}} consultation(s)</td>
        </tr>
        <tr>
          <th class="category">Valeur</th>
          <td>{{$total.value|string_format:"%.2f"}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Tout valider</th>
          <td>
            <form name="reglement-add-tiers-{{$curr_consult->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_multi_noemie_aed" />
              <input type="hidden" name="_dialog" value="print_noemie" />
              <input type="hidden" name="_href" value="consult-{{$curr_consult->_id}}" />
              <input type="hidden" name="date" value="now" />
              <input type="hidden" name="emetteur" value="tiers" />
              <input type="hidden" name="mode" value="virement" />
              <button class="add notext" type="submit">+</button>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Date</th>
    <th>Du tiers total</th>
    <th>Restant du</th>
    <th>Valider</th>
  </tr>
  {{foreach from=$listConsults item=curr_consult}}
  <tr>
    <td>{{$curr_consult->_ref_patient->_view}}</td>
    <td>{{$curr_consult->_ref_chir->_view}}</td>
    <td>{{$curr_consult->_date|date_format:"%d/%m/%Y"}}</td>
    <td>{{$curr_consult->du_tiers}}</td>
    <td>{{$curr_consult->_du_tiers_restant}}</td>
    <td>
      <form name="reglement-add-tiers-{{$curr_consult->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_reglement_aed" />
        <input type="hidden" name="_dialog" value="print_noemie" />
        <input type="hidden" name="_href" value="consult-{{$curr_consult->_id}}" />
        <input type="hidden" name="date" value="now" />
        <input type="hidden" name="emetteur" value="tiers" />
        {{mb_field object=$curr_consult field="consultation_id" hidden=1 prop=""}}
        <button class="add notext" type="submit">+</button>
        {{mb_field object=$curr_consult->_new_tiers_reglement field="montant"}}
        {{mb_field object=$curr_consult->_new_tiers_reglement field="mode"}}
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>