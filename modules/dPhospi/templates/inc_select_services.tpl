<script type="text/javascript">
  savePref = function(form) {
    var formPref = getForm('editPrefService');
    var services_ids_hospi_elt = formPref.elements['pref[services_ids_hospi]'];
    
    var services_ids_hospi = $V(services_ids_hospi_elt).evalJSON();
    
    services_ids_hospi.g{{$group_id}} = $V(form.select("input[checked]"));
    console.log(services_ids_hospi.g{{$group_id}});
    if (services_ids_hospi.g{{$group_id}} == null) {
      services_ids_hospi.g{{$group_id}} = "";
    }
    else {
      services_ids_hospi.g{{$group_id}} = services_ids_hospi.g{{$group_id}}.join("|");
    }
    
    $V(services_ids_hospi_elt, Object.toJSON(services_ids_hospi));
    return onSubmitFormAjax(formPref, {onComplete: function() {
      form.onsubmit();
      Control.Modal.close();
    } });
  }
  checked_radio = false;
  toggleChecked = function() {
    var form = getForm("selectServices");
    form.select("input[type=checkbox]").each(function(elt) {
      elt.checked = checked_radio;
    });
    checked_radio = !checked_radio;
  }
</script>

<!-- Formulaire de sauvegarde des services en préférence utilisateur -->
<form name="editPrefService" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="pref[services_ids_hospi]" value="{{$services_ids_hospi}}" />
</form>
<div style="overflow-x: auto;">
  <form name="selectServices" method="get" onsubmit="return onSubmitFormAjax(this, null, '{{$view}}')">
    <input type="hidden" name="m" value="dPhospi" />
    {{if $view == "tableau"}}
      <input type="hidden" name="a" value="vw_affectations" />
    {{else}}
      <input type="hidden" name="a" value="vw_mouvements" />
    {{/if}}
    <table class="tbl">
      <tr>
        <th>
          <button type="button" style="float: left;" class="cancel notext" onclick="toggleChecked()" title="Tout cocher / décocher"></button>
          {{tr}}CService-title-selection{{/tr}}
        </th>
      </tr>
      {{foreach from=$all_services item=_service}}
        <tr>
          <td>
            <label>
              <input type="checkbox" name="services_ids[{{$_service->_id}}]" value="{{$_service->_id}}"
                {{if !in_array($_service->_id, array_keys($services_allowed))}}disabled="disabled"{{/if}}
                {{if $services_ids && in_array($_service->_id, $services_ids)}}checked="checked"{{/if}}/> {{$_service}}
            </label>
          </td>
        </tr>
      {{/foreach}}
      <tr>
        <td class="button">
          <button type="button" class="tick"
            onclick="Control.Modal.close(); this.form.onsubmit();">{{tr}}Validate{{/tr}}</button>
          <button type="button" class="save" onclick="savePref(form);">
            {{tr}}Validate{{/tr}} {{tr}}and{{/tr}} {{tr}}Save{{/tr}}
          </button>
          <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>