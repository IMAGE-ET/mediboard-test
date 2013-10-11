{{* $Id: vw_aed_rpu.tpl 8113 2010-02-22 09:29:33Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8113 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$consult->_ref_patient}}

<table class="form">
  <tr>
    <th class="title" colspan="2">
      <a style="float: left" href="?m=patients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{mb_include module=patients template=inc_vw_photo_identite size=42}}
      </a>

      <h2 style="color: #fff; font-weight: bold;">
        {{$patient}}
        {{if isset($sejour|smarty:nodefaults)}}
          <span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
        {{/if}}
      </h2>
    </th>
  </tr>
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
        <div id="files-CSejour">
          <script type="text/javascript">
            File.register('{{$sejour->_id}}','{{$sejour->_class}}', 'files-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
        <div id="documents-CSejour">
          <script type="text/javascript">
            Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->_praticien_id}}','documents-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>

  {{if $consult->_id}}
    <tr>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CFile{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>
          <div id="files-CConsultation">
            <script type="text/javascript">
              File.register('{{$consult->_id}}','{{$consult->_class}}', 'files-CConsultation');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>
          <div id="documents-CConsultation">
            <script type="text/javascript">
              Document.register('{{$consult->_id}}','{{$consult->_class}}','{{$consult->_praticien_id}}','documents-CConsultation');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  {{else}}
    <tr>
      <td colspan="2">
        <div class="small-info">Consultation non réalisée</div>
      </td>
    </tr>
  {{/if}}
</table>