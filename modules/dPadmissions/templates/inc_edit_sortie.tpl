{{*
 * $Id$
 *  
 * @category dPadmissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if "dPurgences"|module_active}}
  {{mb_script module=dPurgences script=contraintes_rpu ajax=true}}
{{/if}}

{{assign var=form_name value="validerSortie`$sejour->_id`"}}
{{assign var=form_rpu_name value="editRpu`$sejour->_id`"}}

{{assign var=rpu value=$sejour->_ref_rpu}}
{{assign var=atu value=$sejour->_ref_consult_atu}}

{{assign var=class_sortie_reelle value=""}}
{{assign var=class_sortie_autorise value=""}}
{{assign var=class_mode_sortie value=""}}
{{if $modify_sortie_prevue}}
  {{assign var=class_sortie_autorise value="inform-field"}}
{{else}}
  {{assign var=class_mode_sortie value="notNull"}}
  {{assign var=class_sortie_reelle value="inform-field"}}
{{/if}}

<script>
  Main.add(function () {
    ContraintesRPU.changeOrientationDestination(getForm({{$form_name}}));
  })
</script>

{{if $rpu && $rpu->_id}}
  <form name="{{$form_name}}" method="post"
      onsubmit="return ContraintesRPU.checkObligatory('{{$rpu->_id}}',
        Admissions.confirmationSortie.curry(this, {{$modify_sortie_prevue}}, '{{$sejour->sortie_prevue}}',
        function() {
          document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();}))">
{{else}}
<form name="{{$form_name}}" method="post"
      onsubmit="return Admissions.confirmationSortie(this, {{$modify_sortie_prevue}}, '{{$sejour->sortie_prevue}}',
        function() { document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();})">
{{/if}}
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="planningOp" />
  {{mb_field object=$sejour field="sejour_id" hidden=true}}
  <input type="hidden" name="view_patient" value="{{$sejour->_ref_patient->_view}}">
  <input type="hidden" name="del" value="0" />
  {{if $sejour->grossesse_id}}
    <input type="hidden" name="_sejours_enfants_ids" value="{{"|"|implode:$sejour->_sejours_enfants_ids}}" />
  {{/if}}
  <table class="form">
    <tr>
      <th>{{mb_label object=$sejour field="entree_reelle"}}</th>
      <td>{{mb_field object=$sejour field="entree_reelle"}}</td>
      <th>{{mb_label object=$sejour field="entree_prevue"}}</th>
      <td>{{mb_field object=$sejour field="entree_prevue"}}</td>
    </tr>
    <tr>
      {{if $module != "dPurgences" || ($module == "dPurgences" && $rpu && $rpu->sejour_id !== $rpu->mutation_sejour_id)}}
        <th>{{mb_label object=$sejour field="sortie_reelle"}}</th>
        <td>{{mb_field object=$sejour field="sortie_reelle" form=$form_name register=false class=$class_sortie_reelle
                      onchange="Admissions.updateLitMutation(this.form);"}}</td>
      {{else}}
        <th></th>
        <td></td>
      {{/if}}
      <th>{{mb_label object=$sejour field="sortie_prevue"}}</th>
      <td>{{mb_field object=$sejour field="sortie_prevue" form=$form_name register=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field="mode_sortie"}}</th>
      <td>
        {{assign var=mode_sortie value=$sejour->mode_sortie}}
        {{if $sejour->service_sortie_id}}
          {{assign var=mode_sortie value="mutation"}}
        {{/if}}
        {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
          {{mb_field object=$sejour field=mode_sortie hidden=true class=$class_mode_sortie onchange="ContraintesRPU.changeOrientationDestination(this.form);Admissions.changeSortie(this.form, '`$sejour->_id`')"}}
          <select name="mode_sortie_id" class="{{$sejour->_props.mode_sortie_id}}" style="width: 16em;" onchange="$V(this.form.mode_sortie, this.options[this.selectedIndex].get('mode'));">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$list_mode_sortie item=_mode}}
              <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
                {{$_mode}}
              </option>
            {{/foreach}}
          </select>
        {{elseif "CAppUI::conf"|static_call:"dPurgences CRPU impose_create_sejour_mutation":"CGroups-$g"}}
          <select name="mode_sortie" class="{{$class_mode_sortie}}" onchange="ContraintesRPU.changeOrientationDestination(this.form);Admissions.changeSortie(this.form, '{{$sejour->_id}}')"">
            {{foreach from=$sejour->_specs.mode_sortie->_list item=_mode}}
              <option value="{{$_mode}}" {{if $sejour->mode_sortie == $_mode}}selected{{/if}}
                {{if $_mode == "mutation"}}{{if $rpu->mutation_sejour_id}}selected{{else}}disabled{{/if}}{{/if}}>
                {{tr}}CSejour.mode_sortie.{{$_mode}}{{/tr}}
              </option>
            {{/foreach}}
          </select>
        {{else}}
          {{if $rpu->mutation_sejour_id}}
            {{assign var=mode_sortie value="mutation"}}
          {{/if}}
          {{mb_field object=$sejour field="mode_sortie" class=$class_mode_sortie value=$mode_sortie onchange="ContraintesRPU.changeOrientationDestination(this.form);Admissions.changeSortie(this.form, '`$sejour->_id`')" value=$mode_sortie}}
        {{/if}}
        {{if !$rpu->mutation_sejour_id}}
          <input type="hidden" name="group_id" value="{{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}" />
        {{else}}
          <strong>
            <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
              Hospitalisation dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
            </a>
          </strong>
        {{/if}}
      </td>
      <th style="width: 100px">{{mb_label object=$sejour field="confirme"}}</th>
      {{if $module == "dPurgences"}}
        <td>{{mb_value object=$rpu field="sortie_autorisee"}}</td>
      {{else}}
        <td>{{mb_field object=$sejour field="confirme" register=$modify_sortie_prevue form=$form_name class=$class_sortie_autorise}}</td>
      {{/if}}
    </tr>
    <tr id="sortie_transfert_{{$sejour->_id}}" {{if $sejour->mode_sortie != "transfert"}} style="display:none;" {{/if}}>
      <th>{{mb_label object=$sejour field="etablissement_sortie_id"}}</th>
      <td colspan="3">{{mb_field object=$sejour field="etablissement_sortie_id" form=$form_name autocomplete="true,1,50,true,true"}}</td>
    </tr>

    <tbody id="lit_sortie_mutation_{{$sejour->_id}}" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
      {{if $conf.dPurgences.use_blocage_lit}}
        <script>
          Main.add(
            function () {
              if (App.m == "dPurgences") {
                Admissions.updateLitMutation(getForm({{$form_name}}));
              }
            }
          )
        </script>
      {{/if}}
    </tbody>

    <tr id="sortie_service_mutation_{{$sejour->_id}}" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
      <th>{{mb_label object=$sejour field="service_sortie_id"}}</th>
      <td colspan="3">
        <input type="hidden" name="service_sortie_id" value="{{$sejour->service_sortie_id}}"
               class="autocomplete" size="25"  />
        <input type="text" name="service_sortie_id_autocomplete_view" value="{{$sejour->_ref_service_mutation}}"
               class="autocomplete" onchange='if(!this.value){this.form["service_sortie_id"].value=""}' size="25" />

        <script>
          Main.add(function(){
            var form = getForm({{$form_name}});
            var input = form.service_sortie_id_autocomplete_view;
            var url = new Url("system", "httpreq_field_autocomplete");
            url.addParam("class", "CSejour");
            url.addParam("field", "service_sortie_id");
            url.addParam("limit", 50);
            url.addParam("view_field", "nom");
            url.addParam("show_view", false);
            url.addParam("input_field", "service_sortie_id_autocomplete_view");
            url.addParam("wholeString", true);
            url.addParam("min_occurences", 1);
            url.autoComplete(input, "service_sortie_id_autocomplete_view", {
              minChars: 1,
              method: "get",
              select: "view",
              dropdown: true,
              afterUpdateElement: function(field,selected){
                $V(field.form["service_sortie_id"], selected.getAttribute("id").split("-")[2]);
                var selectedData = selected.down(".data");
                if (!form.destination.value) {
                  $V(form.destination, selectedData.get("default_destination"));
                }
              },
              callback: function(element, query){
                query += "&where[group_id]={{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}";
                var field = input.form.elements["cancelled"];
                if (field) {
                  query += "&where[cancelled]=" + $V(field);  return query;
                }
                return null;
              }
            });
          });
        </script>

        <input type="hidden" name="cancelled" value="0" />
    </tr>
    <tr id="sortie_deces_{{$sejour->_id}}"{{if $sejour->mode_sortie != "deces"}} style="display:none;" {{/if}}>
      <th>{{mb_label object=$sejour field="_date_deces"}}</th>
      <td colspan="3">
        {{mb_field object=$sejour field="_date_deces" value=$sejour->_ref_patient->deces register=true form=$form_name}}
      </td>
    </tr>
    {{if $module != "dPurgences" || ($module == "dPurgences" && $rpu && $rpu->sejour_id !== $rpu->mutation_sejour_id)}}
      <tr>
        <th>{{mb_label object=$sejour field="transport_sortie"}}</th>
        <td colspan="3">{{mb_field object=$sejour field="transport_sortie"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$sejour field="rques_transport_sortie"}}</th>
        <td colspan="3">{{mb_field object=$sejour field="rques_transport_sortie"}}</td>
      </tr>
    {{/if}}
    <tr>
      <th>{{mb_label object=$sejour field="commentaires_sortie"}}</th>
      <td colspan="3">{{mb_field object=$sejour field="commentaires_sortie" form=$form_name
        aidesaisie="resetSearchField: 0, resetDependFields: 0, validateOnBlur: 0"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field="destination"}}</th>
      <td colspan="3">{{mb_field object=$sejour field="destination" emptyLabel="Choose"}}</td>
    </tr>
    {{if $rpu && $rpu->_id}}
      <tr>
        <th>{{mb_label object=$rpu field="orientation"}}</th>
        <td colspan="3">{{mb_field object=$rpu field="orientation" emptyLabel="Choose" onchange="\$V(getForm('$form_rpu_name').orientation, \$V(this));"}}</td>
      </tr>
    {{/if}}
    {{if !$modify_sortie_prevue}}
      <tr>
        <td colspan="4" class="button">
          <button type="button" class="close singleclick"
                onclick="Admissions.annulerSortie(this.form, function() { document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();})">
            {{tr}}Cancel{{/tr}}
            {{mb_label object=$sejour field=sortie}}
          </button>
          <button type="submit" class="save singleclick">
            {{tr}}Validate{{/tr}}
            {{mb_label object=$sejour field=sortie}}
          </button>
        </td>
      </tr>
    {{else}}
      <tr>
        <td colspan="4" class="button">
          {{mb_field object=$sejour field="confirme_user_id" hidden=true}}
          <button type="submit" class="save">
            {{tr}}Save{{/tr}}
          </button>
          {{if $sejour->confirme}}
            <button type="submit" class="cancel singleclick" onclick="$V(this.form.confirme, ''); $V(this.form.confirme_user_id, '')">
              {{tr}}canceled_exit{{/tr}}
            </button>
          {{else}}
            <button type="submit" class="tick singleclick" onclick="if (!$V(this.form.confirme)){$V(this.form.confirme, '{{$dtnow}}');} $V(this.form.confirme_user_id, '{{$app->user_id}}')">
              {{tr}}allowed_exit{{/tr}}
            </button>
          {{/if}}
        </td>
      </tr>
    {{/if}}
  </table>
