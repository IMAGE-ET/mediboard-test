<tr>
  <th>{{mb_label object=$object field="ean"}}</th>
  <td>{{mb_field object=$object field="ean"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$object field="ean_base"}}</th>
  <td>{{mb_field object=$object field="ean_base"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$object field="rcc"}}</th>
  <td>{{mb_field object=$object field="rcc"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$object field="adherent"}}</th>
  <td>{{mb_field object=$object field="adherent"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$object field="debut_bvr"}}</th>
  <td>{{mb_field object=$object field="debut_bvr"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$object field="banque_id"}}</th>
  <td>
    <select name="banque_id" style="width: 150px;">
      <option value="">&mdash; Choix d'une banque</option>
      {{if is_array($banques)}}
        {{foreach from=$banques item="banque"}}
          <option value="{{$banque->_id}}" {{if $object->banque_id == $banque->_id}}selected = "selected"{{/if}}>
            {{$banque->_view}}
          </option>
        {{/foreach}}
      {{/if}}
    </select>
  </td>
</tr>
<tr>
  <th>{{mb_label object=$object field="electronic_bill"}}</th>
  <td>{{mb_field object=$object field="electronic_bill"}}</td>
</tr>

{{if $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
  <script>
    Main.add(function () {
      var form = getForm("{{$name_form}}");
      var url = new Url("tarmed", "ajax_specialite_autocomplete");
      url.autoComplete(form.specialite_tarmed, null, {
        minChars: 0,
        dropdown: true,
        select: "newspec",
        updateElement: function(selected) {
          $V(form.specialite_tarmed, selected.down(".newspec").getText(), false);
        }
      });
    });
  </script>
  <tr>
    <th>{{mb_label object=$object field="specialite_tarmed"}}</th>
    <td>{{mb_field object=$object field="specialite_tarmed" style="width:200px;"}}</td>
  </tr>
{{/if}}

<tr>
  <th>{{mb_label object=$object field="place_tarmed"}}</th>
  <td>
    <select name="place_tarmed" style="width: 150px;">
      <option value="">&mdash; Choix d'une place</option>
      {{if @$modules.tarmed->_can->read}}
        {{foreach from="CTarmed::getPlacesTarmed"|static_call:null item=_place_tarmed}}
          <option value="{{$_place_tarmed}}" {{if $object->place_tarmed == $_place_tarmed}}selected = "selected"{{/if}}>
            {{tr}}CTarmed.{{$_place_tarmed}}{{/tr}}
          </option>
        {{/foreach}}
      {{/if}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="role_tarmed"}}</th>
  <td>
    <select name="role_tarmed" style="width: 150px;">
      <option value="">&mdash; Choix d'un rôle</option>
      {{if @$modules.tarmed->_can->read}}
        {{foreach from="CTarmed::getRolesTarmed"|static_call:null item=_role_tarmed}}
          <option value="{{$_role_tarmed}}" {{if $object->role_tarmed == $_role_tarmed}}selected = "selected"{{/if}}>
            {{tr}}CTarmed.{{$_role_tarmed}}{{/tr}}
          </option>
        {{/foreach}}
      {{/if}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field="reminder_text"}}</th>
  <td>{{mb_field object=$object field="reminder_text"}}</td>
</tr>

{{mb_script module=system script=exchange_source ajax=true}}
<script>
  loadArchives = function() {
    var url = new Url("mediusers", "ajax_edit_source_mediuser");
    url.addParam("user_id", '{{$object->_id}}');
    url.requestUpdate('sources');
  };

  Main.add(function () {
    loadArchives();
  });
</script>