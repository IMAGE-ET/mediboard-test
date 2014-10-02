{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  createDossier = function(sejour_id, grossesse_id, parturiente_id) {
    var url = new Url("dPplanningOp", "vw_edit_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("grossesse_id", grossesse_id);
    url.addParam("patient_id", parturiente_id);
    url.addParam("dialog", 1);

    url.modal({width: 1000, height: 700});
    url.modalObject.observe("afterClose", function() {
      getForm('filterDate').submit();
    });
  }
</script>

{{mb_script module=admissions script=admissions}}

<table class="tbl" id="admissions">
  <tr>
    <th class="title" colspan="10">
      <button class="new notext" onclick="createDossier();" style="float: left;">
        {{tr}}CGrossesse-title-create{{/tr}}
      </button>
      <button class="search notext" onclick="searchDossier();" style="float: left;">
        {{tr}}Rechercher{{/tr}}
      </button>
      {{$grossesses|@count}} terme(s) prévu(s) entre le {{$date_min|date_format:$conf.date}} et le {{$date_max|date_format:$conf.date}}
    </th>
  </tr>
  <tr>
    <th class="category">{{mb_title class=CGrossesse field=terme_prevu}}</th>
    <th class="category">{{mb_title class=CGrossesse field=parturiente_id}}</th>
    <th class="category narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
    </th>
    <th class="category">Séjours / consults</th>
    <th class="narrow">Actions</th>
  </tr>
  {{foreach from=$grossesses item=_grossesse}}
    <tr>
      <td style="width: 8%">
        {{$_grossesse->terme_prevu|date_format:$conf.date}}
      </td>
      <td colspan="2" style="width: 15%">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_guid}}')" class="CPatient-view">
          {{$_grossesse->_ref_parturiente}}
        </span>
      </td>
      <td class="text">
        {{foreach from=$_grossesse->_ref_sejours item=_sejour}}
          {{if !$_sejour->annule}}
            <span class="{{if $_sejour->sortie_reelle}}hatching{{/if}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
              {{$_sejour}}
            </span>
            <br />
          {{/if}}
        {{/foreach}}
        {{assign var=consult value=$_grossesse->_ref_last_consult_anesth}}
        {{if $consult->_id}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$consult->_ref_consult_anesth->_guid}}')">
            {{$consult}} du {{$consult->_ref_plageconsult->date|date_format:$conf.date}}
          </span>
        {{/if}}
      </td>
      <td class="button">
        <button class="new notext" title="{{tr}}CSejour-title-create{{/tr}}"
                onclick="editSejour(0, '{{$_grossesse->_id}}', '{{$_grossesse->parturiente_id}}');">
        </button>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="10">{{tr}}CGrossesse.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