</form>

{{if $sejour->grossesse_id}}
  {{foreach from=$sejour->_ref_naissances item=_naissance}}
    {{assign var=sejour_enfant value=`$_naissance->_ref_sejour_enfant`}}
    {{assign var=form_name_enfant value="validerSortieEnfant`$sejour_enfant->_id`"}}

    <form name="{{$form_name_enfant}}" method="post"
          onsubmit="return onSubmitFormAjax(this, function() { if (window.reloadSortieLine) { reloadSortieLine('{{$sejour_enfant->_id}}')}})">
      {{mb_class object=$sejour_enfant}}
      {{mb_key object=$sejour_enfant}}
      <input type="hidden" name="view_patient" value="{{$sejour_enfant->_ref_patient->_view}}">
      <input type="hidden" name="del" value="0" />
      {{mb_field object=$sejour_enfant field=entree_reelle hidden=true}}
      {{mb_field object=$sejour_enfant field="sortie_reelle" hidden=true}}
      {{mb_field object=$sejour_enfant field=mode_sortie hidden=true}}
      {{mb_field object=$sejour_enfant field=confirme hidden=true}}
      {{mb_field object=$sejour_enfant field=confirme_user_id hidden=true}}
      {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
        {{mb_field object=$sejour_enfant field=mode_sortie_id hidden=true}}
      {{/if}}
    </form>
  {{/foreach}}
{{/if}}

{{if $rpu && $rpu->_id}}
  <form name="{{$form_rpu_name}}" method="post" onsubmit="return onSubmitFormAjax(this)">
    {{mb_key object=$rpu}}
    {{mb_class object=$rpu}}
    <input type="hidden" name="_validation" value="1">
    <input type="hidden" name="del" value="0" />
    {{mb_field object=$rpu field="orientation" hidden=true onchange=this.form.onsubmit()}}
  </form>
{{/if}}