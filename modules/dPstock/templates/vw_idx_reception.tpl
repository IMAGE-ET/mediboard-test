{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7769 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function changePage(page) {
  $V(getForm("filter-receptions").start, page);
}

Main.add(function(){
  getForm("filter-receptions").onsubmit();
});
</script>

{{mb_include_script module=dPstock script=order_manager}}

<form name="filter-receptions" method="get" action="" onsubmit="return Url.update(this, 'receptions_list')">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="a" value="httpreq_vw_receptions_list" />
  <input type="hidden" name="start" value="{{$start}}" onchange="this.form.onsubmit()"/>
</form>

<div id="receptions_list"></div>
