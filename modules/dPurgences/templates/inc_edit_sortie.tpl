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
	
  var form = getForm('editSejour');
  return onSubmitFormAjax(form, { onComplete: function() {
    Sortie.refresh('{{$rpu->_id}}');
    Sortie.close();
  }});
} 

Fields = {
  init: function(mode_sortie) {
	  $('etablissement_sortie_transfert').setVisible(mode_sortie == "transfert");
	  $('lit_sortie_transfert'      ).setVisible(mode_sortie == "mutation");
  },
  
  modif: function(lit_id) {
    var form = getForm('editSejour');
    $('service_sortie_transfert').setVisible(lit_id);
    
    var service = $('CLit-'+lit_id).className;
    service = service.split("-");
    form.service_sortie_id.value = service[1];
    
    form.service_sortie_id_autocomplete_view.value = service[2];
  },
  
  clear: function() {
    if (confirm($T('CSejour-sortie-confirm-clearall'))) {
      var form = getForm('editSejour');
      form.mode_sortie.clear();
      form.sortie_reelle.clear();
      form.sortie_reelle_da.clear();
      form.etablissement_sortie_id_autocomplete_view.clear();
      form.etablissement_sortie_id.clear();
      form.service_sortie_id_autocomplete_view.clear();
      form.service_sortie_id.clear();
      form.commentaires_sortie.clear();
      
      submitSejour(true);
    }
  }
}
</script>

{{mb_include template=inc_form_sortie}}

<table class="form">
  <tr>
    <td class="button">
      <button class="cancel singleclick" onclick="Fields.clear();">
        {{tr}}Cancel{{/tr}}
        {{mb_label object=$sejour field=sortie}}
      </button>
      <button class="save singleclick" onclick="submitSejour(true);">
        {{tr}}Validate{{/tr}}
        {{mb_label object=$sejour field=sortie}}
      </button>
    </td>
  </tr>
</table>