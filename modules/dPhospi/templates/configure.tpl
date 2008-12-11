<script type="text/javascript">
function synchronizeSejours() {
  var url = new Url();
  url.setModuleAction("dPhospi", "httpreq_do_synchronize_sejours");
  url.addElement(document.synchronizeFrm.dateMin);
  url.requestUpdate("synchronize");
}
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  <tr>
    <th class="category" colspan="2">Prise en compte des pathologies</th>
  </tr>
  
  <tr>
    <th>
      <label for="{{$m}}[pathologies]" title="{{tr}}config-{{$m}}-pathologies{{/tr}}">
        {{tr}}config-{{$m}}-pathologies{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[pathologies]">
        <option value="1" {{if $dPconfig.$m.pathologies == 1}}selected="selected"{{/if}}>
          Oui
        </option>
        <option value="0" {{if $dPconfig.$m.pathologies == 0}}selected="selected"{{/if}}>
          Non
        </option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>

<form name="synchronizeFrm">
<table class="form">
  <tr>
    <th colspan="2" class="title">
      Synchronisation des dates de sortie des séjours et des affectations
    </th>
  </tr>
  <tr>
    <td>
      Date minimale de sortie : <input type="text" name="dateMin" value="AAAA-MM-JJ" />
      <br />
      <button type="button" class="tick" onclick="synchronizeSejours()">Synchroniser</button>
    </td>
    <td id="synchronize"></td>
  </tr>
</table>
</form>