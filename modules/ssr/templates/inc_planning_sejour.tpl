{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=ssr template=inc_vw_week}}

<script type="text/javascript">
Main.add(function() {
  var planning = window["planning-{{$planning->guid}}"];
  planning.onEventChange = function(e){
    var form = getForm("form-edit-planning-{{$planning->guid}}");
    var time = e.getTime();
    $V(form.evenement_ssr_id, e.guid.split('-')[1]);
    $V(form.debut, time.start.toDATETIME(true));
    $V(form.duree, time.length);
    onSubmitFormAjax(form, {onComplete: function(){
      Planification.refreshSejour("{{$planning->guid}}".split("-")[1], true);
    }});
  }
});
</script>

<form name="form-edit-planning-{{$planning->guid}}" method="post" action="">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_evenement_ssr_aed" />
  <input type="hidden" name="evenement_ssr_id" value="" />
  <input type="hidden" name="debut" value="" />
  <input type="hidden" name="duree" value="" />
</form>
