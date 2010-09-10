{{* $Id: vw_idx_delivrance.tpl 8542 2010-04-13 09:02:43Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 8542 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkOutflow(form) {
  if (!($V(form.comments) || $V(form.service_id))) return false;
  return onSubmitFormAjax(form, {onComplete: function(){
    getForm("filter").onsubmit();
  }});
}

Main.add(function(){
  getForm("filter").onsubmit();
});

function changePage(start) {
  $V(getForm("filter").start, start);
}

function removeOutflow(delivery_id, view) {
  var form = getForm("outflow-delete");
  $V(form.delivery_id, delivery_id);
  confirmDeletion(form,{typeName: 'la sortie manuelle de', ajax: true, objName: view}, {
    onComplete: function(){
      getForm("filter").onsubmit();
    }
  });
}
</script>


<form name="outflow-delete" method="post" action="?">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="delivery_id" value="" />
</form>

<form name="filter" action="?" method="get" onsubmit="return Url.update(this, 'outflows')">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="a" value="httpreq_vw_list_outflows" />
  <input type="hidden" name="start" value="{{$start}}" onchange="this.form.onsubmit()" />
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<div id="outflows"></div>
