{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{assign var=datetime value='CMbDT::dateTime'|static_call:null}}
{{assign var=date value='CMbDT::date'|static_call:null}}

<script type="text/javascript">
  callbackDevis = function(devis_id) {
    DevisCodage.edit(devis_id, '{{$object->_class}}', '{{$object->_id}}');
  }
</script>

<form name="createDevis" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="callback" value="callbackDevis"/>
  <input type="hidden" name="class" value="CDevisCodage"/>
  <input type="hidden" name="m" value="ccam"/>
  <input type="hidden" name="dosql" value="do_devis_codage_aed"/>
  <input type="hidden" name="devis_codage_id" value=""/>
  <input type="hidden" name="codable_class" value="{{$object->_class}}"/>
  <input type="hidden" name="codable_id" value="{{$object->_id}}"/>
  <input type="hidden" name="patient_id" value="{{$object->patient_id}}"/>
  <input type="hidden" name="praticien_id" value="{{$object->_ref_praticien->_id}}"/>
  <input type="hidden" name="creation_date" value="{{$datetime}}"/>
  <input type="hidden" name="date" value="{{$date}}"/>

  <button type="submit" class="new">
    {{tr}}CDevisCodage-title-create{{/tr}}
  </button>
</form>