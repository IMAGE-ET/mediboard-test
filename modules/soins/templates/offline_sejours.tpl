<style>
  @media print {
    div.modal_view {
      display: block !important;
      height: auto !important;
      width: 100% !important;
      font-size: 8pt !important;
      left: auto !important;
      top: auto !important;
      position: static !important;
    }
    table.table_print {
      page-break-after: always;
    }
    table {
      width: 100% !important;
      font-size: inherit; !important
    }
  }
  @media screen {
    div.modal_view {
      width: 700px;
      height: 500px;
      overflow-x: hidden;
      overflow-y: scroll;
    }
    thead {
      display: none;
    }
  }
</style>
<script type="text/javascript">
 // La div du dossier qui a été passé dans la fonction modal()
 // a du style supplémentaire, qu'il faut écraser lors de l'impression
 // d'un dossier seul.
  printOneDossier = function(sejour_id) {
    $("dossier-"+sejour_id).print();
  }
  
  togglePrintZone = function(name, sejour_id) {
    var dossier_soin = $("dossier-"+sejour_id);
    
    dossier_soin.select("."+name).invoke("toggleClassName", "not-printable");
    
    // Si un seul bloc est à imprimer, il faut retirer le style page-break.
    var patient = dossier_soin.select(".print_patient")[0];
    var sejour  = dossier_soin.select(".print_sejour")[1];
    var prescr  = dossier_soin.select(".print_prescription")[0];
    var task    = dossier_soin.select(".print_tasks")[0];
    var forms   = dossier_soin.select(".print_forms")[0];

    if (!patient.hasClassName("not-printable") && 
         sejour .hasClassName("not-printable") && 
         prescr .hasClassName("not-printable") && 
         task   .hasClassName("not-printable") && 
         forms  .hasClassName("not-printable")) {
      patient.setStyle({pageBreakAfter: "auto"});
    }
    else 
    if ( patient.hasClassName("not-printable") && 
        !sejour .hasClassName("not-printable") && 
         prescr .hasClassName("not-printable") && 
         task   .hasClassName("not-printable") && 
         forms  .hasClassName("not-printable")) {
      sejour.setStyle({pageBreakAfter: "auto"});
    }
    else 
    if ( patient.hasClassName("not-printable") &&
         sejour .hasClassName("not-printable") &&
        !prescr .hasClassName("not-printable") && 
         task   .hasClassName("not-printable") && 
         forms  .hasClassName("not-printable")) {
      prescr.setStyle({pageBreakAfter: "auto"});
    }
    else 
    if ( patient.hasClassName("not-printable") &&
         sejour .hasClassName("not-printable") &&
         prescr .hasClassName("not-printable") && 
        !task   .hasClassName("not-printable") && 
         forms  .hasClassName("not-printable")) {
      task.setStyle({pageBreakAfter: "auto"});
    }
    else {
      patient.setStyle({pageBreakAfter: "always"});
      sejour .setStyle({pageBreakAfter: "always"});
      prescr .setStyle({pageBreakAfter: "always"});
      task   .setStyle({pageBreakAfter: "always"});
    }
  }
  
  loadExForms = function(checkbox, sejour_id) {
    if (checkbox._loaded) return;
    
    // Indication du chargement
    $("forms-loading").setStyle({display: "inline-block"});
    $$("button.print").each(function(e){ e.disabled = true; });
    
    ExObject.loadExObjects("CSejour", sejour_id, "ex-objects", 3, null, {print: 1, onComplete: function(){
      $("forms-loading").hide();
      $$("button.print").each(function(e){ e.disabled = null; });
    }});
    
    checkbox._loaded = true;
  }
  
  resetPrintable = function(sejour_id) {
    var dossier_soin = $("dossier-"+sejour_id);
    dossier_soin.select(".print_patient")[0].removeClassName("not-printable").setStyle({pageBreakAfter: "always"});
    dossier_soin.select(".print_sejour")[1].removeClassName("not-printable").setStyle({pageBreakAfter: "always"});
    dossier_soin.select(".print_prescription")[0].removeClassName("not-printable").setStyle({pageBreakAfter: "always"});
    dossier_soin.select(".print_tasks")[0].removeClassName("not-printable").setStyle({pageBreakAfter: "auto"});
  }
</script>
<table class="tbl table_print">
  <tr>
    <th class="title" colspan="6">
      <button class="print not-printable" style="float: right;" onclick="window.print();">{{tr}}Print{{/tr}}</button>
      ({{$sejours|@count}}) Séjours du {{$date|date_format:$conf.longdate}} {{$hour|date_format:$conf.time}} - Service {{$service}}
    </th>
  </tr>
  <tr>
    <th>
      {{tr}}CSejour-patient_id{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-entree{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-sortie{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-libelle{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-praticien_id{{/tr}}
    </th>
    <th style="width: 1%">
    </th>
  </tr>
  {{foreach from=$sejours item=_sejour}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}')">
          {{$_sejour->_ref_patient->_view}}
        </span>
      </td>
      <td>
        {{mb_value object=$_sejour field=entree format=$conf.date}}
      </td>
      <td>
        {{mb_value object=$_sejour field=sortie format=$conf.date}}
      </td>
      <td>
        {{mb_value object=$_sejour field=libelle}}
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>
      <td>
        <button class="search" onclick="modal($('dossier-{{$_sejour->_id}}'))">Dossier soins</button>
        <button class="print notext" onclick="printOneDossier('{{$_sejour->_id}}')" title="Imprimer le dossier"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{foreach from=$dossiers_complets item=_dossier key=sejour_id name=dossier}}
  <div id="dossier-{{$sejour_id}}" class="modal modal_view" style="display: none;">
    {{$_dossier|smarty:nodefaults}}
  </div>
  {{if !$smarty.foreach.dossier.last}}
    <br style="page-break-after: always;"/>
  {{/if}}
{{/foreach}}