{if $selOp->libelle}
  <em>[{$selOp->libelle}]</em>
{/if}
<ul>
  {foreach from=$selOp->_ext_codes_ccam item=curr_code key=curr_key}
  <li>
    <strong>{$curr_code->libelleLong|escape}</strong> 
    <em>(<a class="action" href="?m=dPccam&amp;tab=vw_full_code&amp;codeacte={$curr_code->code}">{$curr_code->code}</a>)</em>
    <br />Codes associés :
    <select name="asso" onchange="setCode(this.value, 'ccam')">
      <option value="">&mdash; choix</option>
      {foreach from=$curr_code->assos item=curr_asso}
      <option value="{$curr_asso.code}">{$curr_asso.code}({$curr_asso.texte|truncate:40:"...":true})</option>
      {/foreach}
    </select>

    {foreach from=$curr_code->activites item=curr_activite}
    {foreach from=$curr_activite->phases item=curr_phase}
    {assign var="acte" value=$curr_phase->_connected_acte}
    {assign var="view" value=$acte->_view}
    {assign var="key" value="$curr_key$view"}
    <form name="formActe-{$acte->_view}" action="?m={$m}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="dPsalleOp" />
    <input type="hidden" name="dosql" value="do_acteccam_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="acte_id" value="{$acte->acte_id}" />
    <input type="hidden" name="operation_id" title="{$acte->_props.operation_id}" value="{$selOp->operation_id}" />
    <input type="hidden" name="code_acte" title="{$acte->_props.code_acte}" value="{$acte->code_acte}" />
    <input type="hidden" name="code_activite" title="{$acte->_props.code_activite}" value="{$acte->code_activite}" />
    <input type="hidden" name="code_phase" title="{$acte->_props.code_phase}" value="{$acte->code_phase}" />
    <input type="hidden" name="montant_depassement" title="{$acte->_props.montant_depassement}" value="{$acte->montant_depassement}" />

    <table class="form">
      
      <tr id="trigger{$key}" class="triggerShow" onclick="flipEffectElement('group{$key}', 'SlideDown', 'SlideUp', 'trigger{$key}')">  
        <td colspan="2">
          Activité {$curr_activite->numero} ({$curr_activite->type|escape}) &mdash; 
          Phase {$curr_phase->phase} : {$curr_phase->libelle|escape}
        </td>
      </tr>
    
      <tr style="display: none;">
        <th><label for="execution" title="Date et heure d'exécution de l'acte">Exécution :</label></th>
        <td>
          <input type="text" name="execution" title="{$acte->_props.execution}" readonly="readonly" value="{$acte->execution}" />
          <input type="button" value="Maintenant" onclick="this.form.execution.value = makeDATETIMEFromDate(new Date());" /><br />
        </td>
      </tr>
      
      <tbody id="group{$key}" style="display: none">
      
      <tr class="{$key}">
        <th><label for="executant_id" title="Professionnel de santé exécutant l'acte">Exécutant :</label></th>
        <td>
          {if $curr_activite->numero == 4}
            {assign var="listExecutants" value=$listAnesths}
          {else}
            {assign var="listExecutants" value=$listChirs}
          {/if}
          <select name="executant_id" title="{$acte->_props.executant_id}">
            <option value="">&mdash; Choisir un professionnel de santé</option>
            {foreach from=$listExecutants item=curr_executant}
            <option value="{$curr_executant->user_id}" {if $acte->executant_id == $curr_executant->user_id} selected="selected" {/if}>{$curr_executant->_view}</option>
            {/foreach}
          </select>
        </td>
      </tr>

      <tr class="{$acte->_view}">
        <th><label for="modificateurs" title="Modificateurs associés à l'acte">Modificateur(s) :</label></th>
        <td class="text">
          {foreach from=$curr_phase->_modificateurs item=curr_mod}
          <input type="checkbox" name="modificateur_{$curr_mod->code}" {if $curr_mod->_value}checked="checked"{/if} />
          <label for="modificateur_{$curr_mod->code}" title="{$curr_mod->libelle|escape}">{$curr_mod->code} : {$curr_mod->libelle|escape}</label>
          <br />
          {/foreach}
        </td>
      </tr>
        
      <tr class="{$acte->_view}">
        <th><label for="commentaire" title="Commentaires sur l'acte">Commentaire :</label></th>
        <td><textarea name="commentaire" title="{$acte->_props.commentaire}">{$acte->commentaire}</textarea></td>
      </tr>
      
      </tbody>
    
      <tr>
        <td class="button" colspan="2">
          {if $acte->acte_id}
          <input type="submit" value="Modifier cet acte" />
          <input type="button" value="Supprimer cet acte" onclick="confirmDeletion(this.form,{ldelim}typeName:'l\'acte',objName:'{$acte->_view|escape:javascript}'{rdelim})"  />
          {else}
          <input type="submit" value="Coder cet acte" style="background-color: #faa" />
          {/if}
        </td>
      </tr>
      
    </table>
    </form>
  {/foreach}
  {/foreach}
  </li>
  {/foreach}
</ul>