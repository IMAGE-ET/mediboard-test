<script type="text/javascript">
function setClose(code, type) {
  if (type == "ccam") {
    window.opener.setCodeCCAM(code, type);
  }
  if (type == "cim10") {
    window.opener.setCode(code, type);
  }
  window.close();
}

function createFavori() {
  var sType = "{{$type}}";
  var sModule = sType == "ccam" ? "dPccam" : "dPcim10";

  var url = new Url;
  url.setModuleAction(sModule, "vw_find_code");
  url.addParam("dialog", 1);
  url.redirect();
}

</script>

<table class="selectCode">
  <tr>
  	<th>Favoris disponibles</th>
  </tr>
  
  {{if !$list}}
  <tr>
  	<td>{{tr}}CFavoriCCAM.none{{/tr}}</td>
  </tr>
  {{/if}}

  <tr>
  {{foreach from=$list item=curr_code key=curr_key}}
    <td>
      <strong>{{$curr_code->code}}</strong>
      <br />
      {{$curr_code->libelleLong}}
      <br />
      <button class="tick" type="button" onclick="setClose('{{$curr_code->code}}', '{{$type}}')">
        {{tr}}Select{{/tr}}
      </button>
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
      <button class="cancel" type="button" onclick="window.close();">{{tr}}Cancel{{/tr}}</button>
      <button class="search" type="button" onclick="createFavori();">{{tr}}button-CCodeCCAM-searchAnother{{/tr}}</button>
    </td>
  </tr>
</table>