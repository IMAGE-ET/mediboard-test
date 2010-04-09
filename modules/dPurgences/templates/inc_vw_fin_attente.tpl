{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $rpu->$debut && !$rpu->$fin}}
	<form name="editRPU-{{$fin}}-{{$rpu->_id}}" action="" method="post" 
	 onsubmit="return onSubmitFormAjax(this, {onComplete : refreshAttente.curry('{{$debut}}', '{{$fin}}', '{{$rpu->_id}}') })">
		<input type="hidden" name="dosql" value="do_rpu_aed" />
		<input type="hidden" name="del" value="0" />
		<input type="hidden" name="m" value="dPurgences" />
		<input type="hidden" name="{{$fin}}" value="" />
		{{mb_key object=$rpu}}
		
		<button class="submit" type="submit" onclick="this.form.{{$fin}}.value='current';">
		{{tr}}{{$rpu->_class_name}}-{{$fin}}{{/tr}}
		</button>
	</form>
{{else}}
  {{$rpu->$fin|date_format:$dPconfig.time}}
{{/if}}