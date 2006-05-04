<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="javascript:window.print()">
              Rapport du {$deb|date_format:"%d/%m/%Y"}
              {if $deb != $fin}
              au {$fin|date_format:"%d/%m/%Y"}
              {/if}
            </a>
          </th>
        </tr>
        {if $chirSel->user_id}<tr><th>Dr. {$chirSel->_view}</th></tr>{/if}
        <tr><td>affichage {if $etat == -1}de tous les montants{elseif $etat}des montants réglés{else}des impayés{/if}</td></tr>
        <tr><td>Paiments pris en compte : {if $type}{$type}{else}tous{/if}</td></tr>
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr><th class="category" colspan="2">Récapitulatif</th></tr>
        <tr><th>Secteur 1 :</th><td>{$total.secteur1} €</td></tr>
        <tr><th>Secteur 2 :</th><td>{$total.secteur2} €</td>
          {if $etat != 0}
          <th class="category">Chèque</th>
          <th class="category">CB</th>
          <th class="category">Espèces</th>
          <th class="category">Tiers</th>
          <th class="category">Autre</th>
          {/if}
        </tr>
        <tr><th>Nombre de consultations :</th><td>{$total.nombre}</td>
          {if $etat != 0}
          <td>{$total.cheque.nombre}</td>
          <td>{$total.CB.nombre}</td>
          <td>{$total.especes.nombre}</td>
          <td>{$total.tiers.nombre}</td>
          <td>{$total.autre.nombre}</td>
          {/if}
        </tr>
        <tr><th>Valeur totale :</th><td>{$total.tarif} €</td>
          {if $etat != 0}
          <td>{$total.cheque.valeur} €</td>
          <td>{$total.CB.valeur} €</td>
          <td>{$total.especes.valeur} €</td>
          <td>{$total.tiers.valeur} €</td>
          <td>{$total.autre.valeur} €</td>
          {/if}
        </tr>
      </table>
    </td>
  </tr>
  {if $aff}
  {foreach from=$listPlage item=curr_plage}
  <tr>
    <td coslpan="2"><b>{$curr_plage->date|date_format:"%a %d %b %Y"} - Dr. {$curr_plage->_ref_chir->_view}</b></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th width="16%">Patient</th>
          <th width="14%">Type</th>
          <th width="14%">Code</th>
          <th width="14%">Secteur 1</th>
          <th width="14%">Secteur 2</th>
          <th width="14%">Total</th>
          <th width="14%">Paiement</th>
        </tr>
        {foreach from=$curr_plage->_ref_consultations item=curr_consult}
        <tr>
          <td><a name="consultation{$curr_consult->consultation_id}">{$curr_consult->_ref_patient->_view}</a></td>
          <td>{$curr_consult->type_tarif}</td>
          <td>{$curr_consult->tarif}</td>
          <td>{$curr_consult->secteur1} €</td>
          <td>{$curr_consult->secteur2} €</td>
          <td>{if $etat == -1 && !$curr_consult->paye}0{else}{$curr_consult->secteur1+$curr_consult->secteur2}{/if} €</td>
          <td>
            <form name="tarifFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_dialog" value="print_rapport" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="consultation_id" value="{$curr_consult->consultation_id}" />
            <input type="hidden" name="_check_premiere" value="{$curr_consult->_check_premiere}" />
            {if $curr_consult->paye}
              <input type="hidden" name="paye" value="0" />
              <input type="hidden" name="date_paiement" value="" />
              <button type="submit">Annuler</button>
            {else}
              <input type="hidden" name="paye" value="1" />
              <input type="hidden" name="date_paiement" value="{$today}" />
              <select name="type_tarif">
                <option value="cheque"  {if $curr_consult->type_tarif == "cheque" }selected="selected"{/if}>Chèques     </option>
                <option value="CB"      {if $curr_consult->type_tarif == "CB"     }selected="selected"{/if}>CB          </option>
                <option value="especes" {if $curr_consult->type_tarif == "especes"}selected="selected"{/if}>Espèces     </option>
                <option value="tiers"   {if $curr_consult->type_tarif == "tiers"  }selected="selected"{/if}>Tiers-payant</option>
                <option value="autre"   {if $curr_consult->type_tarif == "autre"  }selected="selected"{/if}>Autre       </option>
              </select>
              <button type="submit"><img src="modules/{$m}/images/tick.png" title="valider" /></button>
            {/if}
            </form>
          </td>
        </tr>
        {/foreach}
        <tr>
          <td colspan="3" style="text-align:right;font-weight:bold;">Total</td>
          <td style="font-weight:bold;">{$curr_plage->total1} €</td>
          <td style="font-weight:bold;">{$curr_plage->total2} €</td>
          <td style="font-weight:bold;">{$curr_plage->total1+$curr_plage->total2} €</td>
          <td />
        </tr>
      </table>
    </td>
  </tr>
  {/foreach}
  {/if}
</table>
      