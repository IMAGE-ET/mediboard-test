<form name="editRPU" action="?m={{$m}}{{if !$can->edit}}&amp;tab=vw_idx_rpu{{/if}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="urgences" />
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="actif" value="1"/>
  {{mb_key object=$rpu}}
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />

  <input type="hidden" name="_bind_sejour" value="1" />
  <input type="hidden" name="_entree_preparee" value="{{$sejour->entree_preparee}}"/>
  <table class="form">
    <colgroup>
      <col class="narrow" />
      <col style="width: 50%" />
      <col class="narrow" />
      <col style="width: 50%" />
    </colgroup>

    <tr>
      {{if $rpu->_id}}
        <th class="title modify" colspan="4">

          {{mb_include module=system template=inc_object_notes      object=$sejour}}
          {{mb_include module=system template=inc_object_idsante400 object=$rpu}}
          {{mb_include module=system template=inc_object_history    object=$rpu}}

          <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=planningOp&tab=vw_edit_sejour&sejour_id={{$sejour->_id}}">
            <img src="images/icons/edit.png" alt="modifier" />
          </a>

          {{tr}}CRPU-title-modify{{/tr}}
          '{{$rpu}}'
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        </th>
      {{else}}
        <th class="title" colspan="4">
          {{tr}}CRPU-title-create{{/tr}}
          {{if $sejour->_NDA}}
            pour le dossier
            {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
          {{/if}}
        </th>
      {{/if}}
    </tr>

    {{if $rpu->_annule}}
      <tr>
        <th class="category cancelled" colspan="4">
          {{tr}}CRPU-_annule{{/tr}}
        </th>
      </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$rpu field="_responsable_id"}}</th>
      <td>
        <select name="_responsable_id" style="width: 15em;" class="{{$rpu->_props._responsable_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$rpu->_responsable_id list=$listResponsables}}
        </select>
      </td>

      {{if $conf.dPplanningOp.CSejour.use_custom_mode_entree && $list_mode_entree|@count}}
        <th>{{mb_label object=$rpu field=_mode_entree_id}}</th>
        <td>
          {{mb_field object=$sejour field=mode_entree onchange="\$V(this.form._modifier_entree, 0); ContraintesRPU.updateProvenance(this.value, true); changeModeEntree(this.value)" hidden=true}}

          <input type="hidden" name="_mode_entree_id" value="{{$rpu->_mode_entree_id}}"
                 class="autocomplete notNull" size="50"/>
          <input type="text" name="_mode_entree_id_autocomplete_view" size="50" value="{{if $rpu->_mode_entree_id}}{{$rpu->_fwd._mode_entree_id}}{{/if}}"
                 class="autocomplete" onchange='if(!this.value){this.form["_mode_entree_id"].value=""}' />

          <script>
            Main.add(function(){
              var form = getForm("editRPU");
              var input = form._mode_entree_id_autocomplete_view;
              var url = new Url("system", "httpreq_field_autocomplete");
              url.addParam("class", "CRPU");
              url.addParam("field", "_mode_entree_id");
              url.addParam("limit", 50);
              url.addParam("view_field", "libelle");
              url.addParam("show_view", false);
              url.addParam("input_field", "_mode_entree_id_autocomplete_view");
              url.addParam("wholeString", true);
              url.addParam("min_occurences", 1);
              url.autoComplete(input, "_mode_entree_id_autocomplete_view", {
                minChars: 1,
                method: "get",
                select: "view",
                dropdown: true,
                afterUpdateElement: function(field, selected){
                  $V(field.form["_mode_entree_id"], selected.getAttribute("id").split("-")[2]);
                  var elementFormRPU = getForm("editRPU").elements;
                  var selectedData = selected.down(".data");
                  $V(elementFormRPU.mode_entree, selectedData.get("mode"));
                },
                callback: function(element, query){
                  query += "&where[group_id]={{$g}}";
                  query += "&where[actif]=1";
                  return query;
                }
              });
            });
          </script>
        </td>
        {{else}}
        <th>{{mb_label object=$rpu field="_mode_entree"}}</th>
        <td>
          {{mb_field object=$rpu field="_mode_entree" style="width: 15em;" emptyLabel="Choose" onchange="ContraintesRPU.updateProvenance(this.value, true); changeModeEntree(this.value); changeProvenanceWithEntree(this)"}}
        </td>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$rpu field="ide_responsable_id"}}</th>
      <td colspan="3">
        {{mb_field object=$rpu field="ide_responsable_id" hidden=true}}
        <input type="text" name="ide_responsable_id_view" class="autocomplete" value="{{$rpu->_ref_ide_responsable->_view}}"
               placeholder="&mdash; {{tr}}Choose{{/tr}}"/>
        <script>
          Main.add(function () {
            var form = getForm("editRPU");
            new Url("dPurgences", "ajax_ide_responsable_autocomplete")
              .autoComplete(form.ide_responsable_id_view, null, {
              minChars: 2,
              method: "get",
              select: "view",
              dropdown: true,
              updateElement: function(selected) {
                var id = selected.get("id");
                $V(form.ide_responsable_id, id);
                $V(form.ide_responsable_id_view, selected.get("name"));
                }.bind(form)
            });
          });
        </script>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$rpu field="_entree"}}</th>
      <td>{{mb_field object=$rpu field="_entree" form="editRPU" register=true}}</td>

      <th></th>
      <td>
        <input type="hidden" name="group_id" value="{{$g}}" />
        <div id="etablissement_entree_transfert" {{if !$rpu->_etablissement_entree_id}}style="display:none"{{/if}}>
          {{mb_field object=$rpu field="_etablissement_entree_id" form="editRPU" style="width: 12em;" autocomplete="true,1,50,true,true"}}
        </div>
        <div id="service_entree_mutation" {{if !$rpu->_service_entree_id}}style="display:none"{{/if}}>
          {{mb_field object=$rpu field="_service_entree_id" form="editRPU" autocomplete="true,1,50,true,true"}}
          <input type="hidden" name="cancelled" value="0" />
        </div>
      </td>
    </tr>

    <tr>
      <th>
        {{mb_include module=patients template=inc_button_pat_anonyme form=editRPU patient_id=$rpu->_patient_id input_name="_patient_id"}}

        <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}"  onchange="requestInfoPat();" />
        {{mb_label object=$rpu field="_patient_id"}}
      </th>
      <td>
        <input type="text" name="_patient_view" style="width: 15em;" value="{{$patient->_view}}"
          {{if $conf.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}}
            onfocus="PatSelector.init()"
          {{/if}}
               readonly="readonly" />

        {{if $conf.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}}
          <button type="button" class="search notext" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
        {{/if}}
        <script>
          PatSelector.init = function(){
            this.sForm = "editRPU";
            this.sId   = "_patient_id";
            this.sView = "_patient_view";
            this.pop();
          }
        </script>
        {{if $patient->_id}}
          <button id="button-edit-patient" type="button" class="edit notext"
                  onclick="location.href='?m=patients&tab=vw_edit_patients&patient_id='+this.form._patient_id.value"
            >
            {{tr}}Edit{{/tr}}
          </button>
        {{/if}}

      </td>

      {{if $conf.dPurgences.old_rpu == "1"}}
        <th>{{mb_label object=$rpu field="urprov"}}</th>
        <td>{{mb_field object=$rpu field="urprov" emptyLabel="Choose" style="width: 15em;"}}</td>
      {{else}}
        {{assign var="provenance" value=""}}
        {{if "CAppUI::conf"|static_call:"dPurgences CRPU provenance_necessary":"CGroups-$g"}}
          {{assign var="provenance" value="notNull"}}
        {{/if}}
        <th>{{mb_label object=$rpu field="_provenance" class=$provenance}}</th>
        <td>{{mb_field object=$rpu field="_provenance" class=$provenance emptyLabel="Choose" style="width: 15em;"}}</td>
      {{/if}}
    </tr>

    <tr>
      {{if $can->edit}}
        <th>{{mb_label object=$rpu field="ccmu"}}</th>
        <td>
          {{mb_field object=$rpu field="ccmu" emptyLabel="Choose" style="width: 15em;"}}
          {{if $conf.ref_pays == 2}}
            <script>
              Main.add(function () {
                var form = getForm("editRPU");
                var ccmu = form.ccmu;
                ccmu.options[2].disabled=true;
                ccmu.options[6].disabled=true;
                ccmu.options[7].disabled=true;
                ccmu.options[2].hide();
                ccmu.options[6].hide();
                ccmu.options[7].hide();
              });
            </script>
          {{/if}}
        </td>
      {{else}}
        <th></th>
        <td></td>
      {{/if}}

      <th>{{mb_label object=$rpu field="_transport"}}</th>
      <td>{{mb_field object=$rpu field="_transport"  emptyLabel="Choose" onchange="changePecTransport(this);" style="width: 15em;"}}</td>
    </tr>

    <tr>
      {{assign var=required_uf_soins value="dPplanningOp CSejour required_uf_soins"|conf:"CGroups-$g"}}
      {{if $required_uf_soins != "no"}}
      <th>
        {{mb_label object=$rpu field="_uf_soins_id"}}
      </th>
      <td>
        <select name="_uf_soins_id" class="ref {{if $required_uf_soins == "obl"}}notNull{{/if}}" style="width: 15em">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$ufs.soins item=_uf}}
            <option value="{{$_uf->_id}}" {{if $rpu->_uf_soins_id == $_uf->_id}}selected="selected"{{/if}}>
              {{mb_value object=$_uf field=libelle}}
            </option>
          {{/foreach}}
        </select>
      </td>
      {{else}}
        <th></th>
        <td></td>
      {{/if}}

      <th>{{mb_label object=$rpu field="pec_transport"}}</th>
      <td>{{mb_field object=$rpu field="pec_transport" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>

    {{if $conf.dPurgences.display_regule_par}}
      <tr>
        <th></th>
        <td></td>
        <th>{{mb_label object=$rpu field="regule_par"}}</th>
        <td>{{mb_field object=$rpu field="regule_par" emptyLabel="Choose"}}</td>
      </tr>
    {{/if}}

    <script>
      Main.add(function(){
        var form = getForm("editRPU");

        if (form.elements._service_id) {
          var box = form.elements.box_id;
          box.observe("change", function(event){
            var service_id = box.options[box.selectedIndex].up("optgroup").get("service_id");
            $V(form.elements._service_id, service_id, false);
          });
        }
      });
    </script>
    <!-- Selection du service -->
    <tr>
      <th>{{mb_label object=$rpu field="box_id"}}</th>
      <td style="vertical-align: middle;">
        {{mb_include module=dPhospi template="inc_select_lit" field=box_id selected_id=$rpu->box_id ajaxSubmit=0 listService=$services}}
        <button type="button" class="cancel opacity-60 notext" onclick="this.form.elements['box_id'].selectedIndex=0"></button>
        &mdash; {{tr}}CRPU-_service_id{{/tr}} :
        {{if $services|@count == 1}}
          {{assign var=first_service value=$services_type|@reset|@reset}}
          <input type="hidden" name="_service_id" value="{{$first_service->_id}}" />
          {{$first_service->_view}}
        {{else}}
          <select name="_service_id" class="{{$sejour->_props.service_id}}" onchange="$V(this.form.box_id, '', false)">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$services_type item=_services key=nom_serv}}
              <optgroup label="{{$nom_serv}}">
                {{foreach from=$_services item=_service}}
                  <option value="{{$_service->_id}}" {{if $rpu->_id && $sejour->service_id == $_service->_id}}selected{{/if}}>
                    {{$_service->_view}}
                  </option>
                {{/foreach}}
              </optgroup>
            {{/foreach}}
          </select>
        {{/if}}
      </td>
      <th>{{mb_label object=$rpu field="date_at"}}</th>
      <td>{{mb_field object=$rpu field="date_at" form="editRPU" register=true}}</td>
    </tr>

    {{if $can->edit}}
      <script>
        changeMotifSfmu = function(form) {
          {{if "CAppUI::conf"|static_call:"dPurgences CRPU defer_sfmu_diag_inf":"CGroups-$g"}}
          $V(form.diag_infirmier, form.motif_sfmu_autocomplete_view.value);
          {{/if}}
        };
      </script>
      {{if $gerer_circonstance}}
        <tr>
          <th>{{mb_label object=$rpu field="circonstance"}}</th>
          <td>
            {{mb_field object=$rpu field="circonstance" autocomplete="true,1,10,true,true" form=editRPU}}
          </td>
          {{if "CAppUI::conf"|static_call:"dPurgences CRPU display_motif_sfmu":"CGroups-$g"}}
            {{assign var=notnull value=""}}
            {{if "CAppUI::conf"|static_call:"dPurgences CRPU motif_sfmu_accueil":"CGroups-$g"}}
              {{assign var=notnull value="notNull"}}
            {{/if}}
            <th>{{mb_label object=$rpu field="motif_sfmu" class=$notnull}}</th>
            <td>{{mb_field object=$rpu field="motif_sfmu" autocomplete="true,1,10,true,true" form=editRPU size=50 class=$notnull onchange="changeMotifSfmu(this.form);"}}
                <button type="button" class="search notext" onclick="CCirconstance.searchMotifSFMU(this.form)">
                  {{tr}}Search{{/tr}}
                </button>
            </td>
          {{else}}
            <th></th>
            <td></td>
          {{/if}}
        </tr>
      {{/if}}

      {{if !$gerer_circonstance && "CAppUI::conf"|static_call:"dPurgences CRPU display_motif_sfmu":"CGroups-$g"}}
        {{assign var=notnull value=""}}
        {{if "CAppUI::conf"|static_call:"dPurgences CRPU motif_sfmu_accueil":"CGroups-$g"}}
          {{assign var=notnull value="notNull"}}
        {{/if}}
        <tr>
          <th></th>
          <td></td>
          <th>{{mb_label object=$rpu field="motif_sfmu" class=$notnull}}</th>
          <td>{{mb_field object=$rpu field="motif_sfmu" autocomplete="true,1,10,true,true" form=editRPU size=50 class=$notnull onchange="changeMotifSfmu(this.form);"}}
              <button type="button" class="search notext" onclick="CCirconstance.searchMotifSFMU(this.form)">
                {{tr}}Search{{/tr}}
              </button>
          </td>
        </tr>
      {{/if}}

      {{if $rpu->motif_entree}}
        <tr>
          <th>{{mb_label object=$rpu field="motif_entree"}}</th>
          <td>{{mb_value object=$rpu field="motif_entree"}}</td>
          <th></th>
          <td></td>
        </tr>
      {{/if}}

      <tr>
        <th>{{mb_label object=$rpu field="diag_infirmier"}}</th>
        <td>
          {{mb_field object=$rpu field="diag_infirmier" class="autocomplete" form="editRPU"
          aidesaisie="validate: function() { form.onsubmit() },
                                      validateOnBlur: 0,
                                      resetSearchField: 0,
                                      resetDependFields: 0"}}
        </td>
        <th>{{mb_label object=$rpu field="pec_douleur"}}</th>
        <td>
          {{mb_field object=$rpu field="pec_douleur" class="autocomplete" form="editRPU"
          aidesaisie="validate: function() { form.onsubmit() },
                                      validateOnBlur: 0,
                                      resetSearchField: 0,
                                      resetDependFields: 0"}}
        </td>
      </tr>
      {{if !$rpu->_id && "CAppUI::conf"|static_call:"dPurgences CRPU impose_motif":"CGroups-$g"}}
        <tr>
          <th>{{mb_label object=$rpu field="motif"}}</th>
          <td>{{mb_field object=$rpu field="motif" class="autocomplete" form="editRPU"
            aidesaisie="validateOnBlur: 0, resetSearchField: 0,resetDependFields: 0"}}
          <td colspan="2"></td>
        </tr>
      {{/if}}
    {{else}}
      <th>{{mb_label object=$rpu field="motif_entree"}}</th>
      <td>
        {{mb_field object=$rpu field="motif_entree" class="autocomplete" form="editRPU"
        aidesaisie="validate: function() { form.onsubmit() },
                                      validateOnBlur: 0,
                                      resetSearchField: 0,
                                      resetDependFields: 0"}}
      </td>
      <th></th>
      <td></td>
    {{/if}}

    <tr>
      <td class="button" colspan="4">
        {{if $rpu->_id}}
          <button class="modify" type="submit">Valider</button>
          {{mb_ternary var=annule_text test=$sejour->annule value="Rétablir" other="Annuler"}}
          {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}

          <button class="{{$annule_class}}" type="button" onclick="cancelRPU();">
            {{$annule_text}}
          </button>

          {{if !$sejour->entree_preparee}}
            <button class="tick" type="submit" onclick="$V(this.form._entree_preparee, 1)">{{tr}}CSejour-entree_preparee{{/tr}}</button>
          {{/if}}

          {{if $can->admin}}
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'urgence ',objName:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}

          <button type="button" class="print" onclick="printDossier({{$rpu->_id}})">
            {{tr}}Print{{/tr}} dossier
          </button>

          <button type="button" class="print" onclick="printEtiquettes();">
            {{tr}}CModeleEtiquette.print_labels{{/tr}}
          </button>

          <a class="button new" href="?m=urgences&tab=vw_aed_rpu&rpu_id=0">
            {{tr}}CRPU-title-create{{/tr}}
          </a>

          {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
          {{if $ecap_dhe}}
            {{mb_include module=ecap template=inc_button_dhe_urgence sejour_id=$sejour->_id}}
          {{/if}}
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    {{if !$rpu->_id}}
      <tr>
        <td colspan="4">
          <fieldSet>
            <legend>Infos patient</legend>
            <div class="text" id="infoPat">
              <div class="empty">Aucun patient sélectionné</div>
            </div>
          </fieldSet>
        </td>
      </tr>
    {{/if}}
  </table>
</form>