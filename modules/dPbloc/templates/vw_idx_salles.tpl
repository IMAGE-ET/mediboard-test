<!-- $Id$ -->

{literal}
<script type="text/javascript">
function checkSalle() {
  var form = document.salle;
  var field = null;
  
  if (field = form.nom) {
    if (field.value.length == 0) {
      alert("Intitulé manquant");
      field.focus();
      return false;
    }
  }   
    
  return true;
}
</script>
{/literal}

<table class="main">

<tr>
  <td class="halfPane">

    <a href="index.php?m={$m}&amp;tab={$tab}&amp;salle_id=0"><strong>Créer une salle</strong></a>

    <table class="tbl">
      
    <tr>
      <th>liste des salles</th>
    </tr>
    
    {foreach from=$salles item=curr_salle}
    <tr>
      <td><a href="index.php?m={$m}&amp;tab={$tab}&amp;salle_id={$curr_salle->id}">{$curr_salle->nom}</a></td>
    </tr>
    {/foreach}
      
    </table>

  </td>
  
  <td class="halfPane">

    <form name="salle" action="./index.php?m={$m}" method="post" onsubmit="return checkSalle()">
    <input type="hidden" name="dosql" value="do_salle_aed" />
    <input type="hidden" name="id" value="{$salleSel->id}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {if $salleSel->id}
        Modification de la salle &lsquo;{$salleSel->nom}&rsquo;
      {else}
        Création d'une salle
      {/if}
      </th>
    </tr>

    <tr>
      <th class="mandatory">Intitulé:</th>
      <td><input type="text" name="nom" value="{$salleSel->nom}" /></td>
    </tr>
    
    <tr>
      <th>Stats</th>
      <td>
        <input type="radio" name="stats" value="1" {if $salleSel->stats}checked="checked"{/if}> Oui /
        <input type="radio" name="stats" value="0" {if !$salleSel->stats || !$salleSel->id}checked="checked"{/if}> Non
      </td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {if $salleSel->id}
        <input type="reset" value="Réinitialiser" />
        <input type="submit" value="Valider" />
        <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'la salle',objName:'{$salleSel->nom|escape:javascript}'{rdelim})""/>
        {else}
        <input type="submit" name="btnFuseAction" value="Créer" />
        {/if}
      </td>
    </tr>

    </table>

  </td>
</tr>

</table>
