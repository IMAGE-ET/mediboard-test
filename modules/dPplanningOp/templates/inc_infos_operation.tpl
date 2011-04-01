<!-- $Id$ -->

<script type="text/javascript">
printFicheAnesth = function(consultation_id, operation_id) {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consultation_id);
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheAnesth");
}

chooseAnesthCallback = function() {
  loadSejour({{$sejour->_id}}); 
}

printFicheBloc = function(operation_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc"); 
  url.addParam("operation_id", operation_id);
  url.popup(700, 500, "printFicheBloc");
}
</script>

{{if $sejour->_canRead}}
	<table class="tbl">
	  <tr>
	    <th class="title" colspan="4">
	      {{if $sejour->_ref_consult_anesth->_id && !$sejour->_ref_consult_anesth->operation_id}}
	        <button style="float: right" class="print" type="button" onclick="printFicheAnesth('{{$sejour->_ref_consult_anesth->_ref_consultation->_id}}');">
            Fiche d'anesthésie
          </button>
	      {{/if}}
	      {{tr}}CSejour-back-operations{{/tr}}
	    </th>
	  </tr>
	  <tr>
	    <th>{{tr}}COperation-chir_id{{/tr}}</th>
	    <th>{{tr}}Date{{/tr}}</th>
	    <th>{{tr}}COperation-_ext_codes_ccam{{/tr}}</th>
	    <th></th>
	  </tr>
	  {{foreach from=$sejour->_ref_operations item=_operation name=operation}}
	  <tr>
	    <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
	    </td>
	    <td>{{$_operation->_datetime|date_format:$conf.date}}</td>
	    {{if $_operation->annulee}}
	    <th class="category cancelled">
	      <strong>{{tr}}COperation-annulee{{/tr}}</strong>
			</th>
	    {{else}}
	    <td class="text">
	    	{{mb_include module=dPplanningOp template=inc_vw_operation}}
	    </td>
	    <td class="narrow">
	      <button class="{{if $_operation->_ref_consult_anesth->_ref_consultation->_id}}print{{else}}warning{{/if}}" style="width:11em;" type="button" onclick="printFicheAnesth('{{$_operation->_ref_consult_anesth->_ref_consultation->_id}}', '{{$_operation->_id}}');">
          Fiche d'anesthésie
        </button>
        <br />
        <button class="print" style="width:11em;" type="button" onclick="printFicheBloc('{{$_operation->_id}}');">Feuille de bloc</button>
	    </td>
	    {{/if}}
	  </tr>
    
    {{if $_operation->_back && array_key_exists("check_lists", $_operation->_back)}}
    <tr>
      <td colspan="10">
        
        <table class="layout" style="table-layout: fixed;">
          <tr>
            
          {{foreach from=$_operation->_back.check_lists item=check_list}}
            <td style="vertical-align: top">
              <table class="main form">
                <tr>
                  <th class="title" colspan="2">
                    Checklist {{mb_value object=$check_list field=type}}
                  </th>
                </tr>
                
                <tr>
                  <th class="category" colspan="2">
                    {{mb_label object=$check_list field=validator_id}} :
                    {{mb_value object=$check_list field=validator_id}}
                  </th>
                </tr>
                
                <tr>
                  <td colspan="2" style="padding: 0;">
                    
                    <table class="main">
                      {{assign var=category_id value=0}}
                      {{foreach from=$check_list->_back.items item=_item}}
                        {{assign var=curr_type value=$_item->_ref_item_type}}
                        {{if $curr_type->category_id != $category_id}}
                          <tr>
                            <th colspan="3" class="text category" style="text-align: left; border: none;">
                              <strong>{{$curr_type->_ref_category->title}}</strong>
                              {{if $curr_type->_ref_category->desc}}
                                &ndash; {{$curr_type->_ref_category->desc}}
                              {{/if}}
                            </th>
                          </tr>
                        {{/if}}
                        <tr>
                          <td style="padding-left: 1em; width: 100%; border: none;" class="text" colspan="2">
                            {{mb_value object=$curr_type field=title}}
                            <small style="text-indent: 1em; color: #666;">{{mb_value object=$curr_type field=desc}}</small>
                          </td>
                          <td class="text" style="border: none; {{if $_item->checked == "no"}}color: red; font-weight: bold;{{/if}}">
                            {{$_item->getAnswer()}}
                          </td>
                        </tr>
                        {{assign var=category_id value=$curr_type->category_id}}
                      {{foreachelse}}
                        <tr>
                          <td colspan="3" class="empty" style="border: none;">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
                        </tr>
                      {{/foreach}}
                      <tr>
                        <td colspan="3" style="border: none;">
                          <strong>Commentaires:</strong><br />
                          {{mb_value object=$check_list field=comments}}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          {{/foreach}}
          
          </tr>
        </table>
      </td>
    </tr>
    {{/if}}
	  {{foreachelse}}
	  <tr>
	    <td colspan="4" class="empty">{{tr}}COperation.none{{/tr}}</td>
	  </tr>
	  {{/foreach}}
	</table>
{{elseif $sejour->_id}}
  <div class="small-info">Vous n'avez pas accès au détail des interventions.</div>
{{/if}}