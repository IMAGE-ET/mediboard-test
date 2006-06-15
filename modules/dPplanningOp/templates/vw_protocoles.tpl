<!-- $Id$ -->

{literal}
<script type="text/javascript">
function setClose(user_id, user_last_name, user_first_name, codes_ccam, libelle, _hour_op, _min_op, examen, materiel, convalescence, depassement, type, duree_hospi, rques_sejour) {
  window.opener.setProtocole(user_id, user_last_name, user_first_name, codes_ccam, libelle, _hour_op, _min_op, examen, materiel, convalescence, depassement, type, duree_hospi, rques_sejour);
  window.close();
}
</script>
{/literal}

<table class="main">
  <tr>
    <td colspan="2">

      <form name="selectFrm" action="index.php" method="get">
      
      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" {if $dialog} name="a" {else} name="tab" {/if} value="vw_protocoles" />
      <input type="hidden" name="dialog" value="{$dialog}" />

      <table class="form">
        <tr>
          <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien :</label></th>
          <td>
            <select name="chir_id" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les chirurgiens</option>
              {foreach from=$listPrat item=curr_prat}
              {if $curr_prat->_ref_protocoles|@count}
              <option value="{$curr_prat->user_id}" {if $chir_id == $curr_prat->user_id} selected="selected" {/if}>
                {$curr_prat->_view} ({$curr_prat->_ref_protocoles|@count})
              </option>
              {/if}
              {/foreach}
            </select>
          </td>
          <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM :</label></th>
          <td>
            <select name="code_ccam" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les codes</option>
              {foreach from=$listCodes key=curr_code item=code_nomber}
              <option value="{$curr_code}" {if $code_ccam == $curr_code} selected="selected" {/if}>
                {$curr_code} ({$code_nomber})
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
      </table>
      </form>    
      
    </td>
  </tr>

  <tr>
    {if $dialog}
    <td class="greedyPane">
    {else}
    <td class="halfPane">
    {/if}

      <table class="tbl">
        <tr>
          <th>Chirurgien &mdash; Acte CCAM</th>
        </tr>
        
        {foreach from=$protocoles item=curr_protocole}
        <tr>    
          <td class="text">
            {if $dialog}
            <a href="javascript:setClose('{$curr_protocole->_ref_chir->user_id}','{$curr_protocole->_ref_chir->_user_last_name|escape:javascript}','{$curr_protocole->_ref_chir->_user_first_name|escape:javascript}','{$curr_protocole->codes_ccam}','{$curr_protocole->libelle}','{$curr_protocole->_hour_op}','{$curr_protocole->_min_op}','{$curr_protocole->examen|escape:javascript}','{$curr_protocole->materiel|escape:javascript}','{$curr_protocole->convalescence|escape:javascript}','{$curr_protocole->depassement}','{$curr_protocole->type}','{$curr_protocole->duree_hospi}','{$curr_protocole->rques_sejour|escape:javascript}')">            {else}
            <a href="?m={$m}&amp;{if $dialog}a=vw_protocoles&amp;dialog=1{else}tab={$tab}{/if}&amp;protocole_id={$curr_protocole->protocole_id}">
            {/if}
              <strong>
                {$curr_protocole->_ref_chir->_view} 
                {foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}
                &mdash; {$curr_code->code}
                {/foreach}
              </strong>
            </a>
            {foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}
            {$curr_code->libelleLong} <br />
            {/foreach}
          </td>
        </tr>
        {/foreach}

      </table>

    </td>
    <td class="halfPane">

      {if $protSel->protocole_id && !$dialog}
      <table class="form">
        <tr>
          <th class="category" colspan="2">Détails du protocole</th>
        </tr>

        <tr>
          <th>Chirurgien :</th>
          <td colspan="3"><strong>{$protSel->_ref_chir->_view}</strong></td>
        </tr>

        <tr>
          <th>Acte Médical :</th>
          <td class="text">
          {foreach from=$protSel->_ext_codes_ccam item=curr_code}
            <strong>{$curr_code->code}</strong><br />{$curr_code->libelleLong}<br />
          {/foreach}
          </td>
        </tr>
        
        <tr>
          <th>Temps opératoire :</th>
          <td>{$protSel->_hour_op}h{if $protSel->_min_op}{$protSel->_min_op}{/if}</td>
        </tr>
        
		    {if $protSel->depassement}
        <tr>	
          <th>Dépassement d'honoraire:</th>
          <td>{$protSel->depassement}€</td>
		    <tr>
		    {/if}

        {if $protSel->examen}
        <tr>
          <th class="text" colspan="2">Bilan Pré-op</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{$protSel->examen|nl2br}</td>
        </tr>
        {/if}
        
        {if $protSel->materiel}
        <tr>
          <th class="text" colspan="2">Matériel à prévoir</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{$protSel->materiel|nl2br}</td>
        </tr>
        {/if}
        
        {if $protSel->convalescence}
        <tr>
          <th class="text" colspan="2">Convalescence</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{$protSel->convalescence|nl2br}</td>
        </tr>
        {/if}

        <tr>
          <th class="category" colspan="2">Détails de l'hospitalisation</th>
        </tr>
        
        <tr>
          <th>Admission en:</th>
          <td>
            {if $protSel->type == "comp"} Hospitalisation complète{/if}
            {if $protSel->type == "ambu"} Ambulatoire{/if}
            {if $protSel->type == "exte"} Externe{/if}
          </td>
        </tr>

        <tr>
          <th>Durée d'hospitalisation:</th>
          <td>{$protSel->duree_hospi} jours</td>
        </tr>
  
        {if $protSel->rques_sejour}
        <tr>
          <th class="text" colspan="2">Remarques du séjour</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{$protSel->rques_sejour|nl2br}</td>
        </tr>
        {/if}

        {if $canEdit}
        <tr>
          <td class="button" colspan="2">
            <form name="modif" action="./index.php" method="get">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="tab" value="vw_edit_protocole" />
            <input type="hidden" name="protocole_id" value="{$protSel->protocole_id}" />
            <input type="submit" value="Modifier" />
            </form>
          </td>
        </tr>
        {/if}
      
      </table>
      
      {/if} 
     </td>
  </tr>
</table>

