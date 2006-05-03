{if $curr_adm->annulee == 1} {assign var=background value="#f33"}
{elseif $curr_adm->type_adm == 'ambu'} {assign var=background value="#faa"}
{elseif $curr_adm->type_adm == 'comp'} {assign var=background value="#fff"}
{elseif $curr_adm->type_adm == 'exte'} {assign var=background value="#afa"}
{/if}
<td class="text" style="background: {$background}">
  <a name="adm{$curr_adm->operation_id}" href="javascript:printAdmission({$curr_adm->operation_id})">
  {$curr_adm->_ref_pat->_view}
  </a>
</td>

<td class="text" style="background: {$background}">
  <a href="javascript:printAdmission({$curr_adm->operation_id})">
  Dr. {$curr_adm->_ref_chir->_view}
  </a>
</td>

<td style="background: {$background}">
  <a href="javascript:printAdmission({$curr_adm->operation_id})">
  {$curr_adm->time_adm|date_format:"%Hh%M"} ({$curr_adm->type_adm|truncate:1:"":true})
  </a>
</td>

<td class="text" style="background: {$background}">
  {assign var=affectation value=$curr_adm->_ref_first_affectation}
  {if $affectation->affectation_id}
  {$affectation->_ref_lit->_ref_chambre->_ref_service->nom} -
  {$affectation->_ref_lit->_ref_chambre->nom} -
  {$affectation->_ref_lit->nom}
  {else}
  Pas de chambre
  {/if}
    <form name="editChFrm{$curr_adm->operation_id}" action="index.php" method="post">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="otherm" value="dPadmissions" />
    <input type="hidden" name="dosql" value="do_edit_chambre" />
    <input type="hidden" name="id" value="{$curr_adm->operation_id}" />
    {if $curr_adm->chambre == 'o'}
    <input type="hidden" name="value" value="n" />
    <button type="button" style="background-color: #f55;" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
      <img src="modules/{$m}/images/refresh.png" alt="changer" /> simple
    </button>
    {else}
    <input type="hidden" name="value" value="o" />
    <button type="button" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
      <img src="modules/{$m}/images/refresh.png" alt="changer" /> double
    </button>
    {/if}
    </form>
</td>

{if $curr_adm->annulee == 1}
<td style="background: {$background}" align="center" colspan=2>
  <strong>ANNULE</strong></td>
{else}
<td style="background: {$background}">
  <form name="editAdmFrm{$curr_adm->operation_id}" action="index.php" method="post">
  <input type="hidden" name="m" value="{$m}" />
  <input type="hidden" name="dosql" value="do_edit_admis" />
  <input type="hidden" name="id" value="{$curr_adm->operation_id}" />
  <input type="hidden" name="mode" value="admis" />
  {if $curr_adm->admis == "n"}
  <input type="hidden" name="value" value="o" />
  <button type="button" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
    <img src="modules/{$m}/images/tick.png" alt="Admis" /> Admis
  </button>
  {else}
  <input type="hidden" name="value" value="n" />
  <button type="button" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
    <img src="modules/{$m}/images/cross.png" alt="Annuler" /> Annuler
  </button>
  {/if}
  </form>
</td>

<td style="background: {$background}">
  <form name="editSaisFrm{$curr_adm->operation_id}" action="index.php" method="post">
  <input type="hidden" name="m" value="{$m}" />
  <input type="hidden" name="dosql" value="do_edit_admis" />
  <input type="hidden" name="id" value="{$curr_adm->operation_id}" />
  <input type="hidden" name="mode" value="saisie" />
  {if $curr_adm->saisie == "n"}
  <input type="hidden" name="value" value="o" />
  <button type="button" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
    <img src="modules/{$m}/images/tick.png" alt="Saisie" /> Saisie
  </button>
  {else}
  <input type="hidden" name="value" value="n" />
  <button type="button" onclick="admission_id = {$curr_adm->operation_id}; submitAdmission(this.form);">
    <img src="modules/{$m}/images/cross.png" alt="Annuler" /> Annuler
  </button>
  {/if}
  {if $curr_adm->modifiee == 1}
  <img src="images/icons/rc-gui-status-downgr.png" alt="modifié" />
  {/if}
  </form>
</td>
{/if}

<td style="background: {$background}">
{if $curr_adm->depassement}
<!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
<!-- <a href="javascript:printDepassement({$curr_adm->operation_id})"></a> -->
{$curr_adm->depassement} €
{else}-{/if}</td>