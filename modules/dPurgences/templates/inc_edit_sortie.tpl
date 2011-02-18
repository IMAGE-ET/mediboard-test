{{* $Id: inc_vw_rpu.tpl 11346 2011-02-17 20:38:29Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 11346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
submitSejour = function(force) {
  if (!force) {
	  return;
	}
	
  var form = document.editSejour;
  return onSubmitFormAjax(form, { onComplete: function() {
	  Sortie.refresh('{{$rpu->_id}}');
    Sortie.close();
  }});
} 

initFields = function(mode_sortie){
  $('etablissement_sortie_transfert').setVisible(mode_sortie == "transfert");
  $('service_sortie_transfert'      ).setVisible(mode_sortie == "mutation");
  $('commentaires_sortie'           ).setVisible(mode_sortie && mode_sortie != "normal");
}

</script>

{{mb_include template=inc_form_sortie}}

<table class="form">
	<tr>
		<td class="button">
			<button class="save singleclick" onclick="submitSejour(true);">{{tr}}Validate{{/tr}}</button>
		</td>
	</tr>
</table>