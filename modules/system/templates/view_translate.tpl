{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form action="?m={{$m}}" name="modlang" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<table class="main">
  <tr>
    <th>
      Traduction
      <select name="module" onchange="this.form.submit()">
      {{foreach from=$modules item=curr_module}}
      <option value="{{$curr_module}}" {{if $curr_module == $module}} selected="selected" {{/if}}>
        {{$curr_module}}
      </option>
      {{/foreach}}
      </select>
    </th>
  </tr>
</table>
</form>

<form action="?m={{$m}}" name="translate" method="post" class="prepared">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_translate_aed" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="module" value="{{$module}}" />
<table class="form">
  
  {{assign var="nb_lang" value=$locales|@count}}
  {{assign var="nb_cell" value=$nb_lang+2}}
  {{counter start=0 skip=1 assign=curr_data}}
  {{foreach from=$trans key=keyTrans item=currTrans}}
  
  {{if $curr_data is div by 20}}
  <tr>
    <th class="category">Chaîne</th>
    {{foreach from=$locales item=curr_lang}}
    <th class="category">{{tr}}language.{{$curr_lang}}{{/tr}}</th>
    {{/foreach}}
    <th class="category">
      <button type="submit" class="modify notext">{{tr}}Save{{/tr}}</button>
    </th>
  </tr>
  {{/if}}
  
  {{if $curr_data==0}}
  <tr>
    <td><input size="40" type="text" name="chaine[0]" value="" /></td>
    {{foreach from=$locales item=curr_lang}}
    <td><input size="40" type="text" name="trans[0][{{$curr_lang}}]" value="" /></td>
    {{/foreach}}
    <td class="button">
      <button type="submit" class="submit notext">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  {{/if}}
  {{counter}}
  <tr>
    <td><input size="40" type="text" name="chaine[{{$curr_data}}]" value="{{$keyTrans}}" /></td>
    {{foreach from=$locales item=curr_lang}}
    <td>
      <input size="40" type="text" name="trans[{{$curr_data}}][{{$curr_lang}}]" value="{{$currTrans.$curr_lang}}" />
    </td>
    {{/foreach}}
    <td></td>
  </tr>
  
  {{foreachelse}}
  <tr>
    <th class="category">Chaine</th>
    {{foreach from=$locales item=curr_lang}}
    <th class="category">
      {{$curr_lang}}
    </th>
    {{/foreach}}
  </tr>
  <tr>
    <td><input size="40" type="text" name="chaine[0]" value="" /></td>
    {{foreach from=$locales item=curr_lang}}
    <td><input size="40" type="text" name="trans[0][{{$curr_lang}}]" value="" /></td>
    {{/foreach}}
  </tr>
  {{/foreach}}

  <tr>
    <td class="button" colspan="{{$nb_cell}}">
      <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>