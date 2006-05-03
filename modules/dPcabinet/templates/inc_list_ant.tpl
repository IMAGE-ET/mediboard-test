      <strong>Diagnostics du patient</strong>
      <ul>
        {foreach from=$patient->_codes_cim10 item=curr_code}
        <li>
          <button type="button" onclick="delCim10('{$curr_code->code}')">
            <img src="modules/dPcabinet/images/cross.png" />
          </button>
          {$curr_code->code}: {$curr_code->libelle}
        </li>
        {foreachelse}
        <li>Pas de diagnostic</li>
        {/foreach}
      </ul>
      <strong>Antécédents du patient</strong>
      <ul>
        {foreach from=$patient->_ref_antecedents item=curr_ant}
        <li>
          <form name="delAntFrm" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="dosql" value="do_antecedent_aed" />
          <input type="hidden" name="antecedent_id" value="{$curr_ant->antecedent_id}" />
          <button type="button" onclick="submitAnt(this.form)">
            <img src="modules/dPcabinet/images/cross.png" />
          </button>
          {$curr_ant->type} le {$curr_ant->date|date_format:"%d/%m/%Y"} :
          <i>{$curr_ant->rques}</i>
          </form>
        </li>
        {foreachelse}
        <li>Pas d'antécédents</li>
        {/foreach}
      </ul>
      <strong>Traitements du patient</strong>
      <ul>
        {foreach from=$patient->_ref_traitements item=curr_trmt}
        <li>
          <form name="delTrmtFrm" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="dosql" value="do_traitement_aed" />
          <input type="hidden" name="traitement_id" value="{$curr_trmt->traitement_id}" />
          <button type="button" onclick="submitAnt(this.form)">
            <img src="modules/dPcabinet/images/cross.png" />
          </button>
          {if $curr_trmt->fin}
            Du {$curr_trmt->debut|date_format:"%d/%m/%Y"} au {$curr_trmt->fin|date_format:"%d/%m/%Y"}
          {else}
            Depuis le {$curr_trmt->debut|date_format:"%d/%m/%Y"}
          {/if}
          : <i>{$curr_trmt->traitement}</i>
          </form>
        </li>
        {foreachelse}
        <li>Pas de traitements</li>
        {/foreach}
      </ul>