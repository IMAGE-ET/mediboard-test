{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<script type="text/javascript">
Main.add(function() {
  Control.Tabs.setTabCount('rhs-search', '{{$sejours|@count}}');
});
</script>

<form class="prepared" name="editRHS-search" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: Sejour.search })">
  
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_facture_rhss_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="facture" value="1" />
  <input type="hidden" name="all_rhs" value="1" />
  
  <button type="button" class="print" onclick="CotationRHS.printRHS  (this.form)">{{tr}}Print{{/tr}}</button>
  <button type="button" class="tick"  onclick="CotationRHS.chargeRHS (this.form)">{{tr}}Charge{{/tr}}</button>
  <button type="button" class="cancel"onclick="CotationRHS.restoreRHS(this.form)">{{tr}}Restore{{/tr}}</button>

  <table class="tbl">
    <tr>
      <th class="narrow"></th>
      <th>{{mb_title class=CSejour field=patient_id}}</th>
      <th>{{tr}}CSejour-back-rhss{{/tr}}</th>
      <th>{{mb_title class=CSejour field=entree}}</th>
      <th>{{mb_title class=CSejour field=sortie}}</th>
      <th>{{mb_title class=CSejour field=service_id}}</th>
    </tr>
    {{foreach from=$sejours item=_sejour}} 
      <tr>
        <td>
          <input type="checkbox" class="rhs" name="sejour_ids[{{$_sejour->_id}}]" value="{{$_sejour->_id}}"/>       
        </td>

        <td class="text">
          {{mb_include template=inc_view_patient patient=$_sejour->_ref_patient}}
        </td>

        <td>
          {{foreach from=$_sejour->_back.rhss item=_rhs name=rhss}}
            {{assign var=arretee value=$_rhs->facture|ternary:"arretee":""}}
            <div class="{{$arretee}}" style="clear: both;">
              {{mb_include module=system template=inc_object_notes object=$_rhs}}
              Semaine {{$_rhs->date_monday|date_format:"%U"}} 
              du {{mb_value object=$_rhs field=date_monday}}
            </div>
          {{foreachelse}}
            <div class="empty">{{tr}}CSejour-back-rhss.empty{{/tr}}</div>
          {{/foreach}}
        </td>
        <td>
          {{mb_value object=$_sejour field=entree format=$conf.date}}
        </td>
        <td>
          {{mb_value object=$_sejour field=sortie format=$conf.date}}
        </td>
        
        <td style="text-align: center;">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
           {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
          </span>
          <div class="opacity-60">
           {{mb_value object=$_sejour field=service_id}}
          </div>
        </td>

      </tr>
    {{/foreach}}
  </table>
</form>