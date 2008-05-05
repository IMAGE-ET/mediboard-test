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
          <th>Dr. {{$chirSel->_view}}</th>
        </tr>
        {{else}}
        {{foreach from=$listPrat item=curr_prat}}
        <tr>
          <th>Dr. {{$curr_prat->_view}}</th>
        </tr>
        {{/foreach}}
        {{/if}}
        <tr>
          <td>
            Reglement Patient: {{if $_etat_reglement_patient}}{{tr}}CConsultation._etat_reglement_patient.{{$_etat_reglement_patient}}{{/tr}}{{else}}Tous{{/if}}
          </td>
        </tr>
        <tr>
          <td>
            Réglement Tiers: {{if $_etat_reglement_tiers}}{{tr}}CConsultation._etat_reglement_tiers.{{$_etat_reglement_tiers}}{{/tr}}{{else}}Tous{{/if}}
          </td>  
        </tr>
        <tr><td>Paiments pris en compte : {{if $type}}{{$type}}{{else}}tous{{/if}}</td></tr>
      </table>
    </td>
    
    <td class="halfPane">
     
      <table class="tbl">
        <tr>
          <th class="category" {{if !$compta}}colspan="9"{{else}}colspan="8"{{/if}}>Réglement Patients</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total {{if !$compta}}Facturé{{/if}}</th>
          {{if !$compta}}
          <th class="category">Total Réglé</th>
          {{/if}}
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
          {{if !$compta}}
          <th class="category">Non réglé</th>
          {{/if}}
        </tr>
        <tr>
          <th class="category">Nb consultations</th>
          <td>{{$recapitulatif.nb_patient}}</td>
          {{if !$compta}}
          <td>{{$recapitulatif.nb_patient-$recapitulatif.nb_non_reglee_patient}}</td>
          {{/if}}
          <td>{{$reglement.cheque.nb_reglement_patient}}</td>
          <td>{{$reglement.CB.nb_reglement_patient}}</td>
          <td>{{$reglement.especes.nb_reglement_patient}}</td>
          <td>{{$reglement.virement.nb_reglement_patient}}</td>
          <td>{{$reglement.autre.nb_reglement_patient}}</td>
          {{if !$compta}}
          <td>{{$recapitulatif.nb_non_reglee_patient}}</td>
          {{/if}}
        </tr>
        <tr>
          <th class="category">Total réglement patient</th>
          <td>{{$recapitulatif.somme_patient|string_format:"%.2f"}} &euro;</td>
          {{if !$compta}}
          <td>{{$recapitulatif.somme_patient-$recapitulatif.somme_non_reglee_patient|string_format:"%.2f"}} &euro;</td>
          {{/if}}
          <td>{{$reglement.cheque.du_patient|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.CB.du_patient|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.especes.du_patient|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.virement.du_patient|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.autre.du_patient|string_format:"%.2f"}} &euro;</td>
          {{if !$compta}}
          <td>{{$recapitulatif.somme_non_reglee_patient|string_format:"%.2f"}} &euro;</td>
          {{/if}}
        </tr>
        <tr>
          <th class="category" {{if !$compta}}colspan="9"{{else}}colspan="8"{{/if}}>Réglement Tiers</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total {{if !$compta}}Facturé{{/if}}</th>
          {{if !$compta}}
          <th class="category">Total Réglé</th>
          {{/if}}
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
          {{if !$compta}}
          <th class="category">Non réglé</th>
          {{/if}}
        </tr>
        <tr>
          <th class="category">Nb consultations</th>
          <td>{{$recapitulatif.nb_tiers}}</td>
          {{if !$compta}}
          <td>{{$recapitulatif.nb_tiers-$recapitulatif.nb_non_reglee_tiers}}</td>
          {{/if}}
          <td>{{$reglement.cheque.nb_reglement_tiers}}</td>
          <td>{{$reglement.CB.nb_reglement_tiers}}</td>
          <td>{{$reglement.especes.nb_reglement_tiers}}</td>
          <td>{{$reglement.virement.nb_reglement_tiers}}</td>
          <td>{{$reglement.autre.nb_reglement_tiers}}</td>
          {{if !$compta}}
          <td>{{$recapitulatif.nb_non_reglee_tiers}}</td>
          {{/if}}
        </tr>
        <tr>
          <th class="category">Total réglement Tiers</th>
          <td>{{$recapitulatif.somme_tiers|string_format:"%.2f"}} &euro;</td>
          {{if !$compta}}
          <td>{{$recapitulatif.somme_tiers-$recapitulatif.somme_non_reglee_tiers|string_format:"%.2f"}} &euro;</td>
          {{/if}}
          <td>{{$reglement.cheque.du_tiers|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.CB.du_tiers|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.especes.du_tiers|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.virement.du_tiers|string_format:"%.2f"}} &euro;</td>
          <td>{{$reglement.autre.du_tiers|string_format:"%.2f"}} &euro;</td>
          {{if !$compta}}
          <td>{{$recapitulatif.somme_non_reglee_tiers|string_format:"%.2f"}} &euro;</td>
          {{/if}}
        </tr>
        <tr>
          <th class="category" {{if !$compta}}colspan="9"{{else}}colspan="8"{{/if}}>Récapitulatif des factures</th>
        </tr>
        <tr>
          <th class="category">Total secteur 1</th>
          <td {{if !$compta}}colspan="8"{{else}}colspan="7"{{/if}}>{{$recapitulatif.total_secteur1|string_format:"%.2f"}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td {{if !$compta}}colspan="8"{{else}}colspan="7"{{/if}}>{{$recapitulatif.total_secteur2|string_format:"%.2f"}} &euro;</td>
        </tr>
        {{if !$compta}}
        <tr>
          <th class="category">Total non réglée (Patient)</th>
          <td colspan="8">{{$recapitulatif.somme_non_reglee_patient|string_format:"%.2f"}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Total non réglée (AMO/AMC)</th>
          <td colspan="8">{{$recapitulatif.somme_non_reglee_tiers|string_format:"%.2f"}} &euro;</td>
        </tr>
        {{/if}}
        <tr>
          <th class="category">Total facture</th>
          <td {{if !$compta}}colspan="8"{{else}}colspan="7"{{/if}}>{{$recapitulatif.total_secteur1+$recapitulatif.total_secteur2|string_format:"%.2f"}} &euro;</td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $aff}}
  {{foreach from=$listPlage item=curr_plage}}
  <tr>
    <td colspan="2"><b>{{$curr_plage->date|date_format:"%a %d %b %Y"}} de {{$curr_plage->debut|date_format:"%Hh%M"}} à {{$curr_plage->fin|date_format:"%Hh%M"}} - Dr. {{$curr_plage->_ref_chir->_view}}</b></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th>Patient</th>
          {{if !$compta}}
          <th>Tel</th>
          {{else}}
          <th>Date</th>
          {{/if}}
          <th>Type</th>
          <th>Code</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
          <th colspan="2">Réglement<br />Patient</th>
          <th colspan="2">Réglement<br />Tiers</th>
          <th colspan="2">Total<br />Facturé</th>
        </tr>
        {{foreach from=$curr_plage->_ref_consultations item=curr_consult}}
        <tr>
          <td class="text">
            <a name="consultation{{$curr_consult->consultation_id}}">{{$curr_consult->_ref_patient->_view}}</a>
          </td>
          {{if !$compta}}
          <td>{{$curr_consult->_ref_patient->tel}}</td>
          {{else}}
          <td>
          {{if $curr_consult->patient_date_reglement && $curr_consult->du_patient}}
          {{$curr_consult->patient_date_reglement|date_format:"%d/%m/%Y"}}
          {{/if}}
          {{if $curr_consult->tiers_date_reglement && $curr_consult->du_tiers}}
          {{$curr_consult->tiers_date_reglement|date_format:"%d/%m/%Y"}}
          {{/if}}
          </td>
          {{/if}}
          <td>
          {{if $curr_consult->patient_date_reglement}}
            {{$curr_consult->patient_mode_reglement}}
          {{/if}}
          {{if $curr_consult->tiers_date_reglement}}
            {{$curr_consult->tiers_mode_reglement}}
          {{/if}}
          </td>
          <td class="text">{{$curr_consult->tarif}}</td>
          <td>{{$curr_consult->secteur1|string_format:"%.2f"}} &euro;</td>
          <td>{{$curr_consult->secteur2|string_format:"%.2f"}} &euro;</td>
          <td>
            {{$curr_consult->du_patient|string_format:"%.2f"}} &euro;
          </td>
          
          <td>
          {{if $curr_consult->du_patient != "0"}}
            <form name="tarifFrm-{{$curr_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="du_patient" value="{{$curr_consult->du_patient}}" />
            
            <input type="hidden" name="consultation_id" value="{{$curr_consult->consultation_id}}" />
            {{if $curr_consult->patient_date_reglement}}
            <input type="hidden" name="du_patient" value="{{$curr_consult->du_patient}}" />
            <input type="hidden" name="patient_mode_reglement" value="" />
            <input type="hidden" name="patient_date_reglement" value="" />
            {{if $compta == "0"}}
            <button class="cancel notext" type="submit">Annuler</button>
              Réglée
            {{/if}}
            {{else}}
              {{if $compta == "0"}}
              <input type="hidden" name="du_patient" value="{{$curr_consult->du_patient}}" />
              <input type="hidden" name="patient_date_reglement" value="{{$today}}" />
              {{mb_field object=$curr_consult field=patient_mode_reglement}}
              <button type="submit" class="tick">Valider</button>
              {{/if}}
            {{/if}}
            </form>
            {{/if}}
          </td>
          <td {{if $compta}}colspan="2"{{/if}}>
          <!-- Total de l'assurance maladie -->
          {{$curr_consult->du_tiers|string_format:"%.2f"}} &euro;
          </td>
          {{if $compta == "0"}}
          <td>
            {{if $curr_consult->du_tiers}}
            <form name="tarifFrm_{{$curr_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="consultation_id" value="{{$curr_consult->consultation_id}}" />
            {{if $curr_consult->tiers_date_reglement}}
              <input type="hidden" name="tiers_date_reglement" value="" />
              <input type="hidden" name="tiers_mode_reglement" value="" />
              <button class="cancel notext" type="submit">Annuler</button>
              Réglée
              
            {{else}}
              <input type="hidden" name="tiers_date_reglement" value="{{$today}}" />
              {{mb_field object=$curr_consult field=tiers_mode_reglement}}
              <button type="submit" class="tick">Valider</button>
            {{/if}}
            </form>
            
            {{/if}}
          </td>
          {{/if}}
          <td>
            {{$curr_consult->_somme|string_format:"%.2f"}} &euro;
          </td>
           </tr>
        {{/foreach}}
        <tr>
          <td colspan="4" style="text-align:right;font-weight:bold;">Total</td>
          <td style="font-weight:bold;">{{$curr_plage->total1|string_format:"%.2f"}} &euro;</td>
          <td style="font-weight:bold;">{{$curr_plage->total2|string_format:"%.2f"}} &euro;</td>
          <td style="font-weight:bold;" colspan="2">{{$curr_plage->du_patient|string_format:"%.2f"}} &euro;</td>
          <td style="font-weight:bold;" colspan="2">{{$curr_plage->du_tiers|string_format:"%.2f"}} &euro;</td>
          <td style="font-weight:bold;" colspan="2">{{$curr_plage->total1+$curr_plage->total2|string_format:"%.2f"}} &euro;</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      