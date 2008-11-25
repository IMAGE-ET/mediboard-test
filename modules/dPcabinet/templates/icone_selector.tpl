<script type="text/javascript">
function setClose(icone) {
  var oSelector = window.opener.IconeSelector;
  oSelector.set(icone);
  window.close();
}
</script>

<h2>Icones disponibles</h2>
<div>
  {{foreach from=$icones item="icone"}}
    <a href="#"><img src="./modules/dPcabinet/images/categories/{{$icone}}" onclick="setClose('{{$icone}}')" alt="" /></a>
  {{/foreach}}
</div>