<script type="text/javascript">

ActesNGAP = {
	refreshList: function() {
	  var url = new Url;
	  url.setModuleAction("dPcabinet", "httpreq_vw_actes_ngap");
	  url.addParam("consultation_id", "{{$consult->_id}}");
	  url.requestUpdate('listActesNGAP', {
	    waitingText: null,
	    coverTarget: true
	  } );
	},

	remove: function(acte_ngap_id){
	  var oForm = document.editNGAP;
	  oForm.del.value = 1;
	  oForm.acte_ngap_id.value = acte_ngap_id;
	  this.submit();
	},
	
	submit: function() {
	  var oForm = document.editNGAP;
		submitFormAjax(oForm, 'systemMsg', { 
			onComplete: ActesNGAP.refreshList
		} );
	}
}

</script>

<form name="editNGAP" method="post" action=""> 
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$acte_ngap field="quantite"}}</th>
      <th>{{mb_label object=$acte_ngap field="code"}}</th>
      <th>{{mb_label object=$acte_ngap field="coefficient"}}</th>
      <th>Action</th>
    </tr>
    <tr>
      <td>
        {{mb_field object=$acte_ngap field="quantite"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="code"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="coefficient"}}  
      </td>
      <td>
        <button type="button" class="new" onclick="ActesNGAP.submit()">
          {{tr}}Create{{/tr}}
        </button>
      </td>     
    </tr>
  
    {{foreach from=$listActesNGAP item="_acteNGAP"}}
    <tr>
      <td>
        {{mb_value object=$_acteNGAP field="quantite"}}
      </td>
      <td>
        {{mb_value object=$_acteNGAP field="code"}}
      </td>
      <td>
        {{mb_value object=$_acteNGAP field="coefficient"}}  
      </td>
      <td>
       	<button type="button" class="trash" onclick="ActesNGAP.remove({{$_acteNGAP->_id}})">
          {{tr}}Delete{{/tr}}
		 	</button>
      </td>
   </tr>
   {{/foreach}}
 </table>
</form>
