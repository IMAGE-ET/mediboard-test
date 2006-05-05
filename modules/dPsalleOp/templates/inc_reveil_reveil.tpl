      <table class="form">
        <tr>
          <th class="category">{$listReveil|@count} patients en salle de reveil</th>
        </tr>
      </table>

      <table class="tbl">
        <tr>
          <th>Salle</th>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Chambre</th>
          <th>Sortie Salle</th>
          <th>Entrée reveil</th>
          <th>Sortie reveil</th>
        </tr>    
        {foreach from=$listReveil key=key item=curr_op}
        <tr>
          <td>{$curr_op->_ref_plageop->_ref_salle->nom}</td>
          <td class="text">Dr. {$curr_op->_ref_chir->_view}</td>
          <td class="text">{$curr_op->_ref_pat->_view}</td>
          <td class="text">
            {assign var="affectation" value=$curr_op->_ref_first_affectation}
            {if $affectation->affectation_id}
            {$affectation->_ref_lit->_ref_chambre->_ref_service->nom}
            - {$affectation->_ref_lit->_ref_chambre->nom}
            - {$affectation->_ref_lit->nom}
            {else}
            Non placé
            {/if}
          </td>
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
              {if $canEdit}
	          <input name="hour" size="5" type="text" value="{$curr_op->entree_reveil|date_format:"%H:%M"}">
	          <button type="submit"><img src="modules/{$m}/images/tick.png" /></button>
              {else}
              <select name="hour" onchange="this.form.submit()">
                {foreach from=$timing.$key.entree_reveil item=curr_time}
                <option value="{$curr_time}" {if $curr_time == $curr_op->entree_reveil}selected="selected"{/if}>
                  {$curr_time|date_format:"%Hh%M"}
                </option>
                {/foreach}
              </select>
              {/if}
              <button type="submit" onclick="this.form.del.value = 1">
                <img src="modules/{$m}/images/cross.png" alt="supprimer" />
              </button>
            </form>
          </td>
          <td class="button">
            <form name="editFrm{$curr_op->operation_id}" action="index.php" method="get">
              <input type="hidden" name="m" value="dPsalleOp" />
              <input type="hidden" name="a" value="do_set_hours" />
              <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
              <input type="hidden" name="type" value="sortie_reveil" />
              <input type="hidden" name="del" value="0" />
              <button type="submit">
                <img src="modules/{$m}/images/tick.png" alt="valider" />
              </button>
            </form>
          </td>
        </tr>
        {/foreach}
      </table>