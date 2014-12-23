{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script>
  targetEditCallback = function() { window.url_addeditThesaurusEntry.refreshModal();};
  var form = getForm("addeditFavoris");
  var cont_type = $('cont_types'),
    element_type = form.types;

  window.types = new TokenField(element_type, {onChange: function(){}.bind(element_type)});
</script>


<form method="post" name="addeditFavoris" onsubmit="return Thesaurus.submitThesaurusEntry(this);">
  {{mb_key   object=$thesaurus_entry}}
  {{mb_class object=$thesaurus_entry}}
  <input type="hidden" name="user_id" value="{{$thesaurus_entry->user_id}}"/>
  <input type="hidden" name="function_id" value="{{$thesaurus_entry->function_id}}"/>
  <input type="hidden" name="group_id" value="{{$thesaurus_entry->group_id}}"/>
  <input type="hidden" name="types" value="{{"|"|implode:$search_types}}"/>
  <input type="hidden"  name="del" value="0"/>
  {{if !$thesaurus_entry->_id}}
    <input type="hidden"  name="callback" value="Thesaurus.addeditThesaurusCallback"/>
  {{/if}}
  <table class="main form">
    {{if $thesaurus_entry->_id}}
      <tr>
        <td>
          <button type="button" class="new" onclick="Thesaurus.addeditTargetEntry('{{$thesaurus_entry->_id}}', window.targetEditCallback)">{{tr}}CSearchCibleEntry-action-add edit{{/tr}}</button>
        </td>
        <td>
          <table>
            <tr>
              <td class="halfPane">
                <fieldset>
                  <legend>Codes CIM10 : </legend>
                  <ul class="tags">
                    {{foreach from=$thesaurus_entry->_cim_targets item=_target}}
                      <li class="tag" title="{{$_target->_ref_target->libelle}}">
                        {{$_target->_ref_target->code}} - {{$_target->_ref_target->libelle}}
                      </li>
                      <br/>
                      {{foreachelse}}
                      <li><span class="empty">{{tr}}CSearchCibleEntry.none{{/tr}}</span></li>
                    {{/foreach}}
                  </ul>
                </fieldset>
              </td>
              <td class="halfPane">
                <fieldset>
                  <legend>Codes CCAM : </legend>
                  <ul class="tags">
                    {{foreach from=$thesaurus_entry->_ccam_targets item=_target}}
                      <li class="tag" title="{{$_target->_ref_target->libelle_long}}">
                        {{$_target->_ref_target->code}} - {{$_target->_ref_target->libelle_court}}
                      </li>
                      <br/>
                      {{foreachelse}}
                      <li><span class="empty">{{tr}}CSearchCibleEntry.none{{/tr}}</span></li>
                    {{/foreach}}
                  </ul>
                </fieldset>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="2">
        <span class="circled">
          <img src="images/icons/user.png" title="Favori pour {{$user_thesaurus->_id}}">
          <label><input type="checkbox" name="_user_id" value="{{$user_thesaurus->_id}}" checked></label>
        </span>

        <span class="circled">
           <img src="images/icons/user-function.png" title="Favori pour {{$user_thesaurus->_ref_function}}">
          {{if $thesaurus_entry->function_id}}
            <label><input type="checkbox" name="_function_id" onclick="$V(form.elements.function_id, (this.checked) ? '{{$thesaurus_entry->function_id}}' : null)" checked></label>
          {{else}}
            <label><input type="checkbox" name="_function_id" onclick="$V(form.elements.function_id, (this.checked) ? '{{$user_thesaurus->_ref_function->_id}}' : null)"></label>
          {{/if}}
        </span>

        <span class="circled">
          <img src="images/icons/group.png" title="Favori pour {{$user_thesaurus->_ref_function->_ref_group}}">
          {{if $thesaurus_entry->group_id}}
            <label><input type="checkbox" name="_group_id" onclick="$V(form.elements.group_id, (this.checked) ? '{{$thesaurus_entry->group_id}}' : null)" checked></label>
          {{else}}
            <label><input type="checkbox" name="_group_id" onclick="$V(form.elements.group_id, (this.checked) ? '{{$user_thesaurus->_ref_function->_ref_group->_id}}' : null)"></label>
          {{/if}}
        </span>
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$thesaurus_entry field=titre}}
      </td>
      <td>
        {{mb_field object=$thesaurus_entry field=titre}}
      </td>
    </tr>
    <tr>
      <td class="text narrow">
        {{tr}}CSearchThesaurusEntry-Pattern{{/tr}}
      </td>
      <td>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title and{{/tr}}" onclick="Thesaurus.addPatternToEntry('add')">{{tr}}CSearchThesaurusEntry-Pattern and{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title or{{/tr}}" onclick="Thesaurus.addPatternToEntry('or')">{{tr}}CSearchThesaurusEntry-Pattern or{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title not{{/tr}}" onclick="Thesaurus.addPatternToEntry('not')">{{tr}}CSearchThesaurusEntry-Pattern not{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title like{{/tr}}" onclick="Thesaurus.addPatternToEntry('like')">{{tr}}CSearchThesaurusEntry-Pattern like{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title obligation{{/tr}}" onclick="Thesaurus.addPatternToEntry('obligation')">{{tr}}CSearchThesaurusEntry-Pattern obligation{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title prohibition{{/tr}}" onclick="Thesaurus.addPatternToEntry('prohibition')">{{tr}}CSearchThesaurusEntry-Pattern prohibition{{/tr}}</button>
        <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title without negatif{{/tr}}" onclick="Thesaurus.addPatternToEntry('without_negatif')">{{tr}}CSearchThesaurusEntry-Pattern without negatif{{/tr}}</button>
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$thesaurus_entry field=entry}}
      </td>
      <td>
        {{mb_field object=$thesaurus_entry field=entry}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$thesaurus_entry field=types}}
      </td>
      <td id="cont_types" class="columns-2">
        {{foreach from=$types item=_type}}
          <label> <input type="checkbox" name="addeditFavoris_{{$_type}}" id="{{$_type}}" value="{{$_type}}" {{if in_array($_type, $search_types)}}checked{{/if}}
                 onclick="window.types.toggle(this.value, this.checked);">
          {{tr}}{{$_type}}{{/tr}}</label>
          <br/>
        {{/foreach}}
        </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$thesaurus_entry field=contextes}}
      </td>
      <td>
        {{mb_field object=$thesaurus_entry field=contextes}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$thesaurus_entry field=agregation}}
      </td>
      <td>
        {{mb_field object=$thesaurus_entry field=agregation}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
        {{if $thesaurus_entry->_id}}
          <button type="submit" class="trash" onclick="$V(this.form.del,'1')">{{tr}}Delete{{/tr}}</button>
        {{/if}}
        </td>
    </tr>
  </table>
</form>
