{{* $Id: inc_horodatage_field.tpl 6473 2009-06-24 15:18:19Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=value value=$object->$field}}
{{assign var=field_id value="`$object->_guid`-$field"}}

{{if $value}}
  <strong>{{mb_label object=$object field=$field}}</strong>
  
  <span id="{{$field_id}}-value">
    {{mb_value object=$object field=$field date=$rpu->_ref_sejour->entree|iso_date}}
  </span>

  <span id="{{$field_id}}-field" style="display: none;">
    {{mb_field object=$object field=$field form="Horodatage-`$object->_guid`" register=true onchange="this.form.onsubmit();"}}
  </span>

  <button class="edit notext" type="button" onclick="$('{{$field_id}}-value').hide(); $('{{$field_id}}-field').show(); $(this).hide();">
    {{tr}}Modify{{/tr}}
  </button>
  
  <button class="cancel notext" type="button" onclick="this.form.{{$field}}.value=''; this.form.onsubmit();">
    {{tr}}Cancel{{/tr}}
  </button>
  
{{else}}

  <input type="hidden" name="{{$field}}" value="" />
  <button class="submit" type="button" onclick="this.form.{{$field}}.value='current'; this.form.onsubmit();">
    {{tr}}{{$object->_class}}-{{$field}}{{/tr}}
  </button>

{{/if}}
  