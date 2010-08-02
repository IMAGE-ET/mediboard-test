{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  <li>Du patient
  <ul>
    {{foreach from=$patient->_ref_dossier_medical->_ext_codes_cim item=curr_code}}
    <li>
        <form name="addCim-{{$sejour->_id}}-{{$curr_code->code}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_added_code_cim" value="{{$curr_code->code}}" />
        <button class="add notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { reloadDiagnostic({{$sejour->_id}}, 1) } })">
          {{tr}}Add{{/tr}}
        </button>
        </form>
      {{$curr_code->code}} : {{$curr_code->libelle}}
    </li>
    {{foreachelse}}
    <li>{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</li>
    {{/foreach}}
  </ul>
  </li>
  <li>Significatifs du séjour
  <ul>
    {{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item=curr_code}}
    <li>
      {{$curr_code->code}} : {{$curr_code->libelle}}
    </li>
    {{foreachelse}}
    <li>{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</li>
    {{/foreach}}
  </ul>
  </li>
</ul>