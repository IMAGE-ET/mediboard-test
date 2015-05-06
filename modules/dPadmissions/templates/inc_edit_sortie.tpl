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

{{assign var=urgences_active value="dPurgences"|module_active}}
{{if $urgences_active}}
  {{mb_script module=dPurgences script=contraintes_rpu ajax=true}}
{{/if}}

{{assign var=pass_to_confirm value="dPplanningOp CSejour pass_to_confirm"|conf:"CGroups-$g"}}
{{assign var=form_name value="validerSortie`$sejour->_id`"}}
{{assign var=form_rpu_name value="editRpu`$sejour->_id`"}}

{{assign var=rpu value=$sejour->_ref_rpu}}
{{assign var=atu value=$sejour->_ref_consult_atu}}

{{assign var=class_sortie_reelle value=""}}
{{assign var=class_sortie_autorise value=""}}
{{assign var=class_mode_sortie value=""}}
{{assign var=is_praticien value=$app->_ref_user->isPraticien()}}
{{assign var=modify_sortie_reelle value=false}}

{{if $modify_sortie_prevue}}
  {{if $module == "dPurgences"}}
    {{if $rpu->sortie_autorise}}
      {{assign var=class_sortie_autorise value="valid-field"}}
    {{else}}
      {{assign var=class_sortie_autorise value="inform-field"}}
    {{/if}}
  {{else}}
    {{if $sejour->confirme}}
      {{assign var=class_sortie_autorise value="valid-field"}}
    {{else}}
      {{assign var=class_sortie_autorise value="inform-field"}}
    {{/if}}
  {{/if}}
{{else}}
  {{assign var=modify_sortie_reelle value=true}}
  {{assign var=class_mode_sortie value="notNull"}}
  {{if $sejour->sortie_reelle}}
    {{assign var=class_sortie_reelle value="valid-field"}}
  {{else}}
    {{assign var=class_sortie_reelle value="inform-field"}}
  {{/if}}
{{/if}}

