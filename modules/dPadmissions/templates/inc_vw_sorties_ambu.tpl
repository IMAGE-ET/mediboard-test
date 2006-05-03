      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Sortie ambu</th>
        </tr>
        <tr>
          <th>Effectuer la sortie</th>
          <th>Patient</th>
          <th>Sortie prévue</th>
          <th>Praticien</th>
          <th>Chambre</th>
        </tr>
        {foreach from=$listAmbu item=curr_sortie}
        <tr>
          <td>
            <form name="editFrm{$curr_sortie->affectation_id}" action="?m={$m}" method="post">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="affectation_id" value="{$curr_sortie->affectation_id}" />
            {if $curr_sortie->effectue}
            <input type="hidden" name="effectue" value="0" />
            <button type="button" onclick="submitAmbu(this.form)">
              <img src="modules/{$m}/images/cross.png" alt="Annuler" title="Annuler la sortie" />
              Annuler la sortie
            </button>
            {else}
            <input type="hidden" name="effectue" value="1" />
            <button type="button" onclick="submitAmbu(this.form)">
              <img src="modules/{$m}/images/tick.png" alt="Confirmer" title="Effectuer la sortie" />
              Effectuer la sortie
            </button>
            {/if}
            </form>
          </td>
          <td>
            <a name="sortie{$curr_sortie->affectation_id}"><b>{$curr_sortie->_ref_operation->_ref_pat->_view}</b></a>
          </td>
          <td>{$curr_sortie->sortie|date_format:"%H h %M"}</td>
          <td class="text">Dr. {$curr_sortie->_ref_operation->_ref_chir->_view}</td>
          <td class="text">{$curr_sortie->_ref_lit->_ref_chambre->_ref_service->nom} - {$curr_sortie->_ref_lit->_ref_chambre->nom} - {$curr_sortie->_ref_lit->nom}</td>
        </tr>
        {/foreach}
      </table>