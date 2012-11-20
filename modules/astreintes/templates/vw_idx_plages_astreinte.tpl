{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="astreintes" script="plage"}}
<script type="text/javascript">

function raz(form) {
  $(form).clear(true);
	$V(form.elements.date_debut, "");
	$V(form.elements.date_fin, "");
}

</script>
<table class="main">
  <tr>
     <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">{{tr}}CPlageAstreinte-list{{/tr}}</th>
        </tr>
        <tr>
          {{*<th class="category">
          {{tr}}CMediusers-_user_last_name{{/tr}} {{tr}}CMediusers-_user_first_name{{/tr}}
          </th>*}}
          <th class="catergory">
            {{tr}}CPlageAstreinte-user{{/tr}}
          </th>
          <th class="category">
          {{tr}}CPlageAstreinte-libelle{{/tr}}
          </th>
          <th class="category">
          {{tr}}Date{{/tr}}
          </th>
        </tr>
        {{foreach from=$plages item=_plage}}
        <tr>
          <td>
            {{$_plage->_ref_user}}
          </td>
          <td>
            <a href="#" onclick="PlageAstreinte.edit('{{$_plage->_id}}','{{$_plage->user_id}}');">
            {{if $_plage->libelle}}{{$_plage->libelle}}{{else}}<em>{{tr}}CPlageAstreinte.noLibelle{{/tr}}</em>{{/if}}
            </a>
          </td>
          <td>
            {{if $_plage->date_debut == $_plage->date_fin}}
              {{$_plage->date_debut|date_format:$conf.longdate}}
            {{else}}
              {{$_plage->date_debut|date_format:$conf.longdate}} &rarr; {{$_plage->date_fin|date_format:$conf.longdate}}
            {{/if}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="2" class="empty">{{tr}}CMediusers.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
     </td>
		<td>
			  <div id = "edit_plage"></div>
		</td>
  </tr>
</table>
{{if $filter->user_id}}
  <script type='text/javascript'>
    Main.add( function() {
      PlageAstreinte.loadUser({{$filter->user_id}}, '{{$filter->_id}}');
      PlageAstreinte.edit('{{$filter->_id}}',{{$filter->user_id}});
    });
  </script>
{{/if}}