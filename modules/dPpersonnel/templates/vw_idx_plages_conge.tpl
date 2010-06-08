{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPpersonnel" script="plage"}}
<script type="text/javascript">

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
	        <th class="title" colspan="2">{{tr}}CPlageConge-list{{/tr}}</th>
	      </tr>
        <tr>
		      <th class="category">
	        {{tr}}CMediusers-_user_last_name{{/tr}} {{tr}}CMediusers-_user_first_name{{/tr}}
	        </th>
					<th class="category">
          {{tr}}CPlageConge-corresponding{{/tr}}
          </th>
	      </tr>
        {{foreach from=$found_users item=mediuser}}
        <tr id="u{{$mediuser->_id}}" {{if $filter->user_id == $mediuser->_id}} class="selected" {{/if}}>
          <td>
            <a href="#{{$mediuser->_guid}}"
						onclick="loadUser({{$mediuser->_id}}, '');
										   editPlageConge('',{{$mediuser->_id}});
										 ">
            	{{mb_include module=mediusers template=inc_vw_mediuser object=$mediuser}}</a>
          </td>
          <td>
          	{{assign var=_user_id value=$mediuser->_id}}
            {{$plages_per_user.$_user_id}}
          </td>
        </tr> 
        {{foreachelse}}
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
{{if $filter->user_id}}
  <script type='text/javascript'>
    Main.add( function() {
      loadUser({{$filter->user_id}}, '{{$filter->_id}}');
      editPlageConge('{{$filter->_id}}',{{$filter->user_id}});
    });
  </script>
{{/if}}