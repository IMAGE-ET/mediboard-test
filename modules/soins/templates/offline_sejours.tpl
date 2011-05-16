<style>
  @media print {
    div.modal_view {
      display: block !important;
      height: auto !important;
      width: 100% !important;
      page-break-before: always;
    }
    table.table_print {
      page-break-after: always;
    }
  }
</style>

<table class="tbl table_print">
  <tr>
    <th class="title" colspan="6">
      <button class="print not-printable" style="float: right;" onclick="window.print();">{{tr}}Print{{/tr}}</button>
      Séjours du {{$date|date_format:$conf.longdate}}
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
    <th>
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
      </td>
    </tr>
  {{/foreach}}
</table>

{{foreach from=$dossiers_complets item=_dossier key=sejour_id}}
  <div id="dossier-{{$sejour_id}}" class="modal_view" style="display: none; width: 700px; height: 500px;">
    <div style="overflow-x: hidden; overflow-y: scroll; height: 100%">
    {{$_dossier|smarty:nodefaults}}
    </div>
  </div>
{{/foreach}}