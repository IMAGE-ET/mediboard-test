{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{assign var=qty    value="qty_$litteral_day"}}
{{assign var=bounds value="_in_bounds_$litteral_day"}}

<td class="button {{if !$rhs->$bounds}}disabled{{elseif $_line->$qty}}ok{{/if}}">
  {{if $rhs->$bounds && !$read_only && !$_line->auto}}
    <form name="chg-{{$litteral_day}}-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, '{{$qty}}');">
      {{mb_class object=$_line}}
      {{mb_key   object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-$litteral_day-$line_guid"}}

      {{mb_field object=$_line field=$qty form=$qty_form onchange="this.form.onsubmit()" style="text-align: center;"}}
    </form>
  {{else}}
    {{$_line->$qty}}
  {{/if}}
</td>