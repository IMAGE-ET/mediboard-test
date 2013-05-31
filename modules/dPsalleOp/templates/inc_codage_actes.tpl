{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="object" value=$subject}}

{{mb_include module=salleOp template=js_codage_ccam}}

<script type="text/javascript">
  loadActesNGAP = function(operation_id) {
    var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
    url.addParam("object_id", operation_id);
    url.addParam("object_class", "COperation");
    url.requestUpdate('listActesNGAP');
  }

  loadTarifsSejour = function(operation_id) {
    var url = new Url("dPsalleOp", "ajax_tarifs_operation");
    url.addParam("operation_id", operation_id);
    url.requestUpdate("tarif");
  }

  function reloadActes(operation_id, praticien_id) {
    if($('listActesNGAP')){
      loadActesNGAP(operation_id);
    }
    if($('ccam')){
      ActesCCAM.refreshList(operation_id, praticien_id);
    }
    if ($('tarif')) {
      loadTarifsSejour(operation_id);
    }
  }

  Main.add (function () {
    Control.Tabs.create('codage_tab_group', true);

    {{if $subject instanceof COperation}}
      if ($('tarif')) {
        loadTarifsSejour({{$subject->_id}});
      }
    {{/if}}
  });
</script>

<form name="patAldForm" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_patients_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="patient_id" value="">
  <input type="hidden" name="ald" value="">
  <input type="hidden" name="cmu" value="">
</form>

<ul id="codage_tab_group" class="control_tabs">
  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
    <li><a href="#ccam_tab">CCAM</a></li>
    <li><a href="#ngap_tab">NGAP</a></li>
    {{if $subject instanceof COperation}}
      {{assign var=sejour value=$subject->_ref_sejour}}
      <li style="float: right">
        <table class="narrow">
          <tr>
            <td id="tarif"></td>
            <td>
              <form name="editSejour" method="post" onsubmit="return onSubmitFormAjax(this)">
                <input type="hidden" name="m" value="planningOp">
                <input type="hidden" name="dosql" value="do_sejour_aed">
                <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}">
                {{mb_key object=$sejour}}
                <table class="main">
                  {{mb_include module=planningOp template=inc_check_ald patient=$subject->_ref_patient onchange="this.form.onsubmit()"}}
                </table>
              </form>
            </td>
          </tr>
        </table>
      </li>
    {{/if}}
  {{/if}}
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <li><a href="#tarmed_tab">TARMED</a></li>
    <li><a href="#caisse_tab">Caisses</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="ccam_tab" style="display:none">
  <div id="ccam">
    {{mb_include module=salleOp template=inc_codage_ccam}}
  </div>
</div>

<div id="ngap_tab" style="display:none">
  <div id="listActesNGAP">
    {{mb_include module=cabinet template=inc_codage_ngap}}
  </div>
</div>
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  <div id="tarmed_tab" style="display:none">
    <div id="listActesTarmed">
      {{mb_include module=tarmed template=inc_codage_tarmed}}
    </div>
  </div>
  <div id="caisse_tab" style="display:none">
    <div id="listActesCaisse">
      {{mb_include module=tarmed template=inc_codage_caisse}}
    </div>
  </div>
{{/if}}