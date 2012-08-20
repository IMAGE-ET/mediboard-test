{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=form value=""}}
{{mb_default var=patient_id value=""}}

<script type="text/javascript">
  createPatAnonyme = function() {
    var url = new Url("patients", "do_anonymous_patient", "dosql");
    url.addParam("callback", "fillPatAnonyme");
    url.requestUpdate("systemMsg", {method: "post"});
  }
  fillPatAnonyme = function(pat_id, pat_view) {  
    var form = getForm('{{$form}}');
    
    if (form) {
      $V(form.patient_id, pat_id);
      $V(form._patient_view, pat_view);
    }
  }
</script>

{{if $conf.dPplanningOp.CSejour.create_anonymous_pat && !$patient_id}}
  <button type="button" class="notext" onclick="createPatAnonyme()">
    <img src="modules/dPpatients/images/anonyme.png" alt="Anonyme" />
  </button>
{{/if}}