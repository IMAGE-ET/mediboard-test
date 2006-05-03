{literal}
<script type="text/javascript">
function setClose(code, type) {
  window.opener.setCode(code, type);
  window.close();
}
</script>
{/literal}

<table class="selectCode">
  <tr>
  	<th>Favoris disponibles</th>
  </tr>
  
  {if !$list}
  <tr>
  	<td>Aucun favori disponible</td>
  </tr>
  {/if}

  <tr>
  {foreach from=$list item=curr_code key=curr_key}
    <td>
      <strong>{$curr_code->code}</strong><br />
      {$curr_code->libelleLong}<br />
      <input type="button" class="button" value="selectionner" onclick="setClose('{$curr_code->code}', '{$type}')" />
    </td>
  {if ($curr_key+1) is div by 3}
  </tr><tr>
  {/if}
  {/foreach}
  </tr>
</table>

<table class="form">
  <tr>
    <td class="button" colspan="3">
      <input type="button" value="annuler" onclick="window.close()" />
    </td>
  </tr>
</table>