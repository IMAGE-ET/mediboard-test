{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=pass_to_confirm value="dPplanningOp CSejour pass_to_confirm"|conf:"CGroups-$g"}}

{{mb_script module=patients script=correspondant ajax=true}}
{{mb_script module=system script=alert ajax=true}}
{{mb_script module=dPadmissions script=admissions ajax=true}}

<script>
  modalViewComplete = function(object_guid, title) {
    var url = new Url("system", "httpreq_vw_complete_object");
    url.addParam("object_guid", object_guid);
    url.requestModal(800, 500, { title: title });
  }

  popEtatSejour = function(sejour_id) {
    var url = new Url("hospi", "vw_parcours");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(700, 550);
  }

  toggleProgressBefore = function() {
    $$(".in_progress_before").invoke("toggleClassName", "show_important");
    $$(".class_progress_before").invoke("toggleClassName", "selected");
  }
  toggleProgressAfter = function() {
    $$(".in_progress_after").invoke("toggleClassName", "show_important");
    $$(".class_progress_after").invoke("toggleClassName", "selected");
  }

  afterEditCorrespondant = function() {
    if (window.loadSuiviClinique) {
      loadSuiviClinique('{{$sejour->_id}}')
    }
    else if (window.reloadSynthese) {
      window.reloadSynthese();
    }
  }

  toggleAutorisation = function(status) {
    var isPraticien = "{{$app->_ref_user->isPraticien()}}";
    var pass_to_confirm = "{{$pass_to_confirm}}";
    var form = getForm("edit-sejour-frm");

    if (status == 1) {
      if (isPraticien == "1" || pass_to_confirm == "0") {
        $V(form.confirme_user_id, User.id);
      }
      modal("confirmSortieModal", {width: "410px", height: "290px"});
    }
    else {
      if (isPraticien == "1" || pass_to_confirm == "0") {
        $V(form.confirme, "");
        $V(form.confirme_user_id, "");
        return form.onsubmit();
      }
      else {
        $V(form._cancel_confirme, 1);
        $("confirme_area").hide();
        modal("confirmSortieModal", {width: "410px", height: "290px"});
      }
    }
  }

  afterConfirmPassword = function() {
    var formFrom = getForm("confirmSortie");
    var formTo = getForm("edit-sejour-frm");
    var cancel_confirme = $V(formTo._cancel_confirme);
    $V(formTo.confirme, cancel_confirme == "1" ? "" : $V(formFrom.confirme));
    $V(formTo.confirme_user_id, cancel_confirme == "1" ? "" : $V(formFrom.user_id));
    formTo.onsubmit();
  }

  Main.add(function() {
    {{if "forms"|module_active}}
    ExObject.loadExObjects("{{$sejour->_class}}", "{{$sejour->_id}}", "list-ex_objects", 0.5);
    {{/if}}

    var form = getForm("confirmSortie");
    {{if !$app->_ref_user->isPraticien() && $pass_to_confirm}}
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("input_field", form._user_view.name);
    url.autoComplete(form._user_view, null, {
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
    {{/if}}
    Calendar.regField(form.confirme);
  });
</script>

<div id="confirmSortieModal" style="display: none;">
  <form name="confirmSortie" method="post" action="?m=system&a=ajax_password_action"
        onsubmit="return onSubmitFormAjax(this, {useFormAction: true})">
    <input type="hidden" name="callback" value="afterConfirmPassword" />
    <input type="hidden" name="user_id" class="notNull" value="{{$app->_ref_user->_id}}" />
    <table class="form">
      <tr>
        <th class="title" colspan="2">
          Autorisation de sortie
        </th>
      </tr>
      <tr>
        <tbody id="confirme_area">
        <th>
          Date de sortie autorisée :
        </th>
        <td>
          <input name="confirme" type="hidden" class="dateTime" value="{{$sejour->sortie}}" />
        </td>
      </tr>
      </tbody>
      {{if !$app->_ref_user->isPraticien() && $pass_to_confirm}}
        <tr>
          <th>Utilisateur</th>
          <td>
            <input type="text" name="_user_view" class="autocomplete" value="{{$app->_ref_user}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="user_password">Mot de passe</label>
          </th>
          <td>
            <input type="password" name="user_password" class="notNull password str" />
          </td>
        </tr>
      {{/if}}
      <tr>
        <td colspan="2" class="button">
          {{if $app->_ref_user->isPraticien()}}
            <button type="button" class="tick singleclick" onclick="afterConfirmPassword()">{{tr}}Validate{{/tr}}</button>
          {{else}}
            <button class="tick singleclick">{{tr}}Validate{{/tr}}</button>
          {{/if}}
          <button type="button" class="cancel singleclick"
                  onclick="Control.Modal.close(); {{if !$sejour->confirme}}getForm('edit-sejour-frm')._confirme.checked = false;{{/if}}">{{tr}}Cancel{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<table class="main" style="text-align: left; width: 100%">
<tr>
<!-- Informations sur le patient -->
<td style="width: 50%; vertical-align: top">
<table class="tbl" style="table-layout: fixed;">
  <tr>
    <th colspan="2" class="category">
            <span style="float: right">
              <button type="button" class="search" onclick="modalViewComplete('{{$patient->_guid}}', 'Détail du patient')">Patient</button>
            </span>
            <span style="float: left;">
              <button class="lookup notext" style="margin: 0;" onclick="popEtatSejour('{{$sejour->_id}}');">Etat du séjour</button>
            </span>
      Coordonnées
    </th>
  </tr>
  <tr>
    <td colspan="2">
      {{if $patient->nom_jeune_fille}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="nom_jeune_fille"}}</strong>
          {{mb_value object=$patient field="nom_jeune_fille"}}
        </div>
      {{/if}}
      {{if $patient->tel}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="tel"}}</strong>
          {{mb_value object=$patient field="tel"}}
        </div>
      {{/if}}
      {{if $patient->tel2}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="tel2"}}</strong>
          {{mb_value object=$patient field="tel2"}}
        </div>
      {{/if}}
      {{if $patient->tel_autre}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="tel_autre"}}</strong>
          {{mb_value object=$patient field="tel_autre"}}
        </div>
      {{/if}}
      {{if $patient->email}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="email"}}</strong>
          {{mb_value object=$patient field="email"}}
        </div>
      {{/if}}
      {{if $patient->profession}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="profession"}}</strong>
          {{mb_value object=$patient field="profession"}}
        </div>
      {{/if}}
      {{if $patient->rques}}
        <div class="cellule_patient">
          <strong>{{mb_label object=$patient field="rques"}}</strong>
          {{mb_value object=$patient field="rques"}}
        </div>
      {{/if}}
    </td>
  </tr>
