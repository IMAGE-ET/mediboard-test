<script type="text/javascript">
  function submitFSEI(oForm) {
    if(oForm.type_ei_id.value) {
	    submitFormAjax(
	      oForm,'systemMsg', { 
		    onComplete : function() {
			    doFiche(oForm.blood_salvage_id.value, oForm.type_ei_id.value);
				  }
				}
		  );
    } else {
      submitFormAjax(oForm,'systemMsg');
    }
  }
  
  function doFiche(blood_salvage_id,type_ei_id) {
	  var url = new Url;
	  url.setModuleTab("dPqualite","httpreq_blood_salvage_redirect");
	  url.addParam("type_ei_id",type_ei_id);
	  url.addParam("blood_salvage_id",blood_salvage_id);
	  url.redirect();
  }
</script>

<table class="form">
  <tr>
    <th style="width:10%">
      <b>Incident</b>
    </th>
    <td>
    <form name="fsei" action="?m={{$m}}" method="post">
        <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
        <input type="hidden" name="m" value="bloodSalvage" />
        <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	      <select name="type_ei_id" style="width:150px" onchange=" submitFSEI(this.form);">
	        <option value="">&mdash; Aucun incident</option>
	        {{foreach from=$liste_incident key=id item=incident_type}}
	        <option value="{{$incident_type->_id}}" {{if $incident_type->_id == $blood_salvage->type_ei_id}}selected="selected"{{/if}}>{{$incident_type->_view}}</option>
	        {{/foreach}}
	      </select>
	   </form>
    </td>
    <th style="width:10%">
      <b>Protocole qualité</b>
    </th>
    <td>
      <select name="protocole-qualite">
        <option>&mdash; Protocole</option>
        <option>Non prélevé</option>
        <option>Prélevé et transmis</option>
      </select>
    </td>
  </tr>
</table>