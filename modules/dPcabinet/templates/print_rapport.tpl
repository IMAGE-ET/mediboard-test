<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
              {{if $filter->_date_min != $filter->_date_min}}
              au {{$filter->_date_min|date_format:"%d/%m/%Y"}}
              {{/if}}
            </a>
          </th>
        </tr>
        {{if $chirSel->user_id}}<tr><th>Dr. {{$chirSel->_view}}</th></tr>{{/if}}
        <tr><td>affichage {{if $etat == -1}}de tous les montants{{elseif $etat}}des montants réglés{{else}}des impayés{{/if}}</td></tr>
        <tr><td>Paiments pris en compte : {{if $type}}{{$type}}{{else}}tous{{/if}}</td></tr>
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr><th class="category" colspan="2">Récapitulatif</th></tr>
        <tr><th>Secteur 1 :</th><td>{{$total.secteur1}} €</td></tr>
        <tr><th>Secteur 2 :</th><td>{{$total.secteur2}} €</td>
          {{if $etat != 0}}
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Tiers</th>
          <th class="category">Autre</th>
          {{/if}}
        </tr>
        <tr><th>Nombre de consultations :</th><td>{{$total.nombre}}</td>
          {{if $etat != 0}}
          <td>{{$total.cheque.nombre}}</td>
          <td>{{$total.CB.nombre}}</td>
          <td>{{$total.especes.nombre}}</td>
          <td>{{$total.tiers.nombre}}</td>
          <td>{{$total.autre.nombre}}</td>
          {{/if}}
        </tr>
        <tr><th>Valeur totale :</th><td>{{$total.tarif}} €</td>
          {{if $etat != 0}}
          <td>{{$total.cheque.valeur}} €</td>
          <td>{{$total.CB.valeur}} €</td>
          <td>{{$total.especes.valeur}} €</td>
          <td>{{$total.tiers.valeur}} €</td>
          <td>{{$total.autre.valeur}} €</td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{if $aff}}
  {{foreach from=$listPlage item=curr_plage}}
  <tr>
    <td coslpan="2"><b>{{$curr_plage->date|date_format:"%a %d %b %Y"}} - Dr. {{$curr_plage->_ref_chir->_view}}</b></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th>Patient</th>
          <th>Type</th>
          <th>Code</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
          <th colspan="2">Total Facturé</th>
          <th colspan="2">Réglement du patient</th>
        </tr>
        {{foreach from=$curr_plage->_ref_consultations item=curr_consult}}
        <tr>
          <td><a name="consultation{{$curr_consult->consultation_id}}">{{$curr_consult->_ref_patient->_view}}</a></td>
          <td>{{$curr_consult->mode_reglement}}</td>
          <td>{{$curr_consult->tarif}}</td>
          <td>{{$curr_consult->secteur1}}€</td>
          <td>{{$curr_consult->secteur2}}€</td>
          <td>
            {{$curr_consult->_somme}} &euro;
          </td>
          <td>
            <form name="tarifFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="consultation_id" value="{{$curr_consult->consultation_id}}" />
            {{if $curr_consult->facture_acquittee}}
              <input type="hidden" name="facture_acquittee" value="0" />
              <input type="hidden" name="date_paiement" value="" />
              <button class="cancel notext" type="submit"></button>
              Acquittée
            {{else}}
              <input type="hidden" name="facture_acquittee" value="1" />
              <button type="submit" class="tick">Acquitter</button>
            {{/if}}
            </form>
          </td>
          <td>
            {{$curr_consult->a_regler}} &euro;
          </td>
          <td>
            <form name="tarifFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="consultation_id" value="{{$curr_consult->consultation_id}}" />
            {{if $curr_consult->patient_regle}}
              <input type="hidden" name="patient_regle" value="0" />
              <input type="hidden" name="date_paiement" value="" />
              <button class="cancel notext" type="submit"></button>
              Réglée
            {{else}}
              <input type="hidden" name="patient_regle" value="1" />
              <input type="hidden" name="date_paiement" value="{{$today}}" />
              <select name="mode_reglement">
                <option value="cheque"  {{if $curr_consult->mode_reglement == "cheque" }}selected="selected"{{/if}}>Chèques     </option>
                <option value="CB"      {{if $curr_consult->mode_reglement == "CB"     }}selected="selected"{{/if}}>CB          </option>
                <option value="especes" {{if $curr_consult->mode_reglement == "especes"}}selected="selected"{{/if}}>Espèces     </option>
                <option value="tiers"   {{if $curr_consult->mode_reglement == "tiers"  }}selected="selected"{{/if}}>Tiers-payant</option>
                <option value="autre"   {{if $curr_consult->mode_reglement == "autre"  }}selected="selected"{{/if}}>Autre       </option>
              </select>
              <button type="submit" class="tick">Valider</button>
            {{/if}}
            </form>
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="3" style="text-align:right;font-weight:bold;">Total</td>
          <td style="font-weight:bold;">{{$curr_plage->total1}} €</td>
          <td style="font-weight:bold;">{{$curr_plage->total2}} €</td>
          <td style="font-weight:bold;">{{$curr_plage->total1+$curr_plage->total2}}</td>
          <td />
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      