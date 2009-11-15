<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport
              {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>

        <!-- Praticiens concernés -->
        {{if $chirSel->_id}}
        {{assign var=prat_id value=$chirSel->_id}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$listPrat.$prat_id}}</td>
        </tr>
        {{else}}
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}
        {{/if}}

        <tr>
          <td>Règlements pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
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
      	{{foreach from=$reglement->_specs.emetteur->_list item=emetteur}}
        <tr>
          <th class="category" colspan="8">Réglement {{tr}}CReglement.emetteur.{{$emetteur}}{{/tr}}</th>
        </tr>

        <tr>
          <th class="category">{{mb_label object=$reglement field=mode}}</th>
          <th class="category">{{tr}}Total{{/tr}}</th>
					{{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <th class="category">{{tr}}CReglement.mode.{{$_mode}}{{/tr}}</th>
					{{/foreach}}
          <th class="category">Impayé</th>
        </tr>

        <tr>
          <th class="category">Nb réglements</th>
					{{assign var=nb_reglement_name value="nb_reglement_$emetteur"}}
          <td>{{$recapReglement.total.$nb_reglement_name}}</td>
          {{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <td>{{$recapReglement.$_mode.$nb_reglement_name}}</td>
          {{/foreach}}
          <td>{{$recapReglement.total.$nb_reglement_name}}</td>
        </tr>

        <tr>
          <th class="category">Total réglements</th>
          {{assign var=du_name value="du_$emetteur"}}
          <td>{{$recapReglement.total.$du_name|currency}}</td>
          {{foreach from=$reglement->_specs.mode->_list item=_mode}}
          <td>{{$recapReglement.$_mode.$du_name|currency}}</td>
          {{/foreach}}
          {{assign var=reste_name value="reste_$emetteur"}}
          <td>{{$recapReglement.total.$reste_name|currency}}</td>
        </tr>
				{{/foreach}}
				 
        <tr>
          <th class="category" colspan="8">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="7">{{$recapReglement.total.nb_consultations}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 1</th>
          <td colspan="3">{{$recapReglement.total.secteur1|currency}}</td>
          <th colspan="4">Total facturé</th>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td colspan="3">{{$recapReglement.total.secteur2|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}
          </td>
        </tr>
        <tr>
          <th class="category">Total réglé patient</th>
          <td colspan="3">{{$recapReglement.total.du_patient|currency}}</td>
          <th colspan="4">Total réglé</th>
        </tr>
        <tr>
          <th class="category">Total réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.du_tiers|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}
          </td>
        </tr>
        <tr>
          <th class="category">Total non réglé patient</th>
          <td colspan="3">{{$recapReglement.total.reste_patient|currency}}</td>
          <th colspan="4">Total non réglé</th>
        </tr>
        <tr>
          <th class="category">Total non réglé tiers</th>
          <td colspan="3">{{$recapReglement.total.reste_tiers|currency}}</td>
          <td colspan="4" class="button">
            {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|currency}}
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
          <td>{{mb_value object=$curr_consultation field=secteur1}}</td>
          <td>{{mb_value object=$curr_consultation field=secteur2}}</td>
          <td>{{mb_value object=$curr_consultation field=_somme}}</td>
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
              {{mb_value object=$curr_reglement field=montant}} - {{$curr_reglement->mode}} - {{$curr_reglement->date|date_format:"%d/%m/%Y"}}
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
              {{mb_value object=$curr_reglement field=montant}} - {{$curr_reglement->mode}} - {{$curr_reglement->date|date_format:"%d/%m/%Y"}}
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
          <td><strong>{{$curr_plage.total.secteur1|currency}}</strong></td>
          <td><strong>{{$curr_plage.total.secteur2|currency}}</strong></td>
          <td><strong>{{$curr_plage.total.total|currency}}</strong></td>
          <td><strong>{{$curr_plage.total.patient|currency}}</strong></td>
          <td><strong>{{$curr_plage.total.tiers|currency}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      