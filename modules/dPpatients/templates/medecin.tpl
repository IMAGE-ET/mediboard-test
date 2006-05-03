{if $end_of_process}
<script language="JavaScript" type="text/javascript">
{literal}
function pageMain() {
  window.opener.endProcess();
  window.close();
}

{/literal}
</script>

{else}

<script language="JavaScript" type="text/javascript">
{literal}
function pageMain() {
  {/literal}
  window.opener.endStep(
    {$from},
    {$to}, 
    {$medecins|@count}, 
    {$chrono->total|string_format:"%.3f"}, 
    {$parse_errors}, 
    {$sibling_errors}, 
    {$stores}
  );
  {literal}

  window.close();
}

{/literal}
</script>

<table class="tbl">
  <tr>
   	<th class="title" colspan="12">Résultat de l'étape #{$step}</th>
  </tr>

  <tr>
   	<th colspan="12">{$medecins|@count} médecins trouvés</th>
  </tr>

  {if $long_display}
  <tr>
  	<th>Nom</th>
    <th>Prénom</th>
    <th>Nom de jeune fille</th>
  	<th>Adresse</th>
  	<th>Ville</th>
  	<th>CP</th>
  	<th>Tél</th>
  	<th>Fax</th>
  	<th>Mél</th>
    <th>Disciplines qualifiantes</th>
    <th>Disciplines complémentaires d'exercice</th>
    <th>Mentions et orientations reconnue par l'Ordre</th>
  </tr>
  
  {foreach from=$medecins item=curr_medecin}
  <tr>
  	<td {if $curr_medecin->_has_siblings}style="background: #eef"{/if}>{$curr_medecin->nom}</td>
    <td>{$curr_medecin->prenom}</td>
    <td>{$curr_medecin->nom_jeunefille}</td>
  	<td>{$curr_medecin->adresse|nl2br}</td>
  	<td>{$curr_medecin->ville}</td>
  	<td>{$curr_medecin->cp}</td>
  	<td>{$curr_medecin->tel}</td>
  	<td>{$curr_medecin->fax}</td>
  	<td>{$curr_medecin->email}</td>
    <td>{$curr_medecin->disciplines|nl2br}</td>
    <td>{$curr_medecin->complementaires|nl2br}</td>
    <td>{$curr_medecin->orientations|nl2br}</td>
  </tr>
  {/foreach}
{/if}

</table>

{/if}
