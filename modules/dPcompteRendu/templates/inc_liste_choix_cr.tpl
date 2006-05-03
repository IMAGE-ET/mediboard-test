      {if $lists|@count}
      <ul>
        {foreach from=$lists item=curr_list}
        <li>
          <select name="_liste{$curr_list->liste_choix_id}">
            <option value="undef">&mdash; {$curr_list->nom} &mdash;</option>
            {foreach from=$curr_list->_valeurs item=curr_valeur}
            <option>{$curr_valeur}</option>
            {/foreach}
          </select>
        </li>
        {/foreach}
        <li>
          <button type="submit"><img src="modules/{$m}/images/tick.png" /></button>
        </li>
      </ul>
      {/if}