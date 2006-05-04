<!-- $Id$ -->

{literal}
<script type="text/javascript">
function checkPlage() {
  var form = document.editFrm;
    
  if (form.chir_id.value == 0 && form.id_spec.value == 0) {
    alert("Merci de choisir un chirurgien ou une spécialité");
    form.chir_id.focus();
    return false;
  }
  
  if (form._heurefin.value < form._heuredeb.value || (form._heurefin.value == form._heuredeb.value && form._minutefin.value <= form._minutedeb.value)) {
    alert("L'heure de début doit être supérieure à la l'heure de fin");
    form._heurefin.focus();
    return false;
  }
  
  return true;
}

function pageMain() {
  {/literal}
  regRedirectFlatCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {literal}
  //regFieldCalendar("editFrm", "date");
}

</script>
{/literal}

{if $canEdit}

<form name='editFrm' action='./index.php?m={$m}' method='post' onsubmit='return checkPlage()'>

<input type='hidden' name='dosql' value='do_plagesop_aed' />
<input type='hidden' name='del' value='0' />
<input type='hidden' name='id' value='{$plagesel->id}' />

<table class="form">
  <tr>
    <th class="category" colspan="6">
      {if $plagesel->id}
	    <a style="float:right;" href="javascript:view_log('CPlageOp',{$plagesel->id})">
          <img src="images/history.gif" alt="historique" />
        </a>
        Modifier la plage opératoire
      {else}
        Ajouter une plage opératoire
      {/if}
      </th>
  </tr>

  <tr>
    <th class="mandatory">Chirurgien:</th>
    <td>
      <select name='chir_id'>
        <option value="0">-- Choisir un praticien</option>

        <optgroup label="Chirurgiens">
        {foreach from=$chirs item=chir}
          <option value="{$chir->user_id}" {if $plagesel->chir_id == $chir->user_id} selected="selected" {/if} >
            {$chir->_view}
          </option>
        {/foreach}
        </optgroup>

        <optgroup label="Anesthésistes">
        {foreach from=$anesths item=anesth}
          <option value="{$anesth->user_id}" {if $plagesel->chir_id == $anesth->user_id} selected="selected" {/if} >
            {$anesth->_view}
          </option>
        {/foreach}
        </optgroup>
      </select>
    </td>
    
    <th>Salle:</th>
    <td>
      <select name='id_salle'>
      {foreach from=$salles item=salle}
        <option value="{$salle->id}" {if $plagesel->id_salle == $salle->id} selected="selected"{/if} >
          {$salle->nom}
        </option>
      {/foreach}
      </select>
    </td>

    <th class="mandatory">Début:</th>
    <td>
      <select name='_heuredeb'>
      {foreach from=$heures item=heure}
        <option {if $plagesel->_heuredeb == $heure} selected="selected" {/if} >
          {$heure|string_format:"%02d"}
        </option>
      {/foreach}
      </select>
      :
      <select name='_minutedeb'>
      {foreach from=$minutes item=minute}
        <option {if $plagesel->_minutedeb == $minute} selected="selected" {/if} >
          {$minute|string_format:"%02d"}
        </option>
      {/foreach}
      </select>
    </td>
  </tr>

  <tr>
    <th>Anesthésiste:</th>
    <td>
      <select name='anesth_id'>
        <option value="0">-- Choisir un anesthésiste</option>
      {foreach from=$anesths item=anesth}
        <option value="{$anesth->user_id}" {if $plagesel->anesth_id == $anesth->user_id} selected="selected" {/if} >
          {$anesth->_view}
        </option>
      {/foreach}
	  </select>
    </td>

    <th>Date:</th>
    <td class="date">
      {if $plagesel->id}
      <div id="editFrm_date_da">{$plagesel->date|date_format:"%d/%m/%Y"}</div>
      <input type="hidden" name="date" value="{$plagesel->date}" />
      {else}
      <div id="editFrm_date_da">{$date|date_format:"%d/%m/%Y"}</div>
      <input type="hidden" name="date" value="{$date}" />
      {/if}
      <!-- Possibilité de changer la date ? -->
      <!--img id="editFrm_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date"/-->
    </td>

    <th class="mandatory">Fin:</th>
    <td>
      <select name='_heurefin'>
      {foreach from=$heures item=heure}
        <option {if $plagesel->_heurefin == $heure} selected="selected" {/if} >
          {$heure|string_format:"%02d"}
        </option>
      {/foreach}
      </select>
      :
      <select name='_minutefin'>
      {foreach from=$minutes item=minute}
        <option {if $plagesel->_minutefin == $minute} selected="selected" {/if} >
          {$minute|string_format:"%02d"}
        </option>
      {/foreach}
      </select>
    </td>
  </tr>
  
  <tr>
    <th class="mandatory">Spécialité:</th>
    <td colspan="5">
      <select name='id_spec'>
        <option value="0">-- Choisir une spécialité</option>
        {foreach from=$specs item=spec}
          <option value="{$spec->function_id}" {if $spec->function_id == $plagesel->id_spec} selected="selected" {/if} >
            {$spec->text}
          </option>
        {/foreach}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>Durée de répétition:</th>
    <td><input type="text" name="_repeat" size="1" value="1" /> semaine(s)</td>
    <td colspan="4"><input type="checkbox" name="_double" />Une semaine sur deux</td>
  </tr>
  
  <tr>
    <td class="button" colspan="6">
    {if $plagesel->id}
      <input type='reset' value='Réinitialiser' />
      <input type='submit' value='Modifier' />
    {else}
      <input type='submit' value='Ajouter' >
    {/if}
    </td>
  </tr>

</table>

</form>

{if $plagesel->id}
  <form name='removeFrm' action='./index.php?m={$m}' method='post'>

  <input type='hidden' name='dosql' value='do_plagesop_aed' />
  <input type='hidden' name='del' value='1' />
  <input type='hidden' name='id' value='{$plagesel->id}' /> 
  <table class="form">
    <tr>
      <th class="category" colspan="2">Supprimer la plage opératoire</th>
    </tr>
  
    <tr>
      <th>Supprimer cette plage pendant</th> 
      <td><input type='text' name='_repeat' size="1" value='1' /> semaine(s)</td>
    </tr>
   
    <tr>
      <td class="button" colspan="2">
        <input type='submit' value='Supprimer' />
      </td>
    </tr>
  </table>

  </form>
{/if}

{/if}

</td>
<td>

<div id="calendar-container"></div>

<table class="tbl">
  <tr>
  	<th>Liste des spécialités</th>
  </tr>
  {foreach from=$specs item=curr_spec}
  <tr>
    <td class="text" style="background: #{$curr_spec->color};">{$curr_spec->text}</td>
  </tr>
  {/foreach}
</table>

