<script>
  Main.add(function() {
    {{if $affectation->_id && $affectation->sejour_id}}
      var form = getForm("editAffect");
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CLit");
      url.addParam("field", "lit_id");
      url.addParam("input_field", "_lit_view");
      url.addParam("show_view", "true");
      url.addParam("where[lit.annule]", "0");
      url.autoComplete(form.elements._lit_view, null, {
        minChars: 2,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field, selected){
          var value = selected.id.split('-')[2];
          $V(form.lit_id, value);
        },
        callback: function(input, queryString) {
          var service_id = $V(form.service_id);
          if (service_id) {
            queryString += "&whereComplex[chambre.service_id]= IN ("+ JSON.parse(Preferences.services_ids_hospi.g{{$g}}).replace(/\|/g, ",")  + ")";
            queryString += "&ljoin[chambre]=chambre.chambre_id=lit.chambre_id";
          }
          return queryString;
        }
      });
    {{/if}}
  });
</script>

<form name="editAffect" method="post"
      onsubmit="return onSubmitFormAjax(this, (function() {
        Control.Modal.close();
        if (window.refreshMouvements) {
          if ((this._lock_all_lits && this._lock_all_lits.checked) || (this._lock_all_lits_urgences && this._lock_all_lits_urgences.checked)) {
            refreshMouvements(window.loadNonPlaces);
          }
          else {
            var lit_id = $V(this.lit_id);
            if (lit_id && lit_id != '{{$affectation->lit_id}}') {
              refreshMouvements(null, lit_id);
            }
            refreshMouvements(window.loadNonPlaces, '{{$affectation->lit_id}}');
          }
        }
      }).bind(this));">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$affectation}}
  {{mb_field object=$affectation field=lit_id hidden=true}}

  <table class="form">
    <tr>
      {{if $affectation->_id}}
        <th class="title" colspan="4">
          {{if $affectation->sejour_id}}
            {{$affectation->_ref_sejour->_ref_patient}}
          {{else}}
            Lit bloqué
          {{/if}}
        </th>
      {{/if}}
    </tr>
    {{if !$affectation->_id}}
      {{if $urgence}}
      <input type="hidden" name="function_id" value="{{$affectation->function_id}}"/>
        <tr>
          <th><input type="checkbox" name="_lock_all_lits_urgences" value="1"/></th>
          <td colspan="3">Mise à disposition de  tous les lits du service {{$lit->_ref_chambre->_ref_service->nom}} pour les urgences</td>
        </tr>
      {{else}}
        <tr>
          <th><input type="checkbox" name="_lock_all_lits" value="1"/></th>
          <td colspan="3">Bloquer tous les lits du service {{$lit->_ref_chambre->_ref_service->nom}}</td>
        </tr>
      {{/if}}
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$affectation field=entree}}
      </th>
      <td>
        {{mb_field object=$affectation field=entree form=editAffect register=true}}
      </td>
      <th>
        {{mb_label object=$affectation field=sortie}}
      </th>
      <td>
        {{mb_field object=$affectation field=sortie form=editAffect register=true}}
      </td>
    </tr>
    {{if $affectation->_id && $affectation->sejour_id}}
      <tr>
        <th>
          {{mb_label object=$affectation field=lit_id}}
        </th>
        <td colspan="3">
          <input type="text" name="_lit_view" value="{{$lit}}"/>
        </td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="4" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{if $affectation->_id}}
            {{tr}}Save{{/tr}}
          {{else}}
            {{tr}}Create{{/tr}}
          {{/if}}
        {{if $affectation->_id}}
          <button type="button" class="cancel" onclick="$V(this.form.del, 1); this.form.onsubmit();">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{if $affectation->_id && $affectation->sejour_id}}
  {{mb_include module=hospi template=inc_other_actions}}
{{/if}}