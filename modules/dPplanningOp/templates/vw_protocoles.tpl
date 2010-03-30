<!-- $Id$ -->

<script type="text/javascript">
changePage = function (page){
	 var form = getForm ("selectFrm");
	 var url = new Url("dPplanningOp","httpreq_vw_list_protocoles");
	 url.addParam("page",page);
   url.addFormData(form);
	 type_protocole = null;
	 if($('interv')){
		 if ($("interv").style.display != "none"){
		 type_protocole = 'interv';
		 }
	 }
	 else if($("sejour")) {
	   if ($("sejour").style.display != "none"){
	     type_protocole = 'sejour';
		 }
	 }
	 url.addParam("protocole_id",'{{$protSel->_id}}');
	 url.addParam("type_protocole",type_protocole);
   url.requestUpdate("list_protocoles");
}
Main.add(function(){
  changePage('{{$page}}');
});
</script>
<table class="main">
  <tr>
    <td colspan="2">
      <a class="button new" href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_edit_protocole&amp;protocole_id=0">Créer un nouveau protocole</a>
          
      <form name="selectFrm" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
			<input type="hidden" name="page_interv" value="" />
      <input type="hidden" name="page_sejour" value="" />
			<input type="hidden" name="type_protocole" value="" />
      <table class="form">
        <tr>
          <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien</label></th>
          <td>
            <select name="chir_id" onchange="changePage(0)">
              <option value="" >&mdash; Tous les praticiens</option>
              {{foreach from=$listPrat item=curr_prat}}
              {{if $curr_prat->_ref_protocoles|@count}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
                {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
              </option>
              {{/if}}
              {{/foreach}}
            </select>
          </td>
          <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM</label></th>
          <td>
            <select name="code_ccam" onchange="changePage(0)">
              <option value="" >&mdash; Tous les codes</option>
              {{foreach from=$listCodes|smarty:nodefaults key=curr_code item=code_nomber}}
              <option value="{{$curr_code}}" {{if $code_ccam == $curr_code}} selected="selected" {{/if}}>
                {{$curr_code}} ({{$code_nomber}})
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>    
    </td>
  </tr>
	
  <tr>
    <td id="list_protocoles">
		
    </td>
    {{if $protSel->_id && !$dialog}}
    <td class="halfPane">
      <br />
			{{include file=inc_details_protocole.tpl}}
    </td>
    {{/if}} 
  </tr>
</table>