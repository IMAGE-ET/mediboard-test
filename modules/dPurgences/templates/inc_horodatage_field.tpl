{{* $Id: inc_horodatage_field.tpl 6473 2009-06-24 15:18:19Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $object->$field}}
	<strong>{{mb_label object=$object field=$field}}</strong>
  <input type="hidden" name="ajax" value="1" />
  <input type="hidden" name="{{$field}}" value="{{$object->$field}}" />
  <input type="text" name="_{{$field}}_da" value="{{$object->$field|date_format:$conf.time}}" class="time" readonly="readonly"/>
  <input type="hidden" name="_{{$field}}" autocomplete="off" id="Horodatage-{{$rpu->_guid}}_{{$field}}" value="{{$object->$field|date_format:'%H:%M:%S'}}" class="time"
      onchange="$V(this.form.{{$field}}, '{{$object->$field|date_format:'%Y-%m-%d'}} ' + $V(this.form._{{$field}})); onSubmitFormAjax(this.form, {onComplete: function(){Horodatage.reload();}})"></input> 
  <button class="edit notext" type="button" onclick="Calendar.regField(this.form._{{$field}}); $(this).remove()">
    Modifier l'heure
  </button>
  <button class="cancel notext" type="button" onclick="this.form.{{$field}}.value=''; this.form.onsubmit();">
  	{{tr}}Cancel{{/tr}}
  </button>
{{else}}
  <input type="hidden" name="{{$field}}" value="{{$object->$field}}" />
  <button class="submit" type="button" onclick="this.form.{{$field}}.value='current'; this.form.onsubmit();">
  	{{tr}}{{$object->_class_name}}-{{$field}}{{/tr}}
  </button>
{{/if}}
  