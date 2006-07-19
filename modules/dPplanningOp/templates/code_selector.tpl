<script type="text/javascript">
function setClose(code, type) {
  window.opener.setCode(code, type);
  window.close();
}
</script>

<table class="selectCode">
  <tr>
  	<th>Favoris disponibles</th>
  </tr>
  
  {{if !$list}}
  <tr>
  	<td>Aucun favori disponible</td>
  </tr>
  {{/if}}

  <tr>
  {{foreach from=$list item=curr_code key=curr_key}}
    <td>
      <strong>{{$curr_code->code}}</strong><br />
      {{$curr_code->libelleLong}}<br />
      <button type="button" onclick="setClose('{{$curr_code->code}}', '{{$type}}')">selectionner</button>
    </td>
  {{if ($curr_key+1) is div by 3}}
  </tr><tr>
  {{/if}}
  {{/foreach}}
  </tr>
</table>

<table class="form">
  <tr>
    <td class="button" colspan="3">
      <button class="cancel" type="button" onclick="window.close()">Annuler</button>
    </td>
  </tr>
</table>