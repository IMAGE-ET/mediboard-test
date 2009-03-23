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
      </table>
    </td>
    
    <td class="halfPane">
     
      <table class="tbl">
        <tr>
          <th class="category" colspan="7">Réglement Patients</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
        </tr>
        <tr>
          <th class="category">Nb réglements</th>
          <td>{{$recapReglement.total.nb_reglement_patient}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_patient}}</td>
          <td>{{$recapReglement.CB.nb_reglement_patient}}</td>
          <td>{{$recapReglement.especes.nb_reglement_patient}}</td>
          <td>{{$recapReglement.virement.nb_reglement_patient}}</td>
          <td>{{$recapReglement.autre.nb_reglement_patient}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement patient</th>
          <td>{{$recapReglement.total.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.cheque.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.CB.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.especes.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.virement.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.autre.du_patient|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category" colspan="7">Réglement Tiers</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
        </tr>
        <tr>
          <th class="category">Nb réglements</th>
          <td>{{$recapReglement.total.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.CB.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.especes.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.virement.nb_reglement_tiers}}</td>
          <td>{{$recapReglement.autre.nb_reglement_tiers}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement Tiers</th>
          <td>{{$recapReglement.total.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.cheque.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.CB.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.especes.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.virement.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$recapReglement.autre.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category" colspan="7">Récapitulatif des consultations concernées</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="6">{{$listConsults|@count}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 1</th>
          <td colspan="6">{{$recapReglement.total.secteur1|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td colspan="6">{{$recapReglement.total.secteur2|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category">Total facturé</th>
          <td colspan="6">{{$recapReglement.total.secteur1+$recapReglement.total.secteur2|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
        <tr>
          <th class="category">Total réglé</th>
          <td colspan="6">{{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}</td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $filter->_type_affichage}}
  {{foreach from=$listReglements key=key_date item=curr_date}}
  <tr>
    <td colspan="2"><strong>Règlements du {{$key_date|date_format:$dPconfig.longdate}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Type</th>
          <th>Code</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
          <th>Total<br />facturé</th>
          <th>Réglement<br />patient</th>
          <th>Réglement<br />tiers</th>
        </tr>
        {{foreach from=$curr_date.reglements item=curr_reglement}}
        <tr>
          <td class="text">Dr {{$curr_reglement->_ref_consultation->_ref_chir->_view}}</td>
          <td class="text">{{$curr_reglement->_ref_consultation->_ref_patient->_view}}</td>
          <td>{{$curr_reglement->mode}}</td>
          <td class="text">{{$curr_reglement->_ref_consultation->tarif}}</td>
          <td>{{$curr_reglement->_ref_consultation->secteur1}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$curr_reglement->_ref_consultation->secteur2}} {{$dPconfig.currency_symbol}}</td>
          <td>{{$curr_reglement->_ref_consultation->secteur1+$curr_reglement->_ref_consultation->secteur2}} {{$dPconfig.currency_symbol}}</td>
          <td>
            {{if $curr_reglement->emetteur == "patient"}}
              {{$curr_reglement->montant}} {{$dPconfig.currency_symbol}}
            {{else}}
              0  {{$dPconfig.currency_symbol}}
            {{/if}}
          </td>
          <td>
            {{if $curr_reglement->emetteur == "tiers"}}
              {{$curr_reglement->montant}}
            {{else}}
              0  {{$dPconfig.currency_symbol}}
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="6" />
          <th>Total</th>
          <td><strong>{{$curr_date.total.patient}} {{$dPconfig.currency_symbol}}</strong></td>
          <td><strong>{{$curr_date.total.tiers}} {{$dPconfig.currency_symbol}}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      