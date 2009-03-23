<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
              {{if $filter->_date_max != $filter->_date_min}}
              au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
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
        <tr>
          <td>Paiments pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
        {{if $filter->_etat_reglement_patient}}
        <tr>
          <td>
            Paiment patients :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
          </td>
        </tr>
        {{/if}}
        {{if $filter->_etat_reglement_tiers}}
        <tr>
          <td>
            Paiment tiers :
            {{tr}}CConsultation._etat_reglement_tiers.{{$filter->_etat_reglement_tiers}}{{/tr}}
          </td> 
        </tr>
        {{/if}}
      </table>
    </td>
    
    <td class="halfPane">
     
      <table class="tbl">
        <tr>
          <th class="category" colspan="8">Réglement Patients</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
          <th class="category">Impayé</th>
        </tr>
        <tr>
          <th class="category">Nb réglements</th>
          <td>{{$recapReglement.total.nb_reglement_patient}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_patient}}</td>
          <td>{{$recapReglement.CB.nb_reglement_patient}}</td>
          <td>{{$recapReglement.especes.nb_reglement_patient}}</td>
          <td>{{$recapReglement.virement.nb_reglement_patient}}</td>
          <td>{{$recapReglement.autre.nb_reglement_patient}}</td>
          <td>{{$recapReglement.total.nb_impayes_patient}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement patient</th>
          <td>{{$recapReglement.total.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.cheque.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.CB.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.especes.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.virement.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.autre.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.total.reste_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category" colspan="8">Réglement Tiers</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
          <th class="category">Impayé</th>
        </tr>
        <tr>
          <th class="category">Nb réglements</th>
          <td>{{$recapReglement.total.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.CB.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.especes.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.virement.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.autre.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.total.nb_impayes_tiers}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement Tiers</th>
          <td>{{$recapReglement.total.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.cheque.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.CB.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.especes.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.virement.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.autre.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.total.reste_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category" colspan="8">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="7">{{$recapReglement.total.nb_consultations}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 1</th>
          <td colspan="3">{{$recapReglement.total.secteur1|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <th colspan="4">Total facturé</th>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td colspan="3">{{$recapReglement.total.secteur2|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}
          </td>
        </tr>
        <tr>
          <th class="category">Total réglé patient</th>
          <td colspan="3">{{$recapReglement.total.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <th colspan="4">Total réglé</th>
        </tr>
        <tr>
          <th class="category">Total réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}
          </td>
        </tr>
        <tr>
          <th class="category">Total non réglé patient</th>
          <td colspan="3">{{$recapReglement.total.reste_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <th colspan="4">Total non réglé</th>
        </tr>
        <tr>
          <th class="category">Total non réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.reste_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $filter->_type_affichage}}
  {{foreach from=$listPlages item=curr_plage}}
  <tr>
    <td colspan="2">
      <strong>
        {{$curr_plage.plage->date|date_format:$dPconfig.longdate}}
        de {{$curr_plage.plage->debut|date_format:$dPconfig.time}} à {{$curr_plage.plage->fin|date_format:$dPconfig.time}}
        - Dr {{$curr_plage.plage->_ref_chir->_view}}
      </strong>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Code</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
          <th>Total<br />facturé</th>
          <th>Réglement<br />patient</th>
          <th>Réglement<br />tiers</th>
        </tr>
        {{foreach from=$curr_plage.consultations item=curr_consultation}}
        <tr>
          <td class="text"><a name="consult-{{$curr_consultation->_id}}">Dr {{$curr_consultation->_ref_chir->_view}}</a></td>
          <td class="text">{{$curr_consultation->_ref_patient->_view}}</td>
          <td class="text">{{$curr_consultation->tarif}}</td>
          <td>{{$curr_consultation->secteur1}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$curr_consultation->secteur2}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$curr_consultation->secteur1+$curr_consultation->secteur2}} {{$dPconfig.currency_symbol}}</td>
          <td>
            {{foreach from=$curr_consultation->_ref_reglements_patient item=curr_reglement}}
              <form name="reglement-del-{{$curr_reglement->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              <input type="hidden" name="_dialog" value="print_rapport" />
              <input type="hidden" name="_href" value="consult-{{$curr_consultation->_id}}" />
              {{mb_field object=$curr_reglement field="reglement_id" hidden=1}}
              <button class="remove notext" type="submit">-</button>
              {{$curr_reglement->montant}} {{$dPconfig.currency_symbol}} - {{$curr_reglement->mode}} - {{$curr_reglement->date|date_format:"%d/%m/%Y"}}
              </form>
              <br />
            {{/foreach}}
            {{if $curr_consultation->_du_patient_restant > 0}}
            <form name="reglement-add-patient-{{$curr_consultation->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_reglement_aed" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="_href" value="consult-{{$curr_consultation->_id}}" />
            <input type="hidden" name="date" value="now" />
            <input type="hidden" name="emetteur" value="patient" />
            {{mb_field object=$curr_consultation field="consultation_id" hidden=1 prop=""}}
            <button class="add notext" type="submit">+</button>
            {{mb_field object=$curr_consultation->_new_patient_reglement field="montant"}}
            {{mb_field object=$curr_consultation->_new_patient_reglement field="mode"}}
            <br />
            <select name="banque_id">
              <option value="">&mdash;{{tr}}CReglement-banque_id{{/tr}}&mdash;</option> 
               {{foreach from=$banques item=banque}}
                 <option value="{{$banque->_id}}">{{$banque->_view}}</option>
               {{/foreach}}
            </select>
            </form>
            {{/if}}
          </td>
          <td>
            {{foreach from=$curr_consultation->_ref_reglements_tiers item=curr_reglement}}
              <form name="reglement-del-{{$curr_reglement->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="dosql" value="do_reglement_aed" />
              <input type="hidden" name="_dialog" value="print_rapport" />
              <input type="hidden" name="_href" value="consult-{{$curr_consultation->_id}}" />
              {{mb_field object=$curr_reglement field="reglement_id" hidden=1}}
              <button class="remove notext" type="submit">-</button>
              {{$curr_reglement->montant}} {{$dPconfig.currency_symbol}} - {{$curr_reglement->mode}} - {{$curr_reglement->date|date_format:"%d/%m/%Y"}}
              </form>
              <br />
            {{/foreach}}
            {{if $curr_consultation->_du_tiers_restant > 0}}
            <form name="reglement-add-tiers-{{$curr_consultation->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_reglement_aed" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="_href" value="consult-{{$curr_consultation->_id}}" />
            <input type="hidden" name="date" value="now" />
            <input type="hidden" name="emetteur" value="tiers" />
            {{mb_field object=$curr_consultation field="consultation_id" hidden=1 prop=""}}
            <button class="add notext" type="submit">+</button>
            {{mb_field object=$curr_consultation->_new_tiers_reglement field="montant"}}
            {{mb_field object=$curr_consultation->_new_tiers_reglement field="mode"}}
            <br />
            <select name="banque_id">
              <option value="">&mdash;{{tr}}CReglement-banque_id{{/tr}}&mdash;</option> 
               {{foreach from=$banques item=banque}}
                 <option value="{{$banque->_id}}">{{$banque->_view}}</option>
               {{/foreach}}
            </select>
            </form>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="2" />
          <th>Total</th>
          <td><strong>{{$curr_plage.total.secteur1}} {{$dPconfig.currency_symbol}}</strong></td>
          <td><strong>{{$curr_plage.total.secteur2}} {{$dPconfig.currency_symbol}}</strong></td>
          <td><strong>{{$curr_plage.total.total}} {{$dPconfig.currency_symbol}}</strong></td>
          <td><strong>{{$curr_plage.total.patient}} {{$dPconfig.currency_symbol}}</strong></td>
          <td><strong>{{$curr_plage.total.tiers}} {{$dPconfig.currency_symbol}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      