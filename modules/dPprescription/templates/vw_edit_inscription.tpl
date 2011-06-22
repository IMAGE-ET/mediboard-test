{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

updateAdministration = function(id, object){
  $V(getForm("addLineMedInscription").code_cip, '', false);
  $V(getForm("addLineElementInscription").element_prescription_id, '', false);
  var oForm = getForm("delInscription");
  var open_administration = function() {
    
    var url = new Url("dPprescription", "ajax_vw_form_administration");
    url.addParam("object_guid", object._guid);
    url.requestUpdate("administration");
  }
  // Si le formulaire de suppression de ligne est disponible, c'est que l'administration n'a été enregistrée.
  // Donc suppression de la ligne.
  if (oForm) {
    submitFormAjax(oForm, "systemMsg", {onComplete: open_administration});
  }
  else {
    open_administration();
  }
}

Main.add(function () {
	// Autocomplete des produits
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.autoComplete(getForm("searchProd").produit, "produit_auto_complete", {
    minChars: 2,
    updateElement: function(selected) {
      var oFormAddLineMedSuivi = getForm('addLineMedInscription');
      Element.cleanWhitespace(selected);
      var dn = selected.childNodes;
      $V(oFormAddLineMedSuivi.code_cip, dn[0].firstChild.nodeValue);
	  },
	  callback: 
      function(input, queryString){
        return (queryString + "&inLivret="+($V(getForm("searchProd")._recherche_livret)?'1':'0')); 
      }
  } );
	
	// Autocomplete des elements
	var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
  url.autoComplete(getForm("searchElt").libelle, "element_auto_complete", {
    minChars: 2,
    updateElement: function(selected) {
	    var oFormAddLineElementSuivi = getForm('addLineElementInscription');
      Element.cleanWhitespace(selected);
      var dn = selected.childNodes;
      $V(oFormAddLineElementSuivi.element_prescription_id, dn[0].firstChild.nodeValue);	
    }
  } );
	
	new Control.Tabs("inscriptions-tabs");
});

</script>

<table class="main form">
  <tr>
  	<th class="title">Inscription du {{$datetime|date_format:$conf.datetime}}</th>
  </tr>
  <tr>
  	<td>
			<ul class="control_tabs" id="inscriptions-tabs">
			  <li onclick="getForm('searchProd').produit.focus();"><a href="#tab-med" >Médicaments</a></li>
			  <li onclick="getForm('searchElt').libelle.focus();"><a href="#tab-elt">Elements</a></li>
			</ul>
			<hr class="control_tabs" />
			
      <div id="tab-med">
	      <!-- Ajout de ligne de medicament -->
	      <form action="?" method="post" name="addLineMedInscription" onsubmit="return checkForm(this);">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	        <input type="hidden" name="prescription_line_medicament_id" value=""/>
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="prescription_id" value="{{$prescription_id}}"/>
	        <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
	        <input type="hidden" name="code_cip" value="" onchange="return onSubmitFormAjax(this.form);"/>
	        <input type="hidden" name="inscription" value="1" />
					<input type="hidden" name="debut" value="current" />
					<input type="hidden" name="callback" value="updateAdministration" />
				</form>
	      <form action="?" method="get" name="searchProd">
	        <input type="text" style="width: 350px;" name="produit" class="autocomplete" autofocus="autofocus"/>
          <label title="Recherche dans le livret thérapeutique">
            <input type="checkbox" value="1" name="_recherche_livret"
              {{if $conf.dPprescription.CPrescription.preselect_livret}}checked="checked"{{/if}} /> Livret Thérap.
          </label>
	        <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	      </form>
      </div>

      <div id="tab-elt">
        <!-- Ajout de ligne d'element -->
        <form action="?" method="post" name="addLineElementInscription" onsubmit="return checkForm(this);">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
          <input type="hidden" name="prescription_line_element_id" value=""/>
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="prescription_id" value="{{$prescription_id}}"/>
          <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
          <input type="hidden" name="inscription" value="1" />
          <input type="hidden" name="element_prescription_id" value="" onchange="return onSubmitFormAjax(this.form);"/>
          <input type="hidden" name="debut" value="current" />
					<input type="hidden" name="callback" value="updateAdministration" />
        </form>
        <form action="?" method="get" name="searchElt">
        	<input type="text" style="width: 350px;" name="libelle" class="autocomplete" autofocus="autofocus"/>
          <div style="display:none;" class="autocomplete" id="element_auto_complete"></div>      
        </form>      	
      </div>
  	</td>
  </tr>
	<tr>
		<td id="administration">
			
		</td>
	</tr>		
</table>