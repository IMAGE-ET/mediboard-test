{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=prescription_sejour_id value=""}}
{{if $sejour->_ref_prescription_sejour}}
  {{assign var=prescription_sejour_id value=$sejour->_ref_prescription_sejour->_id}}
{{/if}}

{{mb_script module="patients" script="antecedent" ajax=true}}
{{mb_default var=type_see value=""}}

<script>
Traitement = {
  prescription_sejour_id: {{$prescription_sejour_id|@json}},
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'ce traitement',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  },  
  cancel: function(oForm, onComplete) {
    $V(oForm.annule, 1);
    onSubmitFormAjax(oForm, {
      onComplete: function(){
        if (onComplete) {
          onComplete();
        }
      }
    });
    $V(oForm.annule, '');
  },
  restore: function(oForm, onComplete) {
    $V(oForm.annule, '0');
    onSubmitFormAjax(oForm, {onComplete: function(){
      if (onComplete) {
        onComplete();
      }
    }});
    $V(oForm.annule, '');
  },
  toggleCancelled: function(list) {
    $(list).select('.cancelled').invoke('toggle');
  },
  copyLine: function(prescription_id) {
    this.prescription_sejour_id = prescription_id;
    var oFormTransfert = getForm("transfert_line_TP-{{$patient->_id}}");

    $V(oFormTransfert.prescription_id, prescription_id);
    onSubmitFormAjax(oFormTransfert, {onComplete: DossierMedical.reloadDossierSejour});
  }
};

showModalTP = function() {
  window.modalUrlTp = new Url("prescription", "ajax_vw_traitements_personnels");
  window.modalUrlTp.addParam("object_guid", '{{$sejour->_guid}}');
  window.modalUrlTp.addParam("refresh_prescription", true);
  window.modalUrlTp.addParam("dossier_anesth_id", "{{$dossier_anesth_id}}");
  window.modalUrlTp.requestModal("80%", "80%", {
    onClose: function() {
      if (window.DossierMedical) {
        window.DossierMedical.reloadDossiersMedicaux();
      }
      if (window.tab_sejour) {
        window.tab_sejour.setActiveTab("prescription_sejour");
      }
      if (window.tabsConsultAnesth) {
        window.tabsConsultAnesth.setActiveTab("prescription_sejour");
      }
    Prescription.reloadPrescSejour(null, '{{$sejour->_id}}');
  } });
}
</script>

<!--  Formulaire de création de prescription si inexistante -->
<form name="prescription-sejour-{{$patient->_id}}" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="type" value="sejour" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
  <input type="hidden" name="ajax" value="1" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="Traitement.copyLine" />
</form>

<!--  Formulaire de duplication de traitement -->
<form name="transfert_line_TP-{{$patient->_id}}" action="?" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_transfert_line_tp_aed" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="debut" value="{{$sejour->entree|date_format:'%Y-%m-%d'}}" />
  <input type="hidden" name="prescription_id" value="{{$prescription_sejour_id}}" />
</form>

