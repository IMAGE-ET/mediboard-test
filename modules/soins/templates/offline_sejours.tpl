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

<script>
 // La div du dossier qui a été passé dans la fonction Modal.open()
 // a du style supplémentaire, qu'il faut écraser lors de l'impression
 // d'un dossier seul.
  printOneDossier = function(sejour_id) {
    Element.print($("dossier-"+sejour_id).childElements());
  }
</script>

<table class="tbl table_print">
  <tr>
    <th class="title" colspan="7">
      <button class="print not-printable" style="float: right;" onclick="window.print();">{{tr}}Print{{/tr}}</button>
      ({{$sejours|@count}}) Séjours du {{$date|date_format:$conf.longdate}} {{$hour|date_format:$conf.time}} - Service {{$service}}
    </th>
  </tr>
  <tr>
    <th>
      {{tr}}CAffectation-lit_id{{/tr}}
    </th>
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
        {{if $conf.soins.show_only_lit_bilan}}
          {{$_sejour->_ref_curr_affectation->_ref_lit->_shortview}}
        {{else}}
          {{$_sejour->_ref_curr_affectation->_ref_lit}}
        {{/if}}
      </td>
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
        <button class="search" onclick="Modal.open($('dossier-{{$_sejour->_id}}'), {width: 1000, height: 800})">Dossier soins</button>
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
    <hr style="border: 0; page-break-after: always;"/>
  {{/if}}
{{/foreach}}