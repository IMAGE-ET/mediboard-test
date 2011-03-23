{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $is_anesth || $is_chir || $is_admin}}

<script type="text/javascript">
updateToGuid = function(element) {
  var split = $V(element).split("-");
  var classe = split[0] == "prot" ? "CPrescription" : "CPrescriptionProtocolePack";
  $V(element, classe + "-" + split[1]);
}

updateDiv = function(id, type) {
  var oForm = getForm("selProtocole-"+type+"-"+id);
  var isPack = $V(oForm.elements["protocole_prescription_"+type+"_id"]).match("Pack") != null;
  $("prot_"+type+"_"+id).innerHTML = (isPack ? "Pack: " : "Protocole: ") +$V(oForm.libelle_protocole); 
}

removeProtocole = function(form, type) {
  $V(form.elements["protocole_prescription_"+type+"_id"], "");
  $V(form.elements["libelle_protocole"], "");
  $("prot_"+type+"_"+$V(form.protocole_id)).innerHTML = "";
  submitFormAjax(form, 'systemMsg');
}
</script>
<form name="filtre-protocole" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
	<table class="form">
	  <tr>
	   {{*<td>
	    {{if $anesths|@count}}
        Anesthésiste: 
        <select name="anesth_id" onchange="this.form.submit()">
          <option value="">&mdash; Choix d'un anesthésiste</option>
          {{foreach from=$anesths item=_anesth}}
            <option class="mediuser" style="border-color: #{{$_anesth->_ref_function->color}};" value="{{$_anesth->_id}}" {{if $_anesth->_id == $anesth_id}}selected="selected"{{/if}}>{{$_anesth->_view}}</option>
          {{/foreach}}
        </select>
      {{/if}}
	    </td>*}}
      <td>
      {{if $praticiens|@count}}
        Chirurgien: 
        <select name="praticien_id" onchange="this.form.submit()">
          <option value="">&mdash; Choix d'un chirurgien</option>
          {{foreach from=$praticiens item=_praticien}}
            <option class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};" value="{{$_praticien->_id}}" {{if $_praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}</option>
          {{/foreach}}
        </select>
      {{/if}}
      </td>
	    <td>
	      <select name="all_prot" onchange="this.form.submit()">
	        <option value="1" {{if $all_prot == "1"}}selected="selected"{{/if}}>Tous les protocoles</option>
	        <option value="0" {{if $all_prot == "0"}}selected="selected"{{/if}}>Seulement les protocoles non associés</option>
	      </select>
	    </td>
	  </tr>
	</table>
