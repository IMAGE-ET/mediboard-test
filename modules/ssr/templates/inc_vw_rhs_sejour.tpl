{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<script type="text/javascript">
  Main.add(function(){	  
	  $$('a[href=#rhs-no-charge-{{$rhs_date_monday}}]')[0].down("span.count").update("({{$count_sej_rhs_no_charge}})");

	  Charged.refresh('{{$rhs_date_monday}}');
	});
</script>

{{assign var=days value="CRHS"|static:days}}

<button type="button" class="print" onclick="CotationRHS.printRHS('{{$rhs_date_monday}}')">{{tr}}Print{{/tr}}</button>
<button type="button" class="tick" onclick="CotationRHS.chargeRHS('{{$rhs_date_monday}}')">{{tr}}Charge{{/tr}}</button>

<form name="editRHS-{{$rhs_date_monday}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshSejour.curry('{{$rhs_date_monday}}')})">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_facture_rhss_aed" />
  <input type="hidden" name="del" value="0" />
  
  <div style="float:right">
    <label style="visibility: hidden;" class="rhs-charged" title="Cacher les RHS déjà facturés">
      <input type="checkbox" checked="checked" onchange="Charged.toggle(this);" />
      {{tr}}Hide{{/tr}} <span>0</span> RHS facturé(s)
    </label>
  </div>

  <table class="tbl">
    <tr>
      <th class="title"></th>
      <th class="title">Séjours</th>
    </tr>
    {{foreach from=$sejours_rhs item=_rhs}}
      {{if $_rhs->facture == 1}}
      <tr class="charged" style="display:none">
        <td style="width:1%" class="arretee"></td>
        <td class="arretee">{{$_rhs->_ref_sejour}}</td>
      </tr>
      {{else}}
      <tr>
        <td style="width:1%">
          <input type="checkbox" class="rhs" name="rhs_ids[{{$_rhs->_id}}]" value="{{$_rhs->_id}}"/>       
        </td>
        <td>{{$_rhs->_ref_sejour}}</td>
      </tr>
      {{/if}}
    {{/foreach}}
  </table>
</form>