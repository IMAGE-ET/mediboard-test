{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function viewMomentsUnitaires(code_moment_id){
  $("moment-"+code_moment_id).className = "selected";
  var url = new Url;
  url.setModuleAction("dPprescription","httpreq_vw_moments_unitaires");
  url.addParam("code_moment_id", code_moment_id);
  url.requestUpdate("moments_unitaires");
}


function submitMomentComplexe(oForm){
  submitFormAjax(oForm, 'systemMsg', {
    onComplete:
      function(){
        reloadMomentsUnitaires(oForm.code_moment_id.value);   
      }
  });
}


function delMomentUnitaire(oForm, association_id){
  oForm.del.value = 1;
  oForm.association_moment_id.value = association_id;
  submitAssociation(oForm);
}


function reloadMomentsUnitaires(code_moment_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_moments_unitaires");
  url.addParam("code_momet_id", code_moment_id);
  url.requestUpdate("moments_unitaires");
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl" id="moments">
      	<tr>
      		<th class="title" colspan="3">Moments</th>
        </tr>
        <tr>
          <th>Libelle</th>
          <th>Code Moment</th>
          <th>Coeff</th>
        </tr>
        <!-- Parcours des moments BCB-->
        {{foreach from=$moments item=_moment}}
          <tr {{if $_moment.CODE_MOMENT == $moment->code_moment_id}}class="selected"{{/if}} id="moment-{{$_moment.CODE_MOMENT}}">
            <td>
              <a href="#1" onclick="viewMomentsUnitaires('{{$_moment.CODE_MOMENT}}');">
                {{$_moment.LIBELLE_MOMENT}}
              </a>
            </td>
            <td>
              {{$_moment.CODE_MOMENT}}
            </td>
            <td>
              {{$_moment.COEFF}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane" id="moments_unitaires">
      {{include file="inc_vw_moments_unitaires.tpl"}}
    </td>
  </tr>
</table>