</form>
<table class="tbl">
  <tr>
    <th>Chirurgien - Actes CCAM</th>
    <!-- <th>Protocole Anesth</th>-->
    <th>Protocole Chir</th>
  </tr>
  {{foreach from=$protocoles item=_protocole}}
  <tr>
    <td class="text">
      <strong>
      {{$_protocole->_ref_chir->_view}}
      {{if $_protocole->libelle}}
        - {{$_protocole->libelle}}
      {{/if}}
      {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
        - {{$_code->code}}
      {{/foreach}}
      </strong>
      <br />  
      {{foreach from=$_protocole->_ext_codes_ccam item=_code_ccam}}
        - {{$_code_ccam->libelleLong}}
        <br />
      {{/foreach}}
    </td>
    {{*<td>
      {{if $is_anesth || $is_admin}}
        <form name="selProtocole-anesth-{{$_protocole->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_protocole_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="protocole_id" value="{{$_protocole->_id}}" />
          <input type="hidden" name="protocole_prescription_anesth_id" value="{{$_protocole->protocole_prescription_anesth_id}}"/>
          <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete"
            style="font-weight: bold; font-size: 1.3em; width: 200px;"
            onchange="updateToGuid(this.form.protocole_prescription_anesth_id); updateDiv('{{$_protocole->_id}}', 'anesth');
            submitFormAjax(this.form, 'systemMsg');" style="max-width: 15em;"/>
        </form>
        <script type="text/javascript">
          var oForm = getForm("selProtocole-anesth-{{$_protocole->_id}}");
          var url_anesth_{{$_protocole->_id}} = new Url("dPprescription", "httpreq_vw_select_protocole");
          var autocompleter_anesth_{{$_protocole->_id}} = url_anesth_{{$_protocole->_id}}.autoComplete(oForm.libelle_protocole, null, {
            dropdown: true,
            minChars: 1,
            valueElement: oForm.elements.protocole_prescription_anesth_id,
            updateElement: function(selectedElement) {
              if (autocompleter_anesth_{{$_protocole->_id}}.options.afterUpdateElement) {
                autocompleter_anesth_{{$_protocole->_id}}.options.afterUpdateElement(autocompleter_anesth_{{$_protocole->_id}}.element, selectedElement);
              }
              var node = $(selectedElement).down('.view');
              $V(getForm("selProtocole-anesth-{{$_protocole->_id}}").libelle_protocole, (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
            },
            callback: 
              function(input, queryString){
                return (queryString + "&praticien_id={{$anesth_id}}");  
              }
          } );
        </script>
        <br />
        <div id="prot_anesth_{{$_protocole->_id}}">{{$_protocole->_ref_protocole_prescription_anesth->_view}}</div>
      {{else}}
        {{$_protocole->_ref_protocole_prescription_anesth->_view}}
      {{/if}}
    </td>*}}
    <td>  
      {{if $is_chir || $is_admin}}
        <form name="selProtocole-chir-{{$_protocole->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_protocole_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="protocole_id" value="{{$_protocole->_id}}" />
          <input type="hidden" name="protocole_prescription_chir_id" value="{{$_protocole->protocole_prescription_chir_id}}"/>
          <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete"
            style="font-weight: bold; font-size: 1.3em; width: 200px;"
            onchange="updateToGuid(this.form.protocole_prescription_chir_id); updateDiv('{{$_protocole->_id}}', 'chir');
            submitFormAjax(this.form, 'systemMsg');" style="max-width: 15em;"/>
          <button type="button" name="delete" class="cancel notext" onclick="removeProtocole(this.form, 'chir')">{{tr}}CPrescription.unlink_protocole{{/tr}}</button>
        </form>
        <script type="text/javascript">
          var oForm = getForm("selProtocole-chir-{{$_protocole->_id}}");
          var url_chir_{{$_protocole->_id}} = new Url("dPprescription", "httpreq_vw_select_protocole");
          var autocompleter_chir_{{$_protocole->_id}} = url_chir_{{$_protocole->_id}}.autoComplete(oForm.libelle_protocole, null, {
            dropdown: true,
            minChars: 1,
            valueElement: oForm.elements.protocole_prescription_chir_id,
            updateElement: function(selectedElement) {
              if (autocompleter_chir_{{$_protocole->_id}}.options.afterUpdateElement) {
                autocompleter_chir_{{$_protocole->_id}}.options.afterUpdateElement(autocompleter_chir_{{$_protocole->_id}}.element, selectedElement);
              }
              var node = $(selectedElement).down('.view');
              $V(getForm("selProtocole-chir-{{$_protocole->_id}}").libelle_protocole, (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
            },
            callback: 
              function(input, queryString){
                return (queryString + "&praticien_id={{$praticien_id}}");  
              }
          } );
        </script>
        <div id="prot_chir_{{$_protocole->_id}}">{{$_protocole->_ref_protocole_prescription_chir->_view}}</div>
      {{else}}
        {{$_protocole->_ref_protocole_prescription_chir->_view}}
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3">Aucun protocole ne correspond à la recherche</td>
  </tr>
  {{/foreach}}
</table>
{{else}}
  <div class="small-info">
  Vous devez être praticien pour gérer vos protocoles
  </div>
{{/if}}