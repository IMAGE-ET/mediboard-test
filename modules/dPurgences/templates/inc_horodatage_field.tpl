{{* $Id: inc_vw_radio.tpl 6473 2009-06-24 15:18:19Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<input type="hidden" name="{{$field}}" value="{{$object->$field}}" />
{{if $object->$field}}
	<strong>{{mb_label object=$object field=$field}}</strong>
  {{$object->$field|date_format:$dPconfig.time}}
  <button class="cancel notext" type="button" onclick="this.form.{{$field}}.value=''; this.form.onsubmit();">
  	{{tr}}Cancel{{/tr}}
  </button>
{{else}}
  <button class="submit" type="button" onclick="this.form.{{$field}}.value='current'; this.form.onsubmit();">
  	{{tr}}{{$object->_class_name}}-{{$field}}{{/tr}}
  </button>
{{/if}}
  