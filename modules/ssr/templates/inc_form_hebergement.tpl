<script type="text/javascript">
</script>


<form name="editSejourHebergement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />

  {{mb_key object=$sejour}}
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$sejour field="recuse"}}</th>
      <td>{{mb_field object=$sejour field="recuse"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
      <td>{{mb_field object=$sejour field="chambre_seule"}}</td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$sejour field="service_id"}}
      </th>
      <td colspan="3">
        <select name="service_id" class="{{$sejour->_props.service_id}}" style="width: 15em">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
            {{$_service->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field=rques}}</th>
      <td>
        {{mb_field object=$sejour field=rques}}
        <script type="text/javascript">
          Main.add(function() {
            new AideSaisie.AutoComplete(getForm("editSejourHebergement").elements.rques, {
              objectClass: "{{$sejour->_class}}",
              contextUserId: "{{$app->user_id}}"
            });
          });
        </script>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="6">
        <button class="submit" type="submit">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>