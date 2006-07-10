{literal}
<script type="text/javascript">

function pageMain() {
  {/literal}
  regRedirectPopupCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");

  initGroups("acte");
  {literal}
}

</script>
{/literal}

<table class="main">
  <tr>
    <td style="width: 200px;">
    
      <form action="index.php" name="selection" method="get">
      
      <input type="hidden" name="m" value="{$m}" />
  
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {$date|date_format:"%A %d %B %Y"}
            <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        
        <tr>
          <th><label for="salle" title="Salle d'opération">Salle</label></th>
          <td>
            <select name="salle" onchange="this.form.submit()">
              <option value="0">&mdash; Aucune salle</option>
              {foreach from=$listSalles item=curr_salle}
              <option value="{$curr_salle->id}" {if $curr_salle->id == $salle} selected="selected" {/if}>
              {$curr_salle->nom}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
      </table>
      
      </form>
            
      {foreach from=$plages item=curr_plage}
      <hr />
      
      <form name="anesth{$curr_plage->id}" action="index.php" method="post">

      <input type="hidden" name="m" value="dPbloc" />
      <input type="hidden" name="otherm" value="{$m}" />
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_repeat" value="1" />
      <input type="hidden" name="id" value="{$curr_plage->id}" />
      <input type="hidden" name="chir_id" value="{$curr_plage->chir_id}" />

      <table class="form">
        <tr>
          <th class="category" colspan="2">
            <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;id={$curr_plage->id}" title="Administrer la plage">
              Plage du Dr. {$curr_plage->_ref_chir->_view}
              de {$curr_plage->debut|date_format:"%Hh%M"} à {$curr_plage->fin|date_format:"%Hh%M"}
            </a>
          </th>
        </tr>
      
        <tr>
          <th><label for="anesth_id" title="Anesthésiste associé à la plage d'opération">Anesthésiste</label></th>
          <td>
            <select name="anesth_id" onchange="submit()">
              <option value="0">&mdash; Choisir un anesthésiste</option>
              {foreach from=$listAnesths item=curr_anesth}
              <option value="{$curr_anesth->user_id}" {if $curr_plage->anesth_id == $curr_anesth->user_id} selected="selected" {/if}>{$curr_anesth->_view}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        
      </table>

      </form>

       <table class="tbl">
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          <th>Intervention</th>
          <th>Coté</th>
          <th>Durée</th>
        </tr>
        {foreach from=$curr_plage->_ref_operations item=curr_operation}
        <tr>
          {if $curr_operation->entree_bloc && $curr_operation->sortie_bloc}
          <td style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
          {elseif $curr_operation->entree_bloc}
          <td style="background-color:#cfc">
          {elseif $curr_operation->sortie_bloc}
          <td style="background-color:#fcc">
          {else}
          <td>
          {/if}
            <a href="index.php?m={$m}&amp;op={$curr_operation->operation_id}" title="Coder l'intervention">
            {$curr_operation->time_operation|date_format:"%Hh%M"}
            </a>
          </td>
          <td>{$curr_operation->_ref_sejour->_ref_patient->_view}</td>
          <td>
            <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={$curr_operation->operation_id}" title="Modifier l'intervention">
              {foreach from=$curr_operation->_ext_codes_ccam item=curr_code}
              {$curr_code->code}<br />
              {/foreach}
            </a>
          </td>
          <td>{$curr_operation->cote}</td>
          <td>{$curr_operation->temp_operation|date_format:"%Hh%M"}</td>
        </tr>
        {/foreach}
      </table>
      {/foreach}
    </td>
    <td class="greedyPane">
      <table class="tbl">
        {if $selOp->operation_id}
        <tr>
          <th class="title" colspan="2">
            {$selOp->_ref_sejour->_ref_patient->_view} 
            ({$selOp->_ref_sejour->_ref_patient->_age} ans) 
            &mdash; Dr. {$selOp->_ref_chir->_view}
          </th>
        </tr>
        <tr>
          <th>Horaires début</th>
          <td>
            <table class="form">
              <tr>
                <td>
	              <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
	                <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="entree_bloc" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->entree_bloc}
	                Entrée du patient:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->entree_bloc|date_format:"%H:%M"}">
	                <button class="tick" type="submit"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.entree_bloc item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->entree_bloc}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button class="cancel" type="submit" onclick="this.form.del.value = 1"></button>
	                {else}
	                <input type="submit" value="entrée du patient" />
	                {/if}
	              </form>
	            </td>
	            <td>
	              <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
	                <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="pose_garrot" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->pose_garrot}
	                Pose du garrot:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->pose_garrot|date_format:"%H:%M"}">
	                <button class="tick" type="submit"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.pose_garrot item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->pose_garrot}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button class="cancel" type="submit" onclick="this.form.del.value = 1"></button>
	                {else}
	                <input type="submit" value="pose du garrot" />
	                {/if}
	              </form>
	            </td>
	            <td>
	              <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
	                <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="debut_op" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->debut_op}
	                Début d'intervention:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->debut_op|date_format:"%H:%M"}">
	                <button class="tick" type="submit"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.debut_op item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->debut_op}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button class="cancel" type="submit" onclick="this.form.del.value = 1"></button>
	                {else}
	                <input type="submit" value="début d'intervention" />
	                {/if}
	              </form>
	            </td>
	          </tr>
	        </table>
          </td>
        </tr>
        <tr>
          <th>Actes</th>
          <td class="text">
          {include file="inc_manage_codes.tpl"}
          </td>
        </tr>
        <tr>
          <th>
            Intervention
            <br />
            Côté {$selOp->cote}
            <br />
            ({$selOp->temp_operation|date_format:"%Hh%M"})
          </th>
          <td class="text">
          {include file="inc_codage_actes.tpl"}
          </td>
        </tr>
        <tr>
          <th>Anesthésie</th>
          <td>
            <form name="editAnesth" action="index.php" method="get">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="a" value="do_set_hours" />
            <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
            <select name="anesth" onchange="this.form.submit()">
              <option value="null">&mdash; Type d'anesthésie</option>
              {foreach from=$listAnesthType item=curr_type}
              <option {if $selOp->_lu_type_anesth == $curr_type} selected="selected" {/if}>{$curr_type}</option>
              {/foreach}
            </select>
            </form>
          </td>
        </tr>
        {if $selOp->materiel}
        <tr>
          <th>Matériel</th>
          <td><strong>{$selOp->materiel|nl2br}</strong></td>
        </tr>
        {/if}
        {if $selOp->rques}
        <tr>
          <th>Remarques</th>
          <td>{$selOp->rques|nl2br}</td>
        </tr>
        {/if}
        <tr>
          <th>Horaires fin</th>
          <td>
            <table class="form">
              <tr>
                <td>
	              <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
	                <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="fin_op" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->fin_op}
	                Fin d'intervention:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->fin_op|date_format:"%H:%M"}">
	                <button type="submit" class="tick"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.fin_op item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->fin_op}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button type="submit" onclick="this.form.del.value = 1" class="cancel"></button>
	                {else}
	                <input type="submit" value="fin d'intervention" />
	                {/if}
	              </form>
	            </td>
	            <td>
                  <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
	                <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="retrait_garrot" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->retrait_garrot}
	                Retrait du garrot:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->retrait_garrot|date_format:"%H:%M"}">
	                <button type="submit" class="tick"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.retrait_garrot item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->retrait_garrot}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button class="cancel" type="submit" onclick="this.form.del.value = 1"></button>
	                {else}
	                <input type="submit" value="retrait du garrot" />
	                {/if}
	              </form>
	            </td>
	            <td>
                  <form name="editFrm{$selOp->operation_id}" action="index.php" method="get">
                    <input type="hidden" name="m" value="dPsalleOp" />
	                <input type="hidden" name="a" value="do_set_hours" />
	                <input type="hidden" name="operation_id" value="{$selOp->operation_id}" />
	                <input type="hidden" name="type" value="sortie_bloc" />
	                <input type="hidden" name="del" value="0" />
	                {if $selOp->sortie_bloc}
	                Sortie du patient:
	                {if $canEdit}
	                <input name="hour" size="5" type="text" value="{$selOp->sortie_bloc|date_format:"%H:%M"}">
	                <button type="submit" class="tick"></button>
	                {else}
	                <select name="hour" onchange="this.form.submit()">
	                  {foreach from=$timing.sortie_bloc item=curr_time}
	                  <option value="{$curr_time}" {if $curr_time == $selOp->sortie_bloc}selected="selected"{/if}>
	                    {$curr_time|date_format:"%Hh%M"}
	                  </option>
	                  {/foreach}
	                </select>
	                {/if}
	                <button type="submit" onclick="this.form.del.value = 1" class="cancel"></button>
	                {else}
	                <input type="submit" value="sortie du patient" />
	                {/if}
	              </form>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        {else}
        <tr>
          <th class="title">
            Selectionnez une opération
          </th>
        </tr>
        {/if}
      </table>
    </td>
  </tr>
</table>