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
  {{if $rhs->$bounds && !$read_only}}
    <form name="chg-{{$litteral_day}}-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitQuantity(this, '{{$qty}}');">
      <input type="hidden" name="m" value="ssr" />
      <input type="hidden" name="dosql" value="do_line_rhs_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$_line}}
      <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
      {{assign var=line_guid value=$_line->_guid}}
      {{assign var=qty_form value="chg-$litteral_day-$line_guid"}}

      {{mb_field object=$_line field=$qty form=$qty_form onchange="this.form.onsubmit()" tabindex="$numsemaine$indexforeach$day"}}
    </form>
  {{else}}
    {{$_line->$qty}}
  {{/if}}
</td>