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
     
      <table class="tbl">
        <tr>
          <th class="category" colspan="8">Réglement des patients</th>
        </tr>
        <tr>
          <th class="category">Type réglement</th>
          <th class="category">Total</th>
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Tiers</th>
          <th class="category">Autre</th>
          <th class="category">Non réglé</th>
        </tr>
        <tr>
          <th class="category">Nb consultations</th>
          <td>{{$total.nombre}}</td>
          <td>{{$total.cheque.nombre}}</td>
          <td>{{$total.CB.nombre}}</td>
          <td>{{$total.especes.nombre}}</td>
          <td>{{$total.tiers.nombre}}</td>
          <td>{{$total.autre.nombre}}</td>
          <td>{{$total.nombre-$total.cheque.nombre-$total.CB.nombre-$total.especes.nombre-$total.tiers.nombre-$total.autre.nombre}}</td>
        </tr>
        <tr>
          <th class="category">Total réglement patient</th>
          <td>{{$total.a_regler}}</td>
          <td>{{$total.cheque.reglement}} €</td>
          <td>{{$total.CB.reglement}} €</td>
          <td>{{$total.especes.reglement}} €</td>
          <td>{{$total.tiers.reglement}} €</td>
          <td>{{$total.autre.reglement}} €</td>
          <td>{{$total.somme_non_regle}} &euro;</td>
        </tr>
      </table>
       <table class="tbl">
        <tr>
          <th class="category" colspan="2">Récapitulatif des factures</th>
        </tr>
        <tr>
          <th class="category" width="10%">Total secteur 1</th>
          <td>{{$total.secteur1}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Total secteur 2</th>
          <td>{{$total.secteur2}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Total non acquittée</th>
          <td>{{$total.somme_non_acquitte}} &euro;</td>
        </tr>
        <tr>
          <th class="category">Total facture</th>
          <td>{{$total.secteur1+$total.secteur2}} &euro;</td>
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
            {{if $curr_consult->_somme != "0"}}
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
            {{/if}}
          </td>
          <td>
            {{$curr_consult->a_regler}} &euro;
          </td>
          <td>
          {{if $curr_consult->a_regler != "0"}}
            <form name="tarifFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="consultation_id" value="{{$curr_consult->consultation_id}}" />
            {{if $curr_consult->patient_regle}}
              <input type="hidden" name="patient_regle" value="0" />
              <input type="hidden" name="facture_acquittee" value="0" />
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
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="3" style="text-align:right;font-weight:bold;">Total</td>
          <td style="font-weight:bold;">{{$curr_plage->total1}} €</td>
          <td style="font-weight:bold;">{{$curr_plage->total2}} €</td>
          <td style="font-weight:bold;" colspan="2">{{$curr_plage->total1+$curr_plage->total2}} &euro;</td>
          <td style="font-weight:bold;" colspan="2">{{$curr_plage->a_regler}} &euro;</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      