{{if !$type_see || $type_see == "antecedent"}}
  {{if $dossier_medical->_count_cancelled_antecedents}}
    <button class="search" style="float: right" onclick="Antecedent.toggleCancelled('antecedents-{{$dossier_medical->_guid}}')">
      Afficher les {{$dossier_medical->_count_cancelled_antecedents}} antécédents annulés
    </button>
  {{/if}}
  <button class="vslip" style="float:right" onclick="DossierMedical.toggleSortAntecedent('{{$type_see}}')">Classer {{if $sort_by_date}}par type{{else}}par date{{/if}}</button>
  <strong {{if $dossier_medical->_count_cancelled_antecedents}}style="line-height: 22px;"{{/if}}>Antécédents (par {{if $sort_by_date}}Date{{else}}Type/Appareil{{/if}})</strong>

  <ul id="antecedents-{{$dossier_medical->_guid}}">
    {{if $dossier_medical->_count_antecedents || $dossier_medical->_count_cancelled_antecedents}}
      {{foreach from=$dossier_medical->_all_antecedents item=_antecedent}}
      <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
        <form name="Del-{{$_antecedent->_guid}}" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_antecedent_aed" />
          {{mb_key object=$_antecedent}}

          <input type="hidden" name="annule" value="" />

          <!-- Seulement si l'utilisateur est le créateur -->
          {{if $_antecedent->owner_id == $app->user_id}}
          <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="
            Antecedent.remove(this.form, function() {
              if (window.DossierMedical) {
                DossierMedical.reloadDossierPatient(null, '{{$type_see}}');
              }
              if (window.reloadAtcd) {
                reloadAtcd();
              }
            })">
            {{tr}}Delete{{/tr}}
          </button>
          {{/if}}

          {{if $_is_anesth && $sejour->_id}}
          <button class="add notext" type="button" onclick="copyAntecedent({{$_antecedent->_id}})">
            {{tr}}Add{{/tr}} comme élément significatif
          </button>
          {{/if}}
        </form>

        <span onmouseover="ObjectTooltip.createEx(this, '{{$_antecedent->_guid}}')">
          <strong>
            {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
            {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
          </strong>
          {{if $_antecedent->date}}
            {{mb_value object=$_antecedent field=date}} :
          {{/if}}
          {{$_antecedent->rques|nl2br}}
        </span>
      </li>
      {{/foreach}}
    {{else}}
      <li class="empty">{{tr}}CAntecedent.unknown{{/tr}}</li>
    {{/if}}
  </ul>

  {{if $sejours|@count}}
    <hr style="width: 50%;" />
    <strong>Motif des séjours précédents</strong>
    <ul>
      {{foreach from=$sejours item=_sejour}}
        {{if $_sejour->_motif_complet != "[Att] "}}
          <li>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">{{$_sejour->_motif_complet|nl2br}}</span>
          </li>
        {{/if}}
      {{/foreach}}
    </ul>
  {{/if}}
{{/if}}

{{if !$type_see || $type_see == "traitement"}}
  {{assign var=display value="none"}}
  {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count != $dossier_medical->_count_cancelled_traitements)}}
    {{assign var=display value="inline"}}
  {{elseif $dossier_medical->absence_traitement}}
    <script>
      Main.add(function(){
        var form = getForm("save_absence_ttt");
        $V(form.absence_traitement, "0");
      });
    </script>
  {{/if}}
  <form name="save_absence_ttt" action="?" method="post" onsubmit="return onSubmitFormAjax(this);" style="float: right;display: {{$display}}">
    {{mb_key   object=$dossier_medical}}
    <input type="hidden" name="m"     value="patients" />
    <input type="hidden" name="del"     value="0" />
    <input type="hidden" name="dosql"     value="do_dossierMedical_aed" />
    <input type="hidden" name="object_id" value="{{$patient->_id}}" />
    <input type="hidden" name="object_class" value="{{$patient->_class}}" />
    {{mb_label object=$dossier_medical field=absence_traitement}}
    {{mb_field object=$dossier_medical field=absence_traitement typeEnum=checkbox onchange="return onSubmitFormAjax(this.form);"}}
  </form>

  <!-- Traitements -->
  {{if is_array($dossier_medical->_ref_traitements) || $dossier_medical->_ref_prescription}}
    {{if $dossier_medical->_count_cancelled_traitements}}
      <button class="search" style="float: right;" onclick="Traitement.toggleCancelled('traitements-{{$dossier_medical->_guid}}')">
        Afficher les {{$dossier_medical->_count_cancelled_traitements}} traitements stoppés
      </button>
    {{/if}}

    {{if $sejour->_id && ($app->_ref_user->isPraticien() || $app->_ref_user->isSageFemme() || !"dPprescription general role_propre"|conf:"CGroups-$g")}}
      <button class="tick" type="button" style="float: right" onclick="showModalTP();">
        Gestion des traitements personnels ({{$sejour->_ref_prescription_sejour->_count_lines_tp}}/{{if $dossier_medical->_ref_prescription}}{{$dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}{{else}}0{{/if}})
      </button>
    {{/if}}

    <strong>Traitements personnels</strong>
  {{/if}}

  <div id="traitements-{{$dossier_medical->_guid}}">
  {{if $dossier_medical->_ref_prescription}}
    <ul>
    {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}
      <li {{if $_line->_stopped}}class="cancelled" style="display: none;"{{/if}}>
        <form name="delTraitementDossierMedPat-{{$_line->_id}}"  action="?" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
          <input type="hidden" name="prescription_line_medicament_id" value="{{$_line->_id}}" />

          {{if $_line->creator_id == $app->user_id}}
          <button class="trash notext" type="button" onclick="Traitement.remove(this.form, function() {DossierMedical.reloadDossierPatient(null, '{{$type_see}}');})">
            {{tr}}Delete{{/tr}}
          </button>
          {{/if}}

          {{mb_include module=system template=inc_interval_date from=$_line->debut to=$_line->fin}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
            <a href="#1" onclick="Prescription.viewProduit(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
              {{$_line->_ucd_view}}
            </a>
          </span>

          <span class="compact" style="display: inline;">
            {{$_line->commentaire}}
            {{if $_line->_ref_prises|@count}}
              <br />
              ({{foreach from=`$_line->_ref_prises` item=_prise name=foreach_prise}}
              {{$_prise}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
              {{/foreach}})
            {{/if}}

            {{if $_line->long_cours}}
              (Long cours)
            {{/if}}
          </span>
        </form>
      </li>
    {{/foreach}}
    {{if $dossier_medical->_ref_prescription->_ref_prescription_lines_element|@count}}
      <hr style="width: 50%;" />
    {{/if}}
      {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines_element item=_line}}
        <li>
          <form name="delTraitementDossierElemsPat-{{$_line->_id}}"  action="?" method="post">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
            <input type="hidden" name="prescription_line_element_id" value="{{$_line->_id}}" />

            {{if $_line->creator_id == $app->user_id}}
              <button class="trash notext" type="button" onclick="Traitement.remove(this.form, function() {DossierMedical.reloadDossierPatient(null, '{{$type_see}}');})">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}
            {{mb_include module=system template=inc_interval_date from=$_line->debut to=$_line->fin}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
              {{$_line->_view}}
            </span>
            <span class="compact" style="display: inline;">
              {{$_line->commentaire}}
              {{if $_line->_ref_prises|@count}}
                <br />
                ({{foreach from=`$_line->_ref_prises` item=_prise name=foreach_prise}}
                  {{$_prise}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
                {{/foreach}})
              {{/if}}
            </span>
          </form>
        </li>
      {{/foreach}}
    </ul>
    {{if $dossier_medical->_ref_traitements|@count && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}
    <hr style="width: 50%;" />
    {{/if}}
  {{/if}}


  {{if is_array($dossier_medical->_ref_traitements)}}
  <ul>
    {{foreach from=$dossier_medical->_ref_traitements item=_traitement}}
    <li {{if $_traitement->annule}}class="cancelled" style="display: none;"{{/if}}>
      <form name="delTrmtFrm-{{$_traitement->_id}}" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      {{mb_key object=$_traitement}}

      {{if $_traitement->owner_id == $app->user_id}}
      <button class="trash notext" type="button" onclick="Traitement.remove(this.form, function() {DossierMedical.reloadDossierPatient(null, '{{$type_see}}');})">
        {{tr}}delete{{/tr}}
      </button>
      {{/if}}

      {{mb_include module=system template=inc_interval_date_progressive object=$_traitement from_field=debut to_field=fin}}

      <span onmouseover="ObjectTooltip.createEx(this, '{{$_traitement->_guid}}')">
        {{$_traitement->traitement|nl2br}}
      </span>

      </form>
    </li>
    {{foreachelse}}
    {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
    <li class="empty">{{tr}}CTraitement.unknown{{/tr}}</li>
    {{/if}}
    {{/foreach}}
  </ul>
  {{/if}}
  </div>
{{/if}}

{{if !$type_see || $type_see == "cim"}}
  <strong>Diagnostics CIM</strong>
  <ul>
    {{foreach from=$dossier_medical->_ext_codes_cim item=_code}}
    <li>
      <button class="trash notext" type="button" onclick="oCimField.remove('{{$_code->code}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{if $_is_anesth || $sejour->_id}}
      <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$_code->code}}')">
        {{tr}}Add{{/tr}}
      </button>
      {{/if}}
      {{if "vidal"|module_active && $conf.dPmedicament.base == "vidal"}}
        {{mb_include module=vidal template=inc_button_reco_cim code_cim=$_code->code}}
      {{/if}}

      {{$_code->code}}: {{$_code->libelle}}
    </li>
    {{foreachelse}}
    <li class="empty">{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</li>
    {{/foreach}}
  </ul>

  <!-- Gestion des diagnostics pour le dossier medical du patient -->
  <form name="editDiagFrm" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="tab" value="edit_consultation" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
    <input type="hidden" name="object_id" value="{{$patient->_id}}" />
    <input type="hidden" name="object_class" value="CPatient" />
    <input type="hidden" name="codes_cim" value="{{$dossier_medical->codes_cim}}" />
  </form>

  <script>
  // FIXME : Modifier le tokenfield, car deux appels à onchange
  Main.add(function(){
    var form = getForm("editDiagFrm");

    // form may be undefined if the page is changed while loading
    if (form) {
      oCimField = new TokenField(form.codes_cim, {
        confirm  : 'Voulez-vous réellement supprimer ce diagnostic ?',
        onChange : updateTokenCim10
      });
    }
    {{if $dossier_medical->_id}}
      if (window.tabsConsult || window.tabsConsultAnesth) {
        var count_tab = '{{math equation=w+x+y+z
          w=$dossier_medical->_all_antecedents|@count
          x=$dossier_medical->_ref_traitements|@count
          y=$dossier_medical->_ext_codes_cim|@count
          z=$dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}';
        Control.Tabs.setTabCount("AntTrait", count_tab);
      }
    {{/if}}
  });
  </script>
{{/if}}