<script type="text/javascript">
function setClose(icone) {
  var oSelector = window.opener.IconeSelector;
  oSelector.set(icone);
  window.close();
}
</script>


<table class="main">
  <tr>
    <th class="title">Icones disponibles</th>
  </tr>
  <tr>
    <td>
      {{foreach from=$icones item="icone"}}
        <a href="#"><img src="./modules/dPcabinet/categories/{{$icone}}" onclick="setClose('{{$icone}}')" alt="" /></a>
      {{/foreach}}
    </td>
  </tr>
</table>