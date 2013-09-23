{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{unique_id var=change_heure}}
{{if $rpu->$debut && !$rpu->$fin}}
	<form name="editRPU{{$change_heure}}" action="" method="post" 
	 onsubmit="return onSubmitFormAjax(this, {onComplete : refreshAttente.curry('{{$debut}}', '{{$fin}}', '{{$rpu->_id}}') })">
		<input type="hidden" name="dosql" value="do_rpu_aed" />
		<input type="hidden" name="del" value="0" />
		<input type="hidden" name="m" value="dPurgences" />
		<input type="hidden" name="{{$fin}}" value="" />
		{{mb_key object=$rpu}}
		
		<button class="submit" type="submit" onclick="this.form.{{$fin}}.value='current';">
		{{tr}}{{$rpu->_class}}-{{$fin}}{{/tr}}
		</button>
	</form>
{{elseif $rpu->$fin}}
  <form name="editHeure{{$change_heure}}" method="post" action="?">
    {{mb_key object=$rpu}}
    <input type="hidden" name="m" value="dPurgences" />
    <input type="hidden" name="dosql" value="do_rpu_aed" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="{{$fin}}" value="" />
    <input type="text" name="_fin_da" value="{{$rpu->$fin|date_format:$conf.time}}" class="time" readonly="readonly"/>
    <input type="hidden" name="_fin" autocomplete="off" id="editHeure{{$change_heure}}_fin" value="{{$rpu->$fin|iso_time}}" class="time"
    onchange="$V(this.form.{{$fin}}, '{{$rpu->$fin|iso_date}} ' + $V(this.form._fin)); onSubmitFormAjax(this.form, {onComplete:refreshAttente.curry('{{$debut}}', '{{$fin}}', '{{$rpu->_id}}')})" />
    <button class="edit notext" type="button" onclick="Calendar.regField(this.form._fin); $(this).remove()">
      Modifier l'heure
    </button>
  </form>
{{/if}}