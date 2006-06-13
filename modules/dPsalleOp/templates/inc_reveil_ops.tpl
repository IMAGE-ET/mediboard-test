{literal}
<script type="text/javascript">

{/literal}
regRedirectPopupCal("{$date}", "index.php?m={$m}&tab=vw_reveil&date=");
{literal}

</script>
{/literal}

      <form action="index.php" name="selection" method="get">
      <input type="hidden" name="m" value="{$m}" />
      <table class="form">
        <tr>
          <th class="category">{$listOps|@count} patients en attente</th>
          <th class="category" colspan="2">
            <div style="float: right;">{$hour|date_format:"%Hh%M"}</div>
            {$date|date_format:"%A %d %B %Y"}
            <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
      </table>
      </form>

      <table class="tbl">
        <tr>
          <th>Salle</th>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Sortie Salle</th>
          <th>Entr�e reveil</th>
        </tr>    
        {foreach from=$listOps item=curr_op}
        <tr>
          <td>{$curr_op->_ref_plageop->_ref_salle->nom}</td>
          <td class="text">Dr. {$curr_op->_ref_chir->_view}</td>
          <td class="text">{$curr_op->_ref_sejour->_ref_patient->_view}</td>
          <td class="button">
            {if $canEdit}
	        <form name="editFrm{$curr_op->operation_id}" action="index.php" method="get">
	          <input type="hidden" name="m" value="dPsalleOp" />
	          <input type="hidden" name="a" value="do_set_hours" />
	          <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
	          <input type="hidden" name="type" value="sortie_bloc" />
	          <input type="hidden" name="del" value="0" />
	          <input name="hour" size="5" type="text" value="{$curr_op->sortie_bloc|date_format:"%H:%M"}">
	          <button type="submit"><img src="modules/{$m}/images/tick.png" /></button>
	        </form>
            {else}
            {$curr_op->sortie_bloc|date_format:"%Hh%M"}
            {/if}
          </td>
          <td class="button">
            <form name="editFrm{$curr_op->operation_id}" action="index.php" method="get">
              <input type="hidden" name="m" value="dPsalleOp" />
              <input type="hidden" name="a" value="do_set_hours" />
              <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
              <input type="hidden" name="type" value="entree_reveil" />
              <input type="hidden" name="del" value="0" />
              <button type="submit">
                <img src="modules/{$m}/images/tick.png" alt="valider" />
              </button>
            </form>
          </td>
        </tr>
        {/foreach}
      </table>