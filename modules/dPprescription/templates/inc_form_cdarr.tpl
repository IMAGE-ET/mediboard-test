{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $element_prescription->_id}}
	<script type="text/javascript">
		Main.add( function(){
		  if($('code_auto_complete')){
		    var url = new Url("ssr", "httpreq_do_activite_autocomplete");
		    url.autoComplete(getForm("editCdarr").code, "code_auto_complete", {
		      minChars: 2,
		      select: "value"
		    } );
		  } 
		});
	</script>
	
	<form name="editCdarr" action="" method="post" onsubmit="return onSubmitFormAjax(this);">
	 <input type="hidden" name="m" value="ssr" />
	 <input type="hidden" name="dosql" value="do_element_prescription_to_cdarr_aed" />
	 <input type="hidden" name="del" value="0" />
	 <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
	 <input type="hidden" name="element_prescription_to_cdarr_id" value="{{$element_prescription_to_cdarr->_id}}" />
	 <input type="hidden" name="callback" value="refreshListCdarr" />
	 <table class="form">
      {{mb_include module=system template=inc_form_table_header object=$element_prescription_to_cdarr}}
	    <tr>
	      <th>{{mb_label object=$element_prescription_to_cdarr field="code"}}</th>
	      <td>
	        {{mb_field object=$element_prescription_to_cdarr field=code class="autocomplete"}}
	        <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
	      </td>
	    </tr>
	    <tr>
	     <th>{{mb_label object=$element_prescription_to_cdarr field="commentaire"}}</th>
	     <td>{{mb_field object=$element_prescription_to_cdarr field="commentaire"}}</td>
	   </tr>
	   <tr>
	    <td class="button" colspan="2">
	      <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
				{{if $element_prescription_to_cdarr->_id}}
	      <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'le code',objName:'{{$element_prescription_to_cdarr->code|smarty:nodefaults|JSAttribute}}'})">
	        {{tr}}Delete{{/tr}}
	      </button>
				{{/if}}
	    </td>
	   </tr>
	  </table>
	</form>
{{/if}}