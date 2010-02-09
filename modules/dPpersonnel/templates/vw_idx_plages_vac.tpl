{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function loadUser(user_id){
  
    var url = new Url("dPpersonnel", "ajax_plage_vac");
    url.addParam("user_id", user_id);
    url.requestUpdate("vw_user");
  
}

function editPlageVac(plage_id, user_id){
  
    var url = new Url("dPpersonnel", "ajax_edit_plage_vac");
    url.addParam("plage_id", plage_id);
		url.addParam("user_id", user_id);
    url.requestUpdate("edit_plage");
  
}

function raz(form) {
  $(form).clear(true);
	$V(form.elements.date_debut, "");
	$V(form.elements.date_fin, "");
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
       {{include file="inc_filtre_plage.tpl" }}
    </td>
		<td>
			<div id = "edit_plage"></div>
		</td>
  </tr>
  <tr>
    <td>
	    <table class="tbl">
        <tr>
	        <th class="title" colspan="2">{{tr}}CPlageVacances-list{{/tr}}</th>
	      </tr>
        <tr>
		      <th class="category">
	        {{tr}}CMediusers-_user_last_name{{/tr}} {{tr}}CMediusers-_user_first_name{{/tr}}
	        </th>
					<th class="category">
          {{tr}}CPlageVacances-corresponding{{/tr}}
          </th>
	      </tr>
        {{foreach from=$found_users item=mediuser}}
        <tr>
          <td>
            <a href="#{{$mediuser->_guid}}"
						onclick="loadUser({{$mediuser->_id}});
										 editPlageVac('',{{$mediuser->_id}})">
            	{{mb_include module=mediusers template=inc_vw_mediuser object=$mediuser}}</a>
          </td>
          <td>
          	{{assign var=_user_id value=$mediuser->_id}}
            {{$plages_per_user.$_user_id}}
          </td>
        </tr> 
        {{foreachelse}}
        <script type='text/javascript'>
          loadUser({{$filter->user_id}});
					editPlageVac('',{{$filter->user_id}});
        </script>
				<tr>
          <td colspan="2">{{tr}}CMediusers.none{{/tr}}</td>
        </tr>
        {{/foreach}}
	    </table>
	  </td>
		<td>
			<div id="vw_user"></div>
		</td>
  </tr>
</table>
{{if $_user_id}}
  <script type='text/javascript'>
    loadUser({{$_user_id}});
    editPlageVac('',{{$_user_id}});
  </script>
{{/if}}