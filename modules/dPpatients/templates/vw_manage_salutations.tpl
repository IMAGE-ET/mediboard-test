{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
Main.add(function() {
  var form = getForm('search_salutations');
  form.onsubmit();
});
</script>

<h2 style="text-align: center;">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
    {{tr}}{{$object->_class}}{{/tr}} : {{$object}}
  </span>
</h2>

<hr />

<form name="search_salutations" method="get" onsubmit="return onSubmitFormAjax(this, null, 'salutations_results')">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="a" value="ajax_manage_salutations" />
  <input type="hidden" name="object_id" value="{{$object->_id}}" />
  <input type="hidden" name="object_class" value="{{$object->_class}}" />
</form>

<div id="salutations_results"></div>