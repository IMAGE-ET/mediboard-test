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
      <button type="button" class="favoris rtl" onclick="Thesaurus.addeditThesaurusEntryManual(null, null, '{{$user->_id}}', null,null, null, function(){})"
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

        {{elseif $_entry->function_id}}
          <img src="images/icons/user-function.png" title="Favori pour {{$user->_ref_function}}">

        {{elseif $_entry->user_id}}
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
        {{if $_entry->types !== null}}
        {{assign var=values_search_types value="|"|@explode:$_entry->types}}
          <div style=" overflow-y: scroll;" class="columns-1">
            {{foreach from=$types item=_value}}
              {{if in_array($_value, $values_search_types)}}
                <label>
                  <input type="checkbox" name="{{$_value}}" value="{{$_value}}"
                     checked="checked" disabled/>
                  {{tr}}{{$_value}}{{/tr}}
                </label>
                <br />
              {{/if}}
            {{/foreach}}
          </div>
        {{else}}
        <span>{{tr}}CSearchThesaurusEntry-all-types{{/tr}}</span>
        {{/if}}
      </td>
      <td>
        {{if $_entry->_cim_targets|@count > 0 || $_entry->_ccam_targets|@count > 0}}
          <div style="float: right;" >
            <ul class="tags">
              {{foreach from=$_entry->_cim_targets item=_target}}
                <li class="tag " title="{{$_target->_ref_target->libelle}}" style="background-color: #CCFFCC; cursor:auto">
                  <span>{{$_target->_ref_target->code}}</span>
                </li>
                <br/>
              {{/foreach}}
            </ul>
          </div>
          <div>
            <ul class="tags">
              {{foreach from=$_entry->_ccam_targets item=_target}}
                <li class="tag" title="{{$_target->_ref_target->libelle_long}}" style="background-color: rgba(153, 204, 255, 0.6); cursor:auto">
                  <span>{{$_target->_ref_target->code}}</span>
                </li>
                <br/>
              {{/foreach}}
            </ul>
          </div>
        {{else}}
          <div class="empty" colspan="7" style="text-align: center">
            {{tr}}CSearchCibleEntry.none{{/tr}}
          </div>
        {{/if}}
      </td>
      <td class="button">
        <button class="edit notext" onclick="Thesaurus.addeditThesaurusEntry(null, '{{$_entry->_id}}', null)"></button>
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