{{if $rpu && $rpu->_id}}
  <form name="{{$form_name}}" method="post"
      onsubmit="return ContraintesRPU.checkObligatory('{{$rpu->_id}}',
        Admissions.confirmationSortie.curry(this, {{$modify_sortie_prevue}}, '{{$sejour->sortie_prevue}}',
        '{{"dPurgences CRPU impose_lit_service_mutation"|conf:"CGroups-$g"}}',
        function() {
          {{if $atu && $atu->_id && $conf.dPurgences.valid_cotation_sortie_reelle}}
            onSubmitFormAjax(getForm('ValidCotation_{{$sejour->_id}}'), function() {
              document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();
            });
          {{else}}
            document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();
          {{/if}}
         }))">
{{else}}
<form name="{{$form_name}}" method="post"
      onsubmit="return Admissions.confirmationSortie(this, {{$modify_sortie_prevue}}, '{{$sejour->sortie_prevue}}', 0,
        function() { document.fire('mb:valider_sortie'); document.stopObserving('mb:valider_sortie'); Control.Modal.close();})">
{{/if}}
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="dtnow" value="{{$dtnow}}" />
  <input type="hidden" name="action_confirm" value="">
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
        <th>
          {{if $module == "dPurgences" && $rpu && $rpu->mutation_sejour_id && $rpu->sejour_id !== $rpu->mutation_sejour_id}}
            <label>{{tr}}Csejour-sortie_reelle_mutation{{/tr}}</label>
           {{else}}
            {{mb_label object=$sejour field="sortie_reelle"}}
          {{/if}}
        </th>

        <td>
          {{assign var=date_time value=$sejour->sortie_reelle}}
          {{if (!$modify_sortie_prevue && !$sejour->sortie_reelle)}}
            {{assign var=date_time value=$dtnow}}
          {{/if}}

          {{mb_field object=$sejour field="sortie_reelle" form=$form_name register=$modify_sortie_reelle class=$class_sortie_reelle
                      onchange="Admissions.updateLitMutation(this.form);" value="$date_time"}}</td>
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
        <script>
          Main.add(function() {
            var form = getForm("{{$form_name}}");
            {{if $urgences_active}}
              ContraintesRPU.changeOrientation(form);
            {{/if}}
            Admissions.changeDestination(form);
            Admissions.changeSortie(form, '{{$sejour->_id}}');
          })
        </script>
        {{if $urgences_active}}
          {{assign var=onchange_mode_sortie value="ContraintesRPU.changeOrientation(this.form);Admissions.changeDestination(this.form);Admissions.changeSortie(this.form, '`$sejour->_id`')"}}
        {{else}}
          {{assign var=onchange_mode_sortie value="Admissions.changeDestination(this.form);Admissions.changeSortie(this.form, '`$sejour->_id`')"}}
        {{/if}}
        {{assign var=mode_sortie value=$sejour->mode_sortie}}
        {{if $sejour->service_sortie_id}}
          {{assign var=mode_sortie value="mutation"}}
        {{/if}}
        {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
          <script type="text/javascript">
            applyModeSortie = function(elt) {
              $V(elt.form.mode_sortie, elt.options[elt.selectedIndex].get('mode'));
              $V(elt.form.destination, elt.options[elt.selectedIndex].get('destination'));
              if (elt.form.orientation !== undefined)
              $V(elt.form.orientation, elt.options[elt.selectedIndex].get('orientation'));
            }
          </script>

          {{mb_field object=$sejour field=mode_sortie hidden=true class=$class_mode_sortie onchange="$onchange_mode_sortie"}}
          <select name="mode_sortie_id" class="{{$sejour->_props.mode_sortie_id}}" style="width: 16em;" onchange="applyModeSortie(this);">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$list_mode_sortie item=_mode}}
              <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" data-destination="{{$_mode->destination}}" data-orientation="{{$_mode->orientation}}" {{if $sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
                {{$_mode}}
              </option>
            {{/foreach}}
          </select>
        {{elseif "CAppUI::conf"|static_call:"dPurgences CRPU impose_create_sejour_mutation":"CGroups-$g"}}
          <select name="mode_sortie" class="{{$class_mode_sortie}}" onchange="{{$onchange_mode_sortie}}">
            {{foreach from=$sejour->_specs.mode_sortie->_list item=_mode}}
              <option value="{{$_mode}}" {{if $sejour->mode_sortie == $_mode}}selected{{/if}}
                {{if $_mode == "mutation"}}{{if $rpu->mutation_sejour_id}}selected{{else}}disabled{{/if}}{{/if}}>
                {{tr}}CSejour.mode_sortie.{{$_mode}}{{/tr}}
              </option>
            {{/foreach}}
          </select>
        {{else}}
          {{if $rpu && $rpu->mutation_sejour_id}}
            {{assign var=mode_sortie value="mutation"}}
           {{else}}
            {{assign var=mode_sortie value=$sejour->mode_sortie}}
          {{/if}}
          {{mb_field object=$sejour field="mode_sortie" class=$class_mode_sortie value=$mode_sortie onchange="$onchange_mode_sortie"}}
        {{/if}}
        {{if !$rpu || ($rpu && !$rpu->mutation_sejour_id)}}
          <input type="hidden" name="group_id" value="{{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}" />
        {{else}}
          <strong>
            <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
              Hospitalisation dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
            </a>
          </strong>
        {{/if}}
      </td>
      <th style="width: 100px">
        {{if $module == "dPurgences" && $rpu && $rpu->mutation_sejour_id && $rpu->sejour_id !== $rpu->mutation_sejour_id}}
          <label>{{tr}}Csejour-confirme_mutation{{/tr}}</label>
        {{else}}
          {{mb_label object=$sejour field="confirme"}}
        {{/if}}
      </th>
      {{if $module == "dPurgences"}}
        <td>{{mb_value object=$rpu field="sortie_autorisee"}}</td>
      {{else}}
        <td>{{mb_field object=$sejour field="confirme" register=true form=$form_name class=$class_sortie_autorise onchange="if(!this.value){\$('submitForm_sortie').disabled = false;}else{\$('submitForm_sortie').disabled = true;}"}}</td>
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
                if (form.orientation && !form.orientation.value) {
                  $V(form.orientation, selectedData.get("default_orientation"));
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
      <tbody id="transport_sortie_mutation_{{$sejour->_id}}">
        <tr>
          <th>{{mb_label object=$sejour field="transport_sortie"}}</th>
          <td colspan="3">{{mb_field object=$sejour field="transport_sortie"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$sejour field="rques_transport_sortie"}}</th>
          <td colspan="3">{{mb_field object=$sejour field="rques_transport_sortie"}}</td>
        </tr>
      </tbody>
    {{/if}}
    <tr>
      <th>{{mb_label object=$sejour field="commentaires_sortie"}}</th>
      <td colspan="3">{{mb_field object=$sejour field="commentaires_sortie" form=$form_name
        aidesaisie="resetSearchField: 0, resetDependFields: 0, validateOnBlur: 0"}}</td>
    </tr>
    {{assign var=destination_notnull value=""}}
    {{assign var=destination_value value=$sejour->destination}}
    {{if "CAppUI::conf"|static_call:"dPplanningOp CSejour required_destination":"CGroups-$g"}}
      {{assign var=destination_notnull value="notNull"}}
      {{if !$sejour->destination && $sejour->mode_sortie == "normal" || $sejour->mode_sortie == "deces"}}
        {{assign var=destination_value value=0}}
      {{/if}}
    {{/if}}
    <tr>
      <th>{{mb_label object=$sejour field="destination" class=$destination_notnull}}</th>
      <td colspan="3">
        {{mb_field object=$sejour field="destination" emptyLabel="Choose" class=$destination_notnull value=$destination_value}}
      </td>
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
          {{if "trajectoire"|module_active}}
            <script>
              Main.add(function() {
                var url = new Url('trajectoire', 'ajax_trajectoire_redirect');
                url.addParam('patient_id', '{{$sejour->patient_id}}');
                url.addParam('sejour_id', '{{$sejour->_id}}');
                url.requestUpdate('trajectoire_button');
              });
            </script>

            <span id="trajectoire_button"></span>
          {{/if}}
          <button type="button" class="close oneclick"
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
          <button type="submit" id="submitForm_sortie" class="save">
            {{tr}}Save{{/tr}}
          </button>
          {{if $sejour->confirme}}
            <button type="button" class="cancel oneclick"
                    onclick="{{if !$is_praticien}}
                                $V(this.form.action_confirm, 0);
                                Admissions.askconfirm('{{$sejour->_id}}');
                             {{else}}
                               $V(this.form.confirme, ''); $V(this.form.confirme_user_id, '');this.form.onsubmit();
                             {{/if}}">
              {{tr}}canceled_exit{{/tr}}
            </button>
          {{else}}
            <button type="button" class="tick oneclick"
                    onclick="{{if !$is_praticien}}
                      $V(this.form.action_confirm, 1);
                      Admissions.askconfirm('{{$sejour->_id}}');
                    {{else}}
                      if (!$V(this.form.confirme)) {
                        var sortie_prevue = $V(this.form.sortie_prevue);
                        var sortie_reelle = $V(this.form.sortie_reelle);
                        var sortie = sortie_reelle ? sortie_reelle : sortie_prevue;
                        $V(this.form.confirme, sortie);
                      }
                      $V(this.form.confirme_user_id, '{{$app->user_id}}');
                      this.form.onsubmit();
                    {{/if}}">
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

{{if $atu && $atu->_id && $conf.dPurgences.valid_cotation_sortie_reelle}}
  <form name="ValidCotation_{{$sejour->_id}}" action="" method="post">
    <input type="hidden" name="dosql" value="do_consultation_aed" />
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="consultation_id" value="{{$atu->_id}}" />
    <input type="hidden" name="valide" value="1" />
  </form>
{{/if}}

<div id="confirmSortieModal_{{$sejour->_id}}" style="display: none;">
  <form name="confirmSortie_{{$sejour->_id}}" method="post" action="?m=system&a=ajax_password_action"
        onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close, useFormAction: true})">
    <input type="hidden" name="callback" value="Admissions.afterConfirmPassword.curry({{$sejour->_id}})" />
    <input type="hidden" name="user_id" class="notNull" value="{{$sejour->_ref_praticien->_id}}" />
    <table class="form">
      <tr>
        <th class="title" colspan="2">
          {{tr}}Confirm-allowed-exit{{/tr}}
        </th>
      </tr>
      {{if !$pass_to_confirm}}
        <tr>
          <td colspan="2" class="button">
            <button type="button" class="tick"
                    onclick="Admissions.afterConfirmPassword({{$sejour->_id}}, '{{$app->_ref_user->_id}}'); Control.Modal.close();">
              {{$app->_ref_user}}
            </button>
            <br/>
            OU
          </td>
        </tr>
      {{/if}}
      <tr>
        <th>{{tr}}CSejour-_nomPraticien{{/tr}}</th>
        <td>
          <input type="text" name="_user_view" class="autocomplete" value="{{$sejour->_ref_praticien}}" />
          <script>
            Main.add(function() {
              var form = getForm("confirmSortie_{{$sejour->_id}}");
              new Url("mediusers", "ajax_users_autocomplete")
                .addParam("input_field", form._user_view.name)
                .addParam("praticiens", 1)
                .autoComplete(form._user_view, null, {
                minChars: 0,
                method: "get",
                select: "view",
                dropdown: true,
                width: '200px',
                afterUpdateElement: function(field, selected) {
                  $V(form._user_view, selected.down('.view').innerHTML);
                  var id = selected.getAttribute("id").split("-")[2];
                  $V(form.user_id, id);
                }
              });
            });
          </script>
        </td>
      </tr>
      <tr>
        <th>
          <label for="user_password">{{tr}}Password{{/tr}}</label>
        </th>
        <td>
          <input type="password" name="user_password" class="notNull password str" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="button">
          <button type="submit" class="tick oneclick">{{tr}}Validate{{/tr}}</button>
          <button type="button" class="cancel oneclick" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>