</table>

<!-- Correspondance -->
<table class="tbl">
  <tr>
    <th style="width: 1%;">
      <button type="button" class="add notext" style="float: left;"
              onclick="Correspondant.edit(0, '{{$patient->_id}}', afterEditCorrespondant);"></button>
    </th>
    <th class="category">
      {{tr}}CCorrespondantPatient-nom{{/tr}} / {{tr}}CCorrespondantPatient-prenom{{/tr}}
    </th>
    <th class="category">
      {{tr}}CCorrespondantPatient-mob{{/tr}}
    </th>
    <th class="category">
      {{tr}}CCorrespondantPatient-tel{{/tr}}
    </th>
  </tr>
  {{foreach from=$patient->_ref_cp_by_relation item=_correspondants}}
    {{foreach from=$_correspondants item=_correspondant}}
      <tr>
        <td>
          <strong>{{mb_value object=$_correspondant field=relation}}</strong>
        </td>
        <td>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_correspondant->_guid}}')">
                  {{mb_value object=$_correspondant field="nom"}}
                  {{mb_value object=$_correspondant field="prenom"}}
                </span>
        </td>
        <td>
          {{mb_value object=$_correspondant field="mob"}}
        </td>
        <td>
          {{mb_value object=$_correspondant field="tel"}}
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
  {{if !$patient->_ref_correspondants_patient|@count}}
    <tr>
      <td colspan="4" class="empty">
        {{tr}}CCorrespondantPatient.none{{/tr}}
      </td>
    </tr>
  {{/if}}
</table>

<!--  Informations sur le séjour -->
<form name="edit-sejour-frm" method="post" action="?"
      onsubmit="return onSubmitFormAjax(this, function() { Control.Modal.close(); loadSuiviClinique('{{$sejour->_id}}') })">
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_cancel_confirme" value="0"/>
  {{mb_field object=$sejour field=confirme hidden=true}}
  {{mb_field object=$sejour field=confirme_user_id hidden=true}}
  {{mb_field object=$sejour field=entree_prevue hidden=true}}
  {{mb_key object=$sejour}}
  <table class="tbl">
    <tr>
      <th class="category" colspan="2">
              <span style="float: right">
                <button type="button" class="search" onclick="modalViewComplete('{{$sejour->_guid}}', 'Détail du séjour')">Détail</button>
              </span>
        {{tr}}CSejour{{/tr}}
      </th>
    </tr>
    <tr>
      <td style="width: 50%;">
        <strong>{{mb_label object=$sejour field="libelle"}}</strong>
        {{mb_value object=$sejour field="libelle"}}
      </td>
      <td>
        <strong>{{mb_label object=$sejour field="praticien_id"}}</strong>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
      </td>
    </tr>
    <tr>
      <td>
        <strong>{{mb_label object=$sejour field="entree"}}{{if $sejour->entree_reelle}} (effectuée){{/if}}</strong>
        {{mb_value object=$sejour field="entree"}}
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createDOM(this, 'confirme_sortie_{{$sejour->_id}}');">
          <strong>{{mb_label object=$sejour field="sortie"}}
            {{if $sejour->sortie_reelle}} (effectuée){{/if}}</strong>
          {{mb_value object=$sejour field="sortie"}}
          <span {{if $sejour->confirme}}class="ok"{{/if}}>
            {{if $sejour->confirme}}
              <br/>
              <strong>Sortie autorisée</strong> pour le {{mb_value object=$sejour field="confirme"}} <br/>
              Par {{$sejour->_ref_confirme_user->_view}}
            {{/if}}
          </span>
          {{if $sejour->mode_sortie == "transfert" && $sejour->etablissement_sortie_id}}
            <br/><strong>{{mb_label object=$sejour field="etablissement_sortie_id"}}</strong>
            {{mb_value object=$sejour field="etablissement_sortie_id"}}
          {{/if}}
        </span>
        <div id="confirme_sortie_{{$sejour->_id}}" style="display: none">
          {{mb_include module=planningOp template=inc_vw_sortie_sejour}}
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <strong>{{mb_label object=$sejour field="type"}}</strong>
        {{mb_value object=$sejour field="type"}}
      </td>
      <td>
        <button type="button" class="edit" onclick='Admissions.validerSortie("{{$sejour->_id}}", true, window.loadSuiviClinique && loadSuiviClinique.curry("{{$sejour->_id}}"));'>
          Autorisation Sortie
        </button>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <strong>{{mb_label object=$sejour field=rques text="CSejour-rques-court"}}</strong>
        <span id="CSejour-rques" class="text compact" style="white-space: normal;">{{$sejour->rques}}</span>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=planningOp template=inc_infos_operation alert=1}}
{{mb_include module=cabinet template=inc_infos_consultation_sejour}}

{{if $sejour->_ref_transmissions|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="6">Transmissions de synthèse</th>
    </tr>
    <tr>
      <th class="narrow">{{tr}}Date{{/tr}}</th>
      <th class="narrow">Cible</th>
      <th>Donnée</th>
      <th>Action</th>
      <th>Résultat</th>
    </tr>
    {{foreach from=$sejour->_ref_transmissions item=_suivi}}
      <tr>
        {{mb_include module=hospi template=inc_line_suivi show_type=false show_patient=false readonly=true show_link=false}}
      </tr>
    {{/foreach}}
  </table>
{{/if}}

{{if $sejour->_ref_observations|@count}}
  <table class="tbl">
    <tr>
      <th colspan="7" class="title">Observations</th>
    </tr>
    <tr>
      <th class="narrow">{{tr}}Date{{/tr}}</th>
      <th class="narrow">{{tr}}Hour{{/tr}}</th>
      <th>{{mb_title class=CObservationMedicale field=text}}</th>
    </tr>
    {{foreach from=$sejour->_ref_observations item=_obs}}
      <tr>
        <td style="text-align: center; height: 22px;">
          {{mb_ditto name=date_obs value=$_obs->date|date_format:$conf.date}}
        </td>
        <td style="text-align: center;">
          {{$_obs->date|date_format:$conf.time}}
        </td>
        <td class="text libelle_trans">{{mb_value object=$_obs field=text}}</td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}

{{if $sejour->_ref_tasks|@count}}
  <table class="tbl">
    <tr>
      <th class="title">Tâches en cours <small>({{$sejour->_ref_tasks|@count}})</small></th>
    </tr>
  </table>
  {{mb_include module=soins template=inc_vw_tasks_sejour mode_realisation=0 readonly=1 header=0}}
{{/if}}
</td>

<td style="vertical-align: top;" rowspan="2">

{{if "dPprescription"|module_active}}
  {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
  <table class="tbl">
  <tr>
    <th class="title">
      <button type="button" style="float: right;" class="search class_progress_after" onclick="toggleProgressAfter();" title="{{tr}}CPrescription.in_progress_after{{/tr}}">
        +{{$days_config}}J
      </button>
      <button type="button" style="float: right;" class="search class_progress_before" onclick="toggleProgressBefore();" title="{{tr}}CPrescription.in_progress_before{{/tr}}">
        -{{$days_config}}J
      </button>
      <button type="button" style="float: left;" class="search" onclick="modalPrescriptionLegend = Modal.open($('modal-prescription-legend'), {height: '100px', width: '290px'});" title="{{tr}}Legend{{/tr}}">{{tr}}Legend{{/tr}}</button>
      {{tr}}CPrescription.in_progress{{/tr}}
    </th>
    {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count ||
    $prescription->_ref_prescription_line_mixes|@count || $prescription->_ref_lines_elements_comments|@count}}
    {{if $prescription->_ref_lines_med_comments.med|@count}}
  <tr>
    <th>{{tr}}CPrescription._chapitres.med{{/tr}}</th>
  </tr>
  <!-- passé -->
  <tr class="hatching in_progress_before opacity-60">
    <td class="text">
      {{assign var=is_first value=1}}
      {{assign var=total value=0}}
      {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
        {{if $_line->_fin_reelle && $_line->_fin_reelle < $date && $_line->_fin_reelle >= $date_before}}
          {{if !$is_first}}
            &ndash;
          {{/if}}
          {{assign var=is_first value=0}}
          {{math equation="x+1" x=$total assign=total}}
          {{$_line->_ref_produit->libelle_abrege}}
        {{/if}}
      {{/foreach}}
      {{if $total == 0}}
        <div class="empty">Aucune ligne de médicament passée</div>
      {{/if}}
    </td>
  </tr>
  <!-- Maintenant -->
  <tr>
    <td class="text">
      {{assign var=is_first value=1}}
      {{assign var=total value=0}}
      {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
        {{if ($_line->_fin_reelle && $_line->_fin_reelle >= $date) &&
        $_line->_debut_reel && $_line->_debut_reel <= $date}}
          {{if !$is_first}}
            &ndash;
          {{/if}}
          {{assign var=is_first value=0}}
          {{math equation="x+1" x=$total assign=total}}
          {{if $_line->_fin_relative}}
            {{math assign=prolongation_time_day equation="x / 24" x="dPprescription general prolongation_time"|conf:"CGroups-$g"}}
            {{math assign=prescription_end_real equation="x - y" x=$_line->_fin_relative y=$prolongation_time_day|intval}}
          {{/if}}
          <span {{if $_line->_fin_reelle|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
            {{if $_line->warning_day && (($_line->duree && $_line->_fin_relative <= $_line->warning_day) || (!$_line->duree && $prescription_end_real <= $_line->warning_day))}}style="border-bottom: 2px solid orangered"{{/if}}
                class="{{if $_line->highlight}}highlight_red{{/if}}"
                onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
                        {{$_line->_ref_produit->libelle_abrege}}
            {{if $_line->_alerte_antibio}}
              <label title="Réévaluation antibiothérapie" style="font-weight: bold; color: red;">(Reeval ATB)</label>
            {{/if}}
                      </span>
        {{/if}}
      {{/foreach}}
      {{if $total == 0}}
        <div class="empty">Aucune ligne de médicament en cours</div>
      {{/if}}
    </td>
  </tr>
  <!-- A venir -->
  <tr class="text in_progress_after opacity-60">
    <td>
      {{assign var=is_first value=1}}
      {{assign var=total value=0}}
      {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
        {{if $_line->_debut_reel && $_line->_debut_reel > $date && $_line->_debut_reel <= $date_after}}
          {{if !$is_first}}
            &ndash;
          {{/if}}
          {{assign var=is_first value=0}}
          {{math equation="x+1" x=$total assign=total}}
          {{$_line->_ref_produit->libelle_abrege}}
        {{/if}}
      {{/foreach}}
      {{if $total == 0}}
        <div class="empty">Aucune ligne de médicament à venir</div>
      {{/if}}
    </td>
  </tr>
  {{/if}}

  {{if $prescription->_ref_prescription_line_mixes_by_type|@count}}
    {{foreach from=$prescription->_ref_prescription_line_mixes_by_type item=_lines_by_type key=chap}}
      <tr>
        <th>{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
      </tr>
      <tr class="hatching in_progress_before opacity-60">
        <td class="text">
          {{assign var=total value=0}}
          {{assign var=is_first value=1}}
          {{foreach from=$_lines_by_type item=_line}}
            {{if $_line->_fin && $_line->_fin < $date && $_line->_fin >= $date_before}}
              {{if !$is_first}}
                &ndash;
              {{/if}}
              {{assign var=is_first value=0}}
              {{math equation="x+1" x=$total assign=total}}
              {{$_line->_libelle_voie}}
              ({{$_line->_compact_view}})
            {{/if}}
          {{/foreach}}
          {{if $total == 0}}
            <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} passée</div>
          {{/if}}
        </td>
      </tr>
      <tr>
        <td class="text">
          {{assign var=total value=0}}
          {{assign var=is_first value=1}}
          {{foreach from=$_lines_by_type item=_line}}
            {{if ($_line->_fin && $_line->_fin >= $date) && ($_line->_debut && $_line->_debut <= $date)}}
              {{if !$is_first}}
                &ndash;
              {{/if}}
              {{assign var=is_first value=0}}
              {{math equation="x+1" x=$total assign=total}}

              <span {{if $_line->_fin|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
                {{if $_line->warning_day && $_line->_fin_relative == $_line->warning_day}}style="border-bottom: 2px solid orangered"{{/if}}
                    class="{{if $_line->highlight}}highlight_red{{/if}}"
                    onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
                          {{$_line->_libelle_voie}}
                ({{$_line->_compact_view}})
                {{if $_line->_alerte_antibio}}
                  <label title="Réévaluation antibiothérapie" style="font-weight: bold; color: red;">(Reeval ATB)</label>
                {{/if}}
                        </span>
            {{/if}}
          {{/foreach}}
          {{if $total == 0}}
            <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} en cours</div>
          {{/if}}
        </td>
      </tr>
      <tr class="in_progress_after opacity-60">
        <td class="text">
          {{assign var=total value=0}}
          {{assign var=is_first value=1}}
          {{foreach from=$_lines_by_type item=_line}}
            {{if $_line->_debut && $_line->_debut > $date && $_line->_debut <= $date_after}}
              {{if !$is_first}}
                &ndash;
              {{/if}}
              {{assign var=is_first value=0}}
              {{math equation="x+1" x=$total assign=total}}
              {{$_line->_libelle_voie}}
              ({{$_line->_compact_view}})
            {{/if}}
          {{/foreach}}
          {{if $total == 0}}
            <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} à venir</div>
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
  {{/if}}

  {{assign var=display_cat_for_elt value="dPprescription general display_cat_for_elt"|conf:"CGroups-$g"}}

  {{foreach from=$prescription->_ref_lines_elements_comments item=chap_element key=_chap_name}}
    <tr>
      <th>{{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}}</th>
    </tr>

    <tr class="hatching in_progress_before opacity-60">
      <td class="text">
        {{assign var=total value=0}}
        {{assign var=is_first_chap value=1}}
        {{assign var=count_elts value=0}}
        {{foreach from=$chap_element item=cat_element}}
          {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
            {{assign var=count_elts value=0}}
            &ndash;
          {{/if}}
          {{assign var=is_first_chap value=0}}
          {{assign var=is_first_cat value=1}}
          {{foreach from=$cat_element.element item=element}}
            {{if $element->_fin_reelle && $element->_fin_reelle < $date && $element->_fin_reelle >= $date_before}}
              {{if !$is_first_cat}}
                &ndash;
              {{elseif $display_cat_for_elt}}
                <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
              {{/if}}
              {{assign var=is_first_cat value=0}}
              {{math equation="x+1" x=$count_elts assign=count_elts}}
              {{math equation="x+1" x=$total assign=total}}
              {{$element->_view}}
            {{/if}}
          {{/foreach}}
        {{/foreach}}
        {{if $total == 0}}
          <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} passée</div>
        {{/if}}
      </td>
    </tr>

    <tr>
      <td class="text">
        {{assign var=total value=0}}
        {{assign var=is_first_chap value=1}}
        {{assign var=count_elts value=0}}
        {{foreach from=$chap_element item=cat_element}}
          {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
            {{assign var=count_elts value=0}}
            &ndash;
          {{/if}}
          {{assign var=is_first_chap value=0}}
          {{assign var=is_first_cat value=1}}
          {{foreach from=$cat_element.element item=element}}
            {{if ($element->_fin_reelle && $element->_fin_reelle >= $date) && ($element->_debut_reel && $element->_debut_reel <= $date)}}
              {{if !$is_first_cat}}
                &ndash;
              {{elseif $display_cat_for_elt}}
                <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
              {{/if}}
              {{assign var=is_first_cat value=0}}
              {{math equation="x+1" x=$total assign=total}}
              {{math equation="x+1" x=$count_elts assign=count_elts}}
              {{if $element->_fin_relative}}
                {{math assign=prolongation_time_day equation="x / 24" x="dPprescription general prolongation_time"|conf:"CGroups-$g"}}
                {{math assign=prescription_end_real equation="x - y" x=$element->_fin_relative y=$prolongation_time_day|intval}}
              {{/if}}
              <span {{if $element->_fin_reelle|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
                {{if $element->warning_day && (($element->duree && $element->_fin_relative == $element->warning_day) || (!$element->duree && $prescription_end_real == $element->warning_day))}}style="border-bottom: 2px solid orangered"{{/if}}
                    class="{{if $element->highlight}}highlight_red{{/if}}"
                    onmouseover="ObjectTooltip.createEx(this, '{{$element->_guid}}')">
                          {{$element->_view}}
                        </span>
            {{/if}}
          {{/foreach}}
        {{/foreach}}
        {{if $total == 0}}
          <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} en cours</div>
        {{/if}}
      </td>
    </tr>

    <tr class="in_progress_after opacity-60">
      <td class="text">
        {{assign var=total value=0}}
        {{assign var=is_first_chap value=1}}
        {{assign var=count_elts value=0}}
        {{foreach from=$chap_element item=cat_element}}
          {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
            {{assign var=count_elts value=0}}
            &ndash;
          {{/if}}
          {{assign var=is_first_chap value=0}}
          {{assign var=is_first_cat value=1}}
          {{foreach from=$cat_element.element item=element}}
            {{if $element->_debut_reel && $element->_debut_reel > $date && $element->_debut_reel <= $date_after}}
              {{if !$is_first_cat}}
                &ndash;
              {{elseif $display_cat_for_elt}}
                <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
              {{/if}}
              {{assign var=is_first_cat value=0}}
              {{math equation="x+1" x=$total assign=total}}
              {{math equation="x+1" x=$count_elts assign=count_elts}}
              {{$element->_view}}
            {{/if}}
          {{/foreach}}
        {{/foreach}}
        {{if $total == 0}}
          <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} à venir</div>
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
  {{else}}
  <tr>
    <td>
      Aucune ligne en cours
    </td>
  </tr>
  {{/if}}
  </table>
{{/if}}
{{if "forms"|module_active}}
  <table class="main tbl">
    <tr>
      <th>Formulaires</th>
    </tr>
  </table>
  <div id="list-ex_objects"></div>
{{/if}}
</td>
</tr>
</table>

<div id="modal-prescription-legend" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        <button class="cancel notext" onclick="modalPrescriptionLegend.close();" style="float: right"></button>
        {{tr}}Legend{{/tr}}
      </th>
    </tr>
    <tr>
      <td class="narrow">
        <span class="color-view" style="display: inline-block; vertical-align: top; padding: 0; margin: 0; width: 16px; height: 16px; border-bottom: 2px solid orange"></span>
      </td>
      <td>
        Fin de la prescription le jour même ou le lendemain
      </td>
    </tr>
    <tr>
      <td class="narrow">
        <span class="color-view" style="display: inline-block; vertical-align: top; padding: 0; margin: 0; width: 16px; height: 16px; border-bottom: 2px solid orangered"></span>
      </td>
      <td>
        Alerte paramétrée sur la fin de la prescription
      </td>
    </tr>
    <tr>
      <td>
        <span class="color-view highlight_red" style="display: inline-block; vertical-align: top; padding: 0; border: none; margin: 0;width: 16px; height: 16px;"></span>
      </td>
      <td>
        Ligne de prescription mise en évidence
      </td>
    </tr>
  </table>
</div>