<script>
  savePref = function(form) {
    var formPref = getForm('editPrefService');
    var services_ids_hospi_elt = formPref.elements['pref[services_ids_hospi]'];
    
    var services_ids_hospi = $V(services_ids_hospi_elt).evalJSON();
    
    services_ids_hospi.g{{$group_id}} = $V(form.select("input[class=service]:checked"));
    
    if (services_ids_hospi.g{{$group_id}} == null) {
      services_ids_hospi.g{{$group_id}} = "";
    }
    else {
      services_ids_hospi.g{{$group_id}} = services_ids_hospi.g{{$group_id}}.join("|");
    }
    
    $V(services_ids_hospi_elt, Object.toJSON(services_ids_hospi));
    return onSubmitFormAjax(formPref, function() {
      {{if $ajax_request}}
        form.onsubmit();
      {{else}}
        form.submit();
      {{/if}}
      Control.Modal.close();
    });
  };

  checked_radio = false;
  toggleChecked = function() {
    var form = getForm("selectServices");
    form.select("input[type=checkbox]").each(function(elt) {
      elt.checked = checked_radio;
    });
    checked_radio = !checked_radio;
  };
  
  changeServicesScission = function() {
    var form = getForm("searchLit");
    var formPref = getForm('selectServices');
    var services_ids_suggest = $V(formPref.select("input[class=service]:checked"));
    $V(form.services_ids_suggest, $A(services_ids_suggest).join(','));
    form.onsubmit();
    Control.Modal.close();
  };
  
  Main.add(function() {
    Control.Modal.stack.last().position();
  });
</script>

<!-- Formulaire de sauvegarde des services en préférence utilisateur -->
<form name="editPrefService" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="pref[services_ids_hospi]" value="{{$services_ids_hospi}}" />
</form>

{{math equation=x+1 x=$secteurs|@count assign=colspan}}

<div style="overflow-x: auto;">
  <form name="selectServices" method="get" {{if $ajax_request}}onsubmit="return onSubmitFormAjax(this, null, '{{$view}}')"{{/if}}>
    {{if $view == "listSorties"}}
      <input type="hidden" name="m" value="pmsi" />
    {{elseif in_array($view, array("listAdmissions", "sortie", "listPresents"))}}
      <input type="hidden" name="m" value="admissions" />
    {{else}}
      <input type="hidden" name="m" value="hospi" />
    {{/if}}
    {{if $view == "mouvements"}}
      <input type="hidden" name="tab" value="edit_sorties" />
    {{elseif $view == "tableau"}}
      <input type="hidden" name="a" value="vw_affectations" />
    {{elseif $view == "topologique"}}
      <input type="hidden" name="a" value="vw_placement_patients" />
    {{elseif $view == "etat_lits"}}
      <input type="hidden" name="tab" value="vw_recherche" />
    {{elseif $view == "listSorties"}}
      <input type="hidden" name="a" value="httpreq_vw_sorties" />
    {{elseif $view == "listAdmissions"}}
      <input type="hidden" name="tab" value="vw_idx_admission" />
    {{elseif $view == "sortie"}}
      <input type="hidden" name="tab" value="vw_idx_sortie" />
    {{elseif $view == "listPresents"}}
      <input type="hidden" name="tab" value="vw_idx_present" />
    {{else}}
      <input type="hidden" name="a" value="vw_mouvements" />
    {{/if}}
    <input type="hidden" name="services_ids[]" value="" />
    <table class="tbl">
      <tr>
        <th colspan="{{$colspan}}">
          <button type="button" style="float: left;" class="tick notext" onclick="toggleChecked()" title="Tout cocher / décocher"></button>
          {{tr}}CService-title-selection{{/tr}}
        </th>
      </tr>
      <tr>
        {{assign var=i value=0}}
        {{foreach from=$secteurs item=_secteur}}
          {{if $i == 6}}
            {{assign var=i value=0}}
            </tr>
            <tr>
          {{/if}}
          <td style="vertical-align: top;">
            <label>
              <input type="checkbox" name="_secteur_{{$_secteur->_id}}" {{if $_secteur->_all_checked}}checked{{/if}}
                onclick="$$('.secteur_{{$_secteur->_id}}').each(function(elt) { elt.down('input').checked=this.checked ? 'checked' : ''; }.bind(this))" />
              <strong>{{mb_value object=$_secteur field=nom}}</strong>
            </label>
            {{foreach from=$_secteur->_ref_services item=_service}}
              <p class="secteur_{{$_secteur->_id}}">
                <label>
                  <input style="margin-left: 1em;" type="checkbox" name="services_ids[{{$_service->_id}}]" value="{{$_service->_id}}"
                    {{if !in_array($_service->_id, array_keys($services_allowed))}}disabled{{/if}} class="service"
                    {{if $services_ids && in_array($_service->_id, $services_ids)}}checked{{/if}}/> {{$_service}}
                </label>
              </p>
            {{/foreach}}
          </td>
          {{math equation=x+1 x=$i assign=i}}
        {{/foreach}}
        <td style="vertical-align: top;" colspan="{{math equation=x-y x=$secteurs|@count y=$i}}">
          <strong>Hors secteur</strong>
          {{foreach from=$all_services item=_service}}
            <p>
              <label>
                <input type="checkbox" name="services_ids[{{$_service->_id}}]" value="{{$_service->_id}}" class="service"
                  {{if !in_array($_service->_id, array_keys($services_allowed))}}disabled{{/if}}
                  {{if $services_ids && in_array($_service->_id, $services_ids)}}checked{{/if}}/> {{$_service}}
              </label>
            </p>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <td class="button" colspan="{{$colspan}}">
          {{if $view == "cut"}}
            <button type="button" class="tick" onclick="changeServicesScission();">{{tr}}Validate{{/tr}}</button>
          {{else}}
            <button {{if $ajax_request}}type="button" onclick="Control.Modal.close(); this.form.onsubmit();"{{/if}} class="tick">{{tr}}Validate{{/tr}}</button>
            <button type="button" class="save" onclick="savePref(form);">
              {{tr}}Validate{{/tr}} {{tr}}and{{/tr}} {{tr}}Save{{/tr}}
            </button>
          {{/if}}
          <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>