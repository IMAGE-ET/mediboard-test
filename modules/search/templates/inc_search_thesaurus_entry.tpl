{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{mb_include module=system template=inc_pagination change_page="changePageThesaurus" total=$nbThesaurus current=$start_thesaurus step=10}}
<table class="main tbl">
  <tr>
  <th class="title" colspan="9">Liste des favoris pour {{$app->_ref_user}}
      <button type="button" class="favoris rtl" onclick="Thesaurus.addeditThesaurusEntry(null, null, '{{$user->_id}}', null,null, null, function(){})"
    >Nouveau favori</button>
  </th>
  </tr>
  <tr>
    <th class="category narrow"></th>
    <th class="category">{{mb_label object=$entry field=titre}}</th>
    <th class="category">{{mb_label object=$entry field=entry}}</th>
    <th class="category">{{mb_label object=$entry field=types}}</th>
    <th class="category">{{tr}}CSearchTargetEntry{{/tr}} <br/> Codes CCAM | Codes CIM10</th>
    <th class="category narrow"></th>
  </tr>
  {{foreach from=$thesaurus item=_entry}}
    <tr>
      <td>
        {{if $_entry->group_id}}
          <img src="images/icons/group.png" title="Favori pour {{mb_value object=$_entry field=group_id}}">
        {{/if}}
        {{if $_entry->function_id}}
          <img src="images/icons/user-function.png" title="Favori pour {{$user->_ref_function}}">
        {{/if}}
        {{if $_entry->user_id}}
          <img src="images/icons/user.png" title="Favori pour {{$user->_view}}">
        {{/if}}
      </td>
      <td class="text">
        {{mb_value object=$_entry field=titre}}
      </td>
      <td class="text">
        {{mb_value object=$_entry field=entry}}
      </td>
      <td class="compact empty">
        {{assign var=values_search_types value="|"|@explode:$_entry->types}}
        <div style=" overflow-y: scroll;" class="columns-1">
          {{foreach from=$types item=_value}}
            <label>
              <input type="checkbox" name="{{$_value}}" value="{{$_value}}"
                {{if in_array($_value, $values_search_types)}} checked="checked" {{/if}} disabled/>
              {{tr}}{{$_value}}{{/tr}}
            </label>
            <br />
          {{/foreach}}
        </div>
      </td>
      <td>
        <div style="float: right;" >
          <ul class="tags">
            {{foreach from=$_entry->_cim_targets item=_target}}
              <li class="tag" title="{{$_target->_ref_target->libelle}}">
                <span class="empty">{{$_target->_ref_target->code}}</span>
              </li>
              <br/>
              {{foreachelse}}
              <li><span class="empty">{{tr}}CSearchCibleEntry.none{{/tr}}</span></li>
            {{/foreach}}
          </ul>
        </div>
        <div>
          <ul class="tags">
            {{foreach from=$_entry->_ccam_targets item=_target}}
              <li class="tag" title="{{$_target->_ref_target->libelle_long}}">
                <span class="empty">{{$_target->_ref_target->code}}</span>
              </li>
              <br/>
              {{foreachelse}}
              <li><span class="empty">{{tr}}CSearchCibleEntry.none{{/tr}}</span></li>
            {{/foreach}}
          </ul>
        </div>
      </td>
      <td class="button">
        <button class="edit notext" onclick="Thesaurus.addeditThesaurusEntry(null, null, null, null, null, '{{$_entry->_id}}')"></button>
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td class="empty" colspan="7" style="text-align: center">
     {{tr}}CSearchThesaurusEntry.none{{/tr}}
    </td>
  </tr>
{{/foreach}}
</table>