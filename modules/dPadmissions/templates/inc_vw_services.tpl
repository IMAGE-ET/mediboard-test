{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
if(!self.changeServiceId) {
  changeServiceId = function(oForm) {return false;}
}
</script>

{{if $services|@count}}
	<select name="service_mutation_id" onchange="changeServiceId(this.form);">
		<option value="">&mdash; Serv. de mutation</option>
		{{foreach from=$services item="_service"}}
		<option value="{{$_service->_id}}" {{if $_service->_id == $serviceSelected}}selected="selected"{{/if}}>
		  {{$_service->_view}}
		</option>
		{{/foreach}}
	</select>
{{/if}}