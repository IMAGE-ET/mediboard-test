{{*
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 *}}

{{mb_script module="pmsi"           script="PMSI"}}

{{mb_script module="ccam"           script="CCodageCCAM"}}
{{mb_script module="ccam"           script="code_ccam"}}

{{mb_script module="patients"       script="patient"}}
{{mb_script module="patients"       script="pat_selector"}}

{{mb_script module="hprim21"        script="pat_hprim_selector"}}
{{mb_script module="hprim21"        script="sejour_hprim_selector"}}

{{mb_script module="dPprescription" script="prescription"}}

{{mb_script module="dPcompteRendu"  script="document"}}
{{mb_script module="dPcompteRendu"  script="modele_selector"}}
{{mb_script module="files"          script="file"}}

<script>
  Main.add(function() {
    var tab = Control.Tabs.create('tabs-pmsi', true);
    tab.activeLink.up().onmousedown();
  });
</script>

<form name="dossier_pmsi_selector" action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <table class="form">
    <tr>
      <th class="title" colspan="4">
        Recherche de dossier
      </th>
    </tr>
    <tr>
      <th class="category halfPane" colspan="2">Champs de recherche</th>
      <th class="category halfPane">Sejours disponibles</th>
    </tr>
    <tr>
      <th>
        <label for="patient_id" title="Choisissez un patient">
          Patient
        </label>
      </th>
      <td>
        <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
        <input type="text" readonly="readonly" name="patient" value="{{$patient->_view}}" onclick="PatSelector.init()" />
        <button class="search notext compact" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
        <script>
          PatSelector.init = function(){
            this.sForm = "dossier_pmsi_selector";
            this.sId   = "patient_id";
            this.sView = "patient";
            this.pop();
          }
        </script>
      </td>
      <td>
        {{if $sejour->_id}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
          {{$sejour}}
        </span>
        {{else}}
          {{tr}}CSejour.none{{/tr}}
        {{/if}}
        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      </td>
    </tr>
    <tr>
      <th>
        <label for="NDA" title="Choisissez directement un numero de dossier">
          NDA
        </label>
      </th>
      <td>
        <input type="text" name="NDA" value="" />
        <button type="submit" class="search notext compact">{{tr}}Search{{/tr}}</button>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createDOM(this, 'list_sejours_pat')">
          {{$patient->_ref_sejours|@count}} séjour(s) disponible(s)
        </span>
        <div id="list_sejours_pat" style="display: none;">
          {{foreach from=$patient->_ref_sejours item=_sejour}}
            <input type="radio" name="_sejour_id" value="{{$_sejour->_id}}" {{if $_sejour->_id == $sejour->_id}}checked="checked"{{/if}}
                   onchange="PMSI.setSejour({{$_sejour->_id}});" />
            <span class="circled{{if $_sejour->_id == $sejour->_id}} ok{{/if}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
              {{$_sejour}}
            </span>
            <br />
            {{foreachelse}}
            <span>{{tr}}CSejour.none{{/tr}}</span>
          {{/foreach}}
        </div>
      </td>
    </tr>
  </table>
</form>

{{if $patient->_id}}

<div id="view_dossier_pmsi">
  {{*mb_include module=pmsi template=inc_view_dossier_pmsi*}}

  <ul id="tabs-pmsi" class="control_tabs">
    <li onmousedown="PMSI.loadPatient('{{$patient->_id}}', '{{$sejour->_id}}'); this.onmousedown='';">
      <a href="#tab-patient">{{tr}}PMSI.DossierPatient{{/tr}}</a>
    </li>
    <li onmousedown="PMSI.loadActes('{{$sejour->_id}}'); this.onmousedown=''">
      <a href="#tab-actes">{{tr}}CCodable-actes{{/tr}} et {{tr}}PMSI.Diagnostics{{/tr}}</a>
    </li>
    <li onmousedown="PMSI.loadDocuments('{{$sejour->_id}}'); this.onmousedown=''">
      <a href="#tab-documents">Documents</a>
    </li>
    {{if "dmi"|module_active}}
      <li onmousedown="PMSI.loadDMI('{{$sejour->_id}}'); this.onmousedown=''">
        <a href="#tab-dmi">{{tr}}CDMI{{/tr}}</a>
      </li>
    {{/if}}
    {{if "search"|module_active}}
      <li onmousedown="PMSI.loadSearch('{{$sejour->_id}}'); this.onmousedown=''">
        <a href="#tab-search">{{tr}}Search{{/tr}}</a>
      </li>
    {{/if}}
    <li style="float: right">
      <button type="button" class="print" onclick="PMSI.printDossierComplet('{{$sejour->_id}}');">
        Dossier complet
      </button>
    </li>
    {{if $sejour->_ref_prescription_sejour && $sejour->_ref_prescription_sejour->_id}}
      <li style="float: right">
        <button type="button" class="print" onclick="Prescription.printOrdonnance('{{$sejour->_ref_prescription_sejour->_id}}');">
          Prescription
        </button>
      </li>
    {{/if}}
    {{*<li><a href="#rss">{{tr}}PMSI.RSS{{/tr}}</a></li>*}}
  </ul>

  <div id="tab-patient" style="display:none;"></div>
  <div id="tab-actes" style="display: none;"></div>
  <div id="tab-documents" style="display: none;"></div>
  {{if "dmi"|module_active}}
    <div id="tab-dmi" style="display: none;"></div>
  {{/if}}

  {{if "search"|module_active}}
    <div id="tab-search" style="display: none;"></div>
  {{/if}}

  {{*<div id="rss" style="display: none;"></div>*}}
</div>

{{/if}}