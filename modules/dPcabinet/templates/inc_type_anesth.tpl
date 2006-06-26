      <table class="form">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>
        <tr>
          {if $consult_anesth->consultation_anesth_id}
          <td class="text">
            Intervention le <strong>{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}</strong>
            par le <strong>Dr. {$consult_anesth->_ref_operation->_ref_chir->_view}</strong><br />
            <ul>
              {foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}
              <li><em>{$curr_code->libelleLong}</em> ({$curr_code->code})</li>
              {/foreach}
            </ul>
          </td>
          <td class="text">
            <form name="editOpFrm" action="?m=dPcabinet" method="post">

            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{$consult_anesth->_ref_operation->operation_id}" />
            <label for="type_anesth" title="Type d'anesthésie pour l'intervention">Type d'anesthésie</label>
            <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg')">
              <option value="">&mdash; Choisir un type d'anesthésie</option>
              {html_options options=$anesth selected=$consult_anesth->_ref_operation->type_anesth}
            </select>

            </form>
          </td>
          {else}
          <td colspan="2" class="text">
            Vous devez d'abord séléctionner une intervention pour ce patient
          </td>
          {/if}
        </tr>
      </table>