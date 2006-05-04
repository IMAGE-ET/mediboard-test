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
        {if $chirSel->user_id}
        <tr><th>Dr. {$chirSel->_view}</th></tr>
        {/if}
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
    <td coslpan="2"><b>{$curr_plage.date|date_format:"%a %d %b %Y"} - Dr. {$curr_plage._ref_chir->_view}</b></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th width="15%">Patient</th>
          <th width="15%">Type</th>
          <th width="15%">Code</th>
          <th width="15%">Secteur 1</th>
          <th width="15%">Secteur 2</th>
          <th width="15%">Total</th>
        </tr>
        {foreach from=$curr_plage._ref_consultations item=curr_consult}
        <tr>
          <td><a name="consultation{$curr_consult->consultation_id}">{$curr_consult->_ref_patient->_view}</a></td>
          <td>{$curr_consult->type_tarif}</td>
          <td>{$curr_consult->tarif}</td>
          <td>{$curr_consult->secteur1} €</td>
          <td>{$curr_consult->secteur2} €</td>
          <td>{if $etat == -1 && !$curr_consult->paye}0{else}{$curr_consult->secteur1+$curr_consult->secteur2}{/if} €</td>
        </tr>
        {/foreach}
        <tr>
          <td colspan="3" style="text-align:right;font-weight:bold;">Total</td>
          <td style="font-weight:bold;">{$curr_plage.total1} €</td>
          <td style="font-weight:bold;">{$curr_plage.total2} €</td>
          <td style="font-weight:bold;">{$curr_plage.total1+$curr_plage.total2} €</td>
        </tr>
      </table>
    </td>
  </tr>
  {/foreach}
  {/if}
</table>
      