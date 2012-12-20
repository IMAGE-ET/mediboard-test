{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
{{if $chir->_id && $chir->isPraticien()}}
  <select name="_function_secondary_id" style="width: 15em;"
    onchange="var facturable = this.options[this.selectedIndex].get('facturable');
      this.form.___facturable.checked = facturable == '1' ? 'checked' : '';
      $V(this.form._facturable, facturable);">
      <option value="{{$chir->function_id}}" data-facturable="{{$chir->_ref_function->facturable}}">{{$chir->_ref_function}}</option>
    {{foreach from=$_functions item=_function}}
      <option value="{{$_function->function_id}}" data-facturable="{{$_function->_ref_function->facturable}}">{{$_function}}</option>
    {{/foreach}}
  </select>
{{else}}
  <div class="small-info">
    {{tr}}CConsultation-choose_prat{{/tr}}
  </div>
{{/if}}