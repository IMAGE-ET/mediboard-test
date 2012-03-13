{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<form name="Edit-CRHS-{{$rhs->_date_sunday}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitRHS(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_rhs_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$rhs}}
{{mb_field object=$rhs field=sejour_id  hidden=1}}

<table class="form">
  <tr>
    <th>{{mb_label object=$rhs field=date_monday}}</th>
    <td>{{mb_field object=$rhs field=date_monday readonly=1}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$rhs field=_date_sunday}}</th>
    <td>{{mb_field object=$rhs field=_date_sunday readonly=1}}</td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="new" type="submit">
        {{tr}}CRHS-title-create{{/tr}}
      </button>
    </td>
  </tr>
</table>

</form>
