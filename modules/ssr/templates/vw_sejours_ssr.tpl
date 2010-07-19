{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=sejours_ssr}}

{{if $dialog}}
  {{mb_include style=mediboard template=open_printable}}
{{else}}
	{{if $modules.dPplanningOp->_can->edit}} 
	<a class="button new" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id=0">
	  Admettre un patient
	</a>
	{{/if}}

	<button type="button" class="print" style="float: right;" onclick="new Url().setModuleAction('{{$m}}', '{{$action}}').popup(800, 600);">
	  {{tr}}Print{{/tr}}
	</button>
{{/if}}
	
<form class="not-printable" name="Filter" action="?" method="get" style="float: right;">
  <input name="m" value="{{$m}}" type="hidden" />
  <input name="{{$actionType}}" value="{{$action}}" type="hidden" />
  <input name="dialog" value="{{$dialog}}" type="hidden" />

  <input name="service_id"   value="{{$filter->service_id}}"   type="hidden" onchange="this.form.submit()" />
  <input name="praticien_id" value="{{$filter->praticien_id}}" type="hidden" onchange="this.form.submit()" />
  <input name="referent_id"  value="{{$filter->referent_id}}"  type="hidden" onchange="this.form.submit()" />

	{{if $dialog}} 
	<label for="group_by">
		Regrouper par kiné
	</label>
  <input type="checkbox" name="group_by" value="1" {{if $group_by}} checked="checked" {{/if}} onclick="this.form.submit();">
	{{/if}}

  Prescription
  <select name="show" onchange="this.form.submit();">
    <option value="all"     {{if $show == "all"    }} selected="selected"{{/if}}>Tous les séjours</option>
    <option value="nopresc" {{if $show == "nopresc"}} selected="selected"{{/if}}>Séjours sans prescription</option>
  </select>
</form>

{{if $group_by}} 
  {{foreach from=$kines item=_kine}}
  	{{assign var=kine_id value=$_kine->_id}}
    <h1>{{$_kine}}</h1>
    {{mb_include template=inc_sejours_ssr sejours=$sejours_by_kine.$kine_id}}
  {{/foreach}}   

  {{assign var=kine_id value=""}}
  <h1>
  	<em>
  		&mdash; {{tr}}None{{/tr}} {{tr}}CBilanSSR-kine_id{{/tr}}
		</em>
	</h1>
  {{mb_include template=inc_sejours_ssr sejours=$sejours_by_kine.$kine_id}}
{{else}}
{{mb_include template=inc_sejours_ssr sejours=$sejours}}
{{/if}}

{{if $dialog}}
  {{mb_include style=mediboard template=close_printable}}
{{/if}}
