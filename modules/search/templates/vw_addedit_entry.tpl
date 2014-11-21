{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script>
  var form = getForm("addeditFavoris");
  var cont_type = $('cont_types'),
    element_type = form.types;

  window.types = new TokenField(element_type, {onChange: function(){}.bind(element_type)});
</script>


<form method="post" name="addeditFavoris" action="?m=search&tab=vw_search_thesaurus" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close.curry()});">
  {{mb_key   object=$thesaurus_entry}}
  {{mb_class object=$thesaurus_entry}}
  <input type="hidden" id="user_id" name="user_id" value="{{$thesaurus_entry->user_id}}"/>
  <input type="hidden" id="types" name="types" value="{{"|"|implode:$search_types}}"/>
  <input type="hidden"  name="del" value="0"/>
  <table class="main form">
    <tr>
      <td colspan="2">
        <span class="circled">
          <img src="images/icons/user.png" title="Favori pour {{mb_value object=$thesaurus_entry field=user_id}}">
          <input type="checkbox" name="user_id" value="{{$user->_id}}" checked>
        </span>
        <span class="circled">
          <img src="images/icons/user-function.png" title="Favori pour {{$user->_ref_function}}">
          <input type="checkbox" name="function_id" value="{{$user->_ref_function->_id}}">
        </span>
        <span class="circled">
          <img src="images/icons/group.png" title="Favori pour {{$user->_ref_function->_ref_group}}">
          <input type="checkbox" name="group_id" value="{{$user->_ref_function->_ref_group->_id}}">
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
      <td id="cont_types">
        {{foreach from=$types item=_type}}
          <input type="checkbox" name="addeditFavoris_{{$_type}}" id="{{$_type}}" value="{{$_type}}" {{if in_array($_type, $search_types)}}checked{{/if}}
                 onclick="window.types.toggle(this.value, this.checked);">
          <label for="{{$_type}}">{{tr}}{{$_type}}{{/tr}}</label>
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
