{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="astreintes" script="plage"}}
<script>

function raz(form) {
  $(form).clear(true);
	$V(form.elements.start, "");
	$V(form.elements.end, "");
}

</script>
<button class="new" type="button" onclick="PlageAstreinte.edit('','')">
{{tr}}CPlageAstreinte-title-create{{/tr}}
</button>
<table class="main">
  <tr>
     <td>
        <table class="tbl">
          <tr>
            <th class="title" colspan="5">{{tr}}CPlageAstreinte-list{{/tr}}</th>
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
            <th class="category">
              {{tr}}Duration{{/tr}}
            </th>
            <th class="category">
              {{tr}}CPlageAstreinte-type{{/tr}}
            </th>
          </tr>
          {{foreach from=$plages item=_plage}}
            {{assign var=class value=""}}
            {{if $_plage->start <= $today && $_plage->end >= $today}}{{assign var=class value="highlight"}}{{/if}}
            <tr>
              <td class="{{$class}}">
                <a href="#" onclick="PlageAstreinte.modal('{{$_plage->_id}}','{{$_plage->user_id}}');">
                {{$_plage->_ref_user}}
                </a>
              </td>
              <td class="{{$class}}">
                {{if $_plage->libelle}}{{$_plage->libelle}}{{else}}<em>{{tr}}CPlageAstreinte.noLibelle{{/tr}}</em>{{/if}}
                </a>
              </td>
              <td class="{{$class}}">
                {{if $_plage->start == $_plage->end}}
                  {{$_plage->start|date_format:$conf.longdate}}
                {{else}}
                  {{$_plage->start|date_format:$conf.longdate}} &rarr; {{$_plage->end|date_format:$conf.longdate}}
                {{/if}}
              </td>
              <td class="{{$class}}">
                {{mb_include module=system template=inc_vw_duration duration=$_plage->_duration}}
              </td>
              <td style="background-color: #{{$_plage->_color}}">
                {{tr}}CPlageAstreinte.type.{{$_plage->type}}{{/tr}}
              </td>
            </tr>
          {{foreachelse}}
            <tr>
              <td colspan="5" class="empty">{{tr}}CMediusers.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
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