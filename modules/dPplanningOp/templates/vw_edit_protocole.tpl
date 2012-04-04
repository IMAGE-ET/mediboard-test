{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

var oCcamField = null;

function copier(){
  var oForm = document.editFrm;
  oForm.protocole_id.value = "";
  {{if $is_praticien}}
  oForm.chir_id.value = '{{$mediuser->_id}}';
  {{/if}}
  if(oForm.libelle.value){
    oForm.libelle.value = "Copie de "+oForm.libelle.value;
  } else {
    oForm.libelle.value = "Copie de "+oForm.codes_ccam.value;
  }
  oForm.submit();
}

function refreshListCCAM() {
  var oCcamNode = $("listCodesCcam");

  var oForm = document.editFrm;
  $V(oForm._codes_ccam, "");
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam = aCcam.without("");
  
  var aCodeNodes = [];
  var iCode = 0;
  
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = printf("<button class='remove' type='button' onclick='oCcamField.remove(\"%s\")'>%s<\/button>", sCode, sCode);
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.update(aCodeNodes.join(""));
}

function checkFormSejour() {
  var oForm = document.editFrm;
  return checkForm(oForm) && checkDuree() && checkDureeHospi() && checkCCAM();
}

function checkCCAM() {
  var oForm = document.editFrm;
  if ($V(oForm.for_sejour) == 1) return true;
  
  var sCcam = $V(oForm._codes_ccam);
  if(sCcam != "") {
    if(!oCcamField.add(sCcam,true)) {
      return false;
    }
  }
  oCcamField.remove("XXXXXX");
  var sCodesCcam = oForm.codes_ccam.value;
  var sLibelle = oForm.libelle.value;
  if(sCodesCcam == "" && sLibelle == "") {
    alert("Vous indiquez un acte ou remplir le libellé");
    oForm.libelle.focus();
    return false;
  }
  return true;
}

function checkDureeHospi() {
  var form = document.editFrm;
  if ($V(form.for_sejour) == 1) return true;
  
  field1 = form.type;
  field2 = form.duree_hospi;
  if (field1 && field2) {
    if (field1.value=="comp" && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
      field2.onchange();
      field2.focus();
      return false;
    }
    if (field1.value=="ambu" && field2.value != 0 && field2.value != '') {
      alert('Pour une admission de type Ambulatoire, la durée du séjour doit être de 0 jour.');
      field2.focus();
      return false;
    }
  }
  return true;
}

function checkChir() {
  var form = document.editFrm;
  var field = null;
  
  if (field = form.chir_id) {
    if (field.value == 0) {
      alert("Chirurgien manquant");
      popChir();
      return false;
    }
  }
  return true;
}

function checkDuree() {
  var form = document.editFrm;
  field1 = form._hour_op;
  field2 = form._min_op;
  
  if ($V(form.for_sejour) == 1) return true; // Si mode séjour
  
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps opératoire invalide");
      field1.focus();
      return false;
    }
  }
  return true;
}

function setOperationActive(active) {
  var op = $('operation'),
      form = getForm('editFrm');
  op.setOpacity(active ? 1 : 0.4);
  op.select('input, button, select, textarea').each(Form.Element[active ? 'enable' : 'disable']);
}

function fillClass(element_id, element_class) {
  var split = $V(element_id).split("-");
  var classe = split[0] == "prot" ? "CPrescription" : "CPrescriptionProtocolePack";
  element_class.value =  classe;
  element_id.value = split[1] ? split[1] : '';
}

Main.add(function () {
  var form = getForm('editFrm');
  refreshListCCAM();
  setOperationActive($V(form.for_sejour) == 0);
  oCcamField = new TokenField(form.codes_ccam, { 
    onChange : refreshListCCAM,
    sProps : "notNull code ccam"
  } );
});

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkFormSejour()" class="{{$protocole->_spec}}">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_protocole_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_ccam_object_class" value="COperation" />
{{mb_key object=$protocole}}

{{if $dialog}}
  <input type="hidden" name="postRedirect" value="m=dPplanningOp&a=vw_protocoles&dialog=1" />
{{/if}}


<table class="form">
  {{if $protocole->protocole_id}}
  <tr>
    <td colspan="2" class="title">
      <a class="button new" href="?m={{$m}}&amp;protocole_id=0">
       	Créer un nouveau protocole
			</a>
    </td>
  </tr>
  {{/if}}

  {{mb_include module=system template=inc_form_table_header object=$protocole}}
	
  <tr>
    <th>{{mb_label object=$protocole field="chir_id"}}</th>
    <td>
      <select name="chir_id" class="{{$protocole->_props.chir_id}}"
              onchange="$('editFrm_libelle_protocole').value = '';
                        this.form.protocole_prescription_chir_id.value = '';"
              style="width: 15em;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listPraticiens item=_prat}}
        <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
                {{if $chir->_id == $_prat->_id}}selected="selected"{{/if}}>
        {{$_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$protocole field="function_id"}}</th>
    <td>
      <select name="function_id" class="{{$protocole->_props.function_id}}"
              onchange="$('editFrm_libelle_protocole').value = '';
                        this.form.protocole_prescription_chir_id.value = '';"
              style="width: 15em;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listFunctions item=_function}}
        <option class="mediuser" style="border-color: #{{$_function->color}};" value="{{$_function->_id}}"
                {{if $protocole->function_id == $_function->_id}}selected="selected"{{/if}}>
        {{$_function->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$protocole field="for_sejour"}}</th>
    <td>
      {{mb_field object=$protocole field="for_sejour" onchange="setOperationActive(\$V(this.form.elements[this.name]) != 1)"}}
    </td>
  </tr>
</table>
<table class="main layout">
  <tr>
    <td id="operation" class="halfPane">
      <table class="form">
        <tr>
          <th class="category" colspan="3">
            Informations concernant l'intervention
          </th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="libelle"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="libelle" style="width: 15em"}}</td>
        </tr>

        <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
          <th>
            {{mb_label object=$protocole field="codes_ccam"}}
          </th>
          <td colspan="2">
            <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()"  style="width: 12em" value="" />
            <button class="add notext" type="button" onclick="oCcamField.add($V(this.form._codes_ccam), true)">{{tr}}Add{{/tr}}</button>
            <button class="search notext" type="button" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
            <script type="text/javascript">
              Main.add(function() {
							  var oForm = getForm("editFrm");
                var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
                url.autoComplete(oForm._codes_ccam, '', {
                  minChars: 1,
                  dropdown: true,
                  width: "250px",
                  updateElement: function(selected) {
                    $V(oForm._codes_ccam, selected.down("strong").getText());
                    oCcamField.add($V(oForm._codes_ccam), true);
                  }
                });
              });
							
              CCAMSelector.init = function(){
                this.sForm  = "editFrm";
                this.sView  = "_codes_ccam";
                this.sChir  = "chir_id";
                this.sClass = "_ccam_object_class";
                this.pop();
              }
            </script>          
          </td>
        </tr>

        <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
          <th>
            Liste des codes CCAM
            {{mb_field object=$protocole field="codes_ccam" hidden=1}}
          </th>
          <td colspan="2" class="text" id="listCodesCcam"></td>
        </tr>
  
        <tr>
          <th>{{mb_label object=$protocole field="cote"}}</th>
          <td colspan="2">
            {{mb_field object=$protocole field="cote" style="width: 15em" emptyLabel="Choose"}}
          </td>
        </tr> 
        
        <tr>
          <th>
            {{mb_label object=$protocole field="_hour_op"}}
          </th>
          <td colspan="2">
            <select name="_hour_op" class="notNull num min|0">
            {{foreach from=$hours|smarty:nodefaults key=key item=hour}}
              <option value="{{$key}}" {{if (!$protocole && $key == 1) || $protocole->_hour_op == $key}} selected="selected" {{/if}}>{{$key}}</option>
            {{/foreach}}
            </select> h 
            <select name="_min_op">
            {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}" {{if (!$protocole && $min == 0) || $protocole->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
            {{/foreach}}
            </select> mn
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="duree_uscpo"}}</th>
          <td colspan="2">{{mb_field object=$protocole field="duree_uscpo" increment=true form=editFrm size="2"}} {{tr}}night{{/tr}}(s)</td>
        </tr>
        
        <tr>
          <td colspan="3"><hr /></td>
        </tr>
        
        <tr>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="examen"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="materiel"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="rques_operation"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="examen" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="materiel" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="rques_operation" rows="3"}}</td>
        </tr>
  
        <tr>
          <td class="text">{{mb_label object=$protocole field="depassement"}}</td>
          <td class="text">{{mb_label object=$protocole field="forfait"}}</td>
          <td class="text">{{mb_label object=$protocole field="fournitures"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="depassement" size="4"}}</td>
          <td>{{mb_field object=$protocole field="forfait" size="4"}}</td>
          <td>{{mb_field object=$protocole field="fournitures" size="4"}}</td>
        </tr>
      </table>
    </td>
    
    <td id="sejour">
      <table class="form">
        <tr>
         <th class="category" colspan="2">Informations concernant le séjour</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="libelle_sejour"}}</th>
          <td>{{mb_field object=$protocole field="libelle_sejour" style="width: 15em;"}}</td>
        </tr>
        
        <tr>
				  <th>
				    {{mb_label object=$protocole field="service_id"}}
				  </th>
				  <td>
				    <select name="service_id" class="{{$protocole->_props.service_id}}" style="width: 15em;">
				      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
				      {{foreach from=$listServices item=_service}}
				      <option value="{{$_service->_id}}" {{if $protocole->service_id == $_service->_id}} selected="selected" {{/if}}>
				        {{$_service->_view}}
				      </option>
				      {{/foreach}}
				    </select>
				  </td>
				</tr>
        
        <tr>
          <th>
            {{mb_label object=$protocole field="DP"}}
          </th>
          <td>
	          <script type="text/javascript">
            Main.add(function(){
	            var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
				      url.autoComplete(getForm("editFrm").keywords_code, '', {
				        minChars: 1,
				        dropdown: true,
				        width: "250px",
                select: "code",
                afterUpdateElement: function(oHidden) {
                  $V(getForm("editFrm").DP, oHidden.value);
                }
				      });
            });
            </script>
            
				    <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$protocole->DP}}" style="width: 12em;" />
	          <input type="hidden" name="DP" value="{{$protocole->DP}}" onchange="$V(this.form.keywords_code, this.value)"/>
            <button type="button" class="cancel notext" onclick="$V(this.form.DP, '');" />
	          <button type="button" class="search notext" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
              <script type="text/javascript">
                CIM10Selector.init = function(){
                  this.sForm = "editFrm";
                  this.sView = "DP";
                  this.sChir = "chir_id";
                  this.pop();
                }
              </script>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="duree_hospi"}}</th>
          <td>{{mb_field object=$protocole field="duree_hospi" size="2"}} {{tr}}night{{/tr}}(s)</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="type"}}</th>
          <td>{{mb_field object=$protocole field="type" style="width: 15em;"}}</td>
        </tr>
        
        {{if $conf.dPplanningOp.CSejour.show_type_pec}}
          <tr>
            <th>{{mb_label object=$protocole field="type_pec"}}</th>
            <td>{{mb_field object=$protocole field="type_pec" style="width: 15em;" emptyLabel="Choose" }}</td>
          </tr>
        {{/if}}
        <tr>
          <td colspan="2"><hr /></td>
        </tr>
        
        <tr>
          <td>{{mb_label object=$protocole field="convalescence"}}</td>
          <td>{{mb_label object=$protocole field="rques_sejour"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="convalescence" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="rques_sejour" rows="3"}}</td>
        </tr>
        {{if array_key_exists("dPprescription", $modules)}}
        <tr>
          <th>
            <script type="text/javascript">
            Main.add(function(){
              var form = getForm("editFrm");
              var url = new Url("dPprescription", "httpreq_vw_select_protocole");
              var autocompleter = url.autoComplete(form.libelle_protocole, null, {
                minChars: 2,
                dropdown: true,
                width: "250px",
                valueElement: form.elements.protocole_prescription_chir_id,
                updateElement: function(selectedElement) {
                  var node = $(selectedElement).down('.view');
                  $V(form.libelle_protocole, node.innerHTML.replace("&lt;", "<").replace("&gt;",">"));
                  if (autocompleter.options.afterUpdateElement)
                    autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
                },
                callback: function(input, queryString){
                  return (queryString + "&praticien_id=" + $V(form.chir_id));
                }
              });
            });
            </script>
            {{mb_label object=$protocole field="protocole_prescription_chir_id"}}
          </th>
          <td>
            <input type="text" name="libelle_protocole" id="editFrm_libelle_protocole" class="autocomplete str"
              value="{{if $protocole->_id}}{{$protocole->_ref_protocole_prescription_chir->libelle}}{{/if}}"  style="width: 12em;"/>
            <input type="hidden" name="protocole_prescription_chir_id" value="{{$protocole->protocole_prescription_chir_id}}"
              onchange="fillClass(this.form.protocole_prescription_chir_id, this.form.protocole_prescription_chir_class);
              submitFormAjax(this.form, 'systemMsg');"/>
            <input type="hidden" name="protocole_prescription_chir_class" value="{{$protocole->protocole_prescription_chir_class}}"/>
          </td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;">
    {{if $protocole->protocole_id}}
      <button class="submit" type="button" onclick="copier()">Dupliquer</button>
      <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$protocole->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
    </td>
  </tr>
</table>

</form>
