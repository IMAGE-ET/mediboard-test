{{assign var=line_guid value=$line->_guid}}

<script type="text/javascript">

updateFieldsMedicament = function(selected) {
  var oFormProduit = getForm("editDM-{{$line_guid}}");
  Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
  if(dn[0].className != 'informal'){
    $V(oFormProduit.cip_dm, dn[0].firstChild.nodeValue);
  }
}

refreshDM = function(line_id){
  var url = new Url("dPprescription", "httpreq_vw_element_dm");
  url.addParam("prescription_line_element_id", line_id);
  url.requestUpdate("vw_dm-"+line_id);
}

Main.add( function(){
  if($('editDM-{{$line_guid}}_produit')){
	  // Autocomplete des DM
	  var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
	  urlAuto.autoComplete("editDM-{{$line_guid}}_produit", "dm_auto_complete", {
	    minChars: 3,
	    updateElement: updateFieldsMedicament,
	    callback: 
	      function(input, queryString){
	        return (queryString + "&hors_specialite=1"); 
	      }
	  } );
	}
} );

</script>

<form name="editDM-{{$line_guid}}" method="post" action="?">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_element_id" value="{{$line->_id}}" />
	<strong>DM</strong>
  {{if $line->cip_dm}}
    {{$line->_ref_dm->libelle}}, 
		{{mb_label object=$line field="quantite_dm"}}
    {{mb_field object=$line field="quantite_dm" form="editDM-$line_guid" increment=1 onchange="onSubmitFormAjax(this.form);"}}
		{{mb_field object=$line field="cip_dm" hidden=true}}
		<button type="button" class="trash notext" onclick="this.form.quantite_dm.value=''; this.form.cip_dm.value=''; onSubmitFormAjax(this.form, { onComplete: refreshDM.curry('{{$line->_id}}') } );" /></button>
  {{else}}
	  <input type="text" name="produit" value="" size="20" style="width: 300px;" class="autocomplete" />
	  <div style="display:none; width: 350px;" class="autocomplete" id="dm_auto_complete"></div>
	  <input type="hidden" name="cip_dm" onchange="onSubmitFormAjax(this.form, { onComplete: refreshDM.curry('{{$line->_id}}') } )" />
	{{/if}}
</form>