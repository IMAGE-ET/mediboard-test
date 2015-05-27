{{mb_script module="dPplanningOp" script="cim10_selector" ajax=true}}
{{mb_script module="dPplanningOp" script="ccam_selector" ajax=true}}
{{assign var=use_charge_price_indicator value="dPplanningOp CSejour use_charge_price_indicator"|conf:"CGroups-$g"}}

<script>

window.oCcamFieldProtocole = null;

copier = function(){
  var oForm = getForm("editProtocole");
  oForm.protocole_id.value = "";
  {{if $is_praticien}}
  oForm.chir_id.value = '{{$mediuser->_id}}';
  {{/if}}
  if(oForm.libelle.value){
    oForm.libelle.value = "Copie de "+oForm.libelle.value;
  } else {
    oForm.libelle.value = "Copie de "+oForm.codes_ccam.value;
  }
  oForm.onsubmit = function() {
    onSubmitFormAjax(this);
  }
  $V(oForm.callback, "afterCopier");
  oForm.onsubmit();
}

afterCopier = function(id) {
  refreshList(getForm("selectFrm"), null, false);
  Control.Modal.close();
  chooseProtocole(id);
}

refreshListCCAMProtocole = function() {
  var oCcamNode = $("listCodesCcamProtocole");

  var oForm = getForm("editProtocole");
  $V(oForm._codes_ccam, "");
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam = aCcam.without("");
  
  var iCode = 0;
  var sCode;

  oCcamNode.update("");
  while (sCode = aCcam[iCode++]) {
    sCode = sCode.htmlSanitize();
    oCcamNode.insert(DOM.button({
      className: 'remove',
      type: 'button',
      onclick: 'oCcamFieldProtocole.remove(\"'+sCode+'\")'
    }, sCode));
  }
}

checkFormSejour = function() {
  var oForm = getForm("editProtocole");
  return checkDureeProtocole() && checkForm(oForm) && checkDureeHospiProtocole() && checkCCAMProtocole();
}

checkCCAMProtocole = function() {
  var oForm = getForm("editProtocole");
  if ($V(oForm.for_sejour) == 1) return true;

  var sCcam = $V(oForm._codes_ccam);
  if(sCcam != "") {
    if(!oCcamFieldProtocole.add(sCcam,true)) {
      return false;
    }
  }
  oCcamFieldProtocole.remove("XXXXXX");
  var sCodesCcam = oForm.codes_ccam.value;
  var sLibelle = oForm.libelle.value;
  if(sCodesCcam == "" && sLibelle == "") {
    alert("Veuillez indiquer un acte ou remplir le libellé");
    oForm.libelle.focus();
    return false;
  }
  return true;
}

checkDureeHospiProtocole = function() {
  var form = getForm("editProtocole");
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


checkDureeProtocole = function() {
  var form = getForm("editProtocole");
  field1 = form.temp_operation;
  
  if ($V(form.for_sejour) == 1) return true; // Si mode séjour

  if ($V(field1) == '00:00:00') {
    $V(field1, '');
  }
  if (field1 && $V(field1) == "") {
    alert("Temps opératoire invalide");
    field1.focus();
    return false;
  }
  return true;
}

setOperationActive = function(active) {
  var op = $('operation'),
      form = getForm('editProtocole');
  op.setOpacity(active ? 1 : 0.4);
  op.select('input, button, select, textarea').each(Form.Element[active ? 'enable' : 'disable']);
}

fillClass = function(element_id, element_class) {
  var split = $V(element_id).split("-");
  var classe = split[0] == "prot" ? "CPrescription" : "CPrescriptionProtocolePack";
  element_class.value =  classe;
  element_id.value = split[1] ? split[1] : '';
}

applyModifProtocole = function() {
  var form = getForm("editProtocole");
  var type_protocole = ["interv"];
  if($V(form.for_sejour) == 1) {
    type_protocole = ["sejour"];
  }
  refreshList(getForm("selectFrm"), type_protocole, false);
  Control.Modal.close();
}

Main.add(function () {
  var form = getForm('editProtocole');
  refreshListCCAMProtocole();
  setOperationActive($V(form.for_sejour) == 0);

  oCcamFieldProtocole = new TokenField(form.codes_ccam, { 
    onChange : refreshListCCAMProtocole,
    sProps : "notNull code ccam"
  } );
  editHour();

  {{if $use_charge_price_indicator != "no"}}
    updateListCPI(form);
  {{/if}}
});

editHour = function () {
  var form = getForm('editProtocole');
  if (form.duree_hospi.value == 0 && form.type.value == "ambu") {
    $('duree_heure_hospi_view').show();
  }
  else {
    $('duree_heure_hospi_view').hide();
    form.duree_heure_hospi.value = 0;
  }
}

updateListCPI = function(form) {
  var field = form.charge_id;

  var url = new Url("dPplanningOp", "ajax_vw_list_cpi");
  url.addParam("group_id", "{{$g}}");

  url.addParam("type", $V(form.type));

  url.requestUpdate(field, function() {
    if (field.type == "hidden") {
      $V(field, ""); // To check the field
    }
    $V(field, "{{$protocole->charge_id}}", true);
  });
}
</script>


<form name="addBesoinProtocole" method="post">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_besoin_ressource_aed" />
  <input type="hidden" name="besoin_ressource_id" />
  <input type="hidden" name="protocole_id" value="{{$protocole->_id}}" />
  <input type="hidden" name="type_ressource_id" />
</form>

<form name="editProtocole" action="?m={{$m}}" method="post" onsubmit="if(checkFormSejour()) return onSubmitFormAjax(this, {onComplete: applyModifProtocole});" class="{{$protocole->_spec}}">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_protocole_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="_ccam_object_class" value="COperation" />
<input type="hidden" name="callback" value=""/>
{{mb_key object=$protocole}}
<input type="hidden" name="_types_ressources_ids"
  onchange="{{if $protocole->_id}}addBesoins(this.value){{else}}synchronizeTypes($V(this)){{/if}}"/>

{{if $dialog}}
  <input type="hidden" name="postRedirect" value="m=dPplanningOp&a=vw_protocoles&dialog=1" />
{{/if}}


<table class="form">

  {{mb_include module=system template=inc_form_table_header object=$protocole}}
  
  <tr>
    <th>{{mb_label object=$protocole field="chir_id"}}</th>
    <td>
      <select name="chir_id" class="{{$protocole->_props.chir_id}}"
              onchange="$V(this.form.libelle_protocole, '');
                        $V(this.form.protocole_prescription_chir_id, '');"
              style="width: 15em;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser selected=$chir->_id list=$listPraticiens}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$protocole field="function_id"}}</th>
    <td>
      <select name="function_id" class="{{$protocole->_props.function_id}}"
              onchange="$V(this.form.libelle_protocole, '');
                        $V(this.form.protocole_prescription_chir_id, '');"
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
            <button class="add notext" type="button" onclick="oCcamFieldProtocole.add($V(this.form._codes_ccam), true)">{{tr}}Add{{/tr}}</button>
            <button class="search notext" type="button" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
            <script>
              Main.add(function() {
                var oForm = getForm("editProtocole");
                var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
                url.autoComplete(oForm._codes_ccam, '', {
                  minChars: 1,
                  dropdown: true,
                  width: "250px",
                  updateElement: function(selected) {
                    $V(oForm._codes_ccam, selected.down("strong").getText());
                    oCcamFieldProtocole.add($V(oForm._codes_ccam), true);
                  }
                });
              });
              
              CCAMSelector.init = function(){
                this.sForm  = "editProtocole";
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
          <td colspan="2" class="text" id="listCodesCcamProtocole"></td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field=exam_extempo}}</th>
          <td colspan="2">{{mb_field object=$protocole field=exam_extempo}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="cote"}}</th>
          <td colspan="2">
            {{mb_field object=$protocole field="cote" style="width: 15em" emptyLabel="Choose"}}
          </td>
        </tr> 
        
        <!-- Choix du type d'anesthésie -->
        {{if $conf.dPplanningOp.COperation.easy_type_anesth}}
          <tr>
            <th>{{mb_label object=$protocole field="type_anesth"}}</th>
            <td colspan="2">
              <select name="type_anesth" style="width: 15em;">
                <option value="">&mdash; Anesthésie</option>
                {{foreach from=$listAnesthType item=curr_anesth}}
                  {{if $curr_anesth->actif || $protocole->type_anesth == $curr_anesth->type_anesth_id}}
                    <option value="{{$curr_anesth->type_anesth_id}}" {{if $protocole->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
                      {{$curr_anesth->name}} {{if !$curr_anesth->actif && $protocole->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
                    </option>
                  {{/if}}
                {{/foreach}}
              </select>
            </td>
          </tr> 
        {{/if}}
        
        <tr>
          <th>{{mb_label object=$protocole field=temp_operation}}</th>
          <td colspan="2">{{mb_field object=$protocole field=temp_operation form=editProtocole class="notNull"}}</td>
        </tr>
        
        {{if $conf.dPplanningOp.COperation.show_duree_uscpo >= 1}}
          <tr>
            <th>{{mb_label object=$protocole field="duree_uscpo"}}</th>
            <td colspan="2">{{mb_field object=$protocole field="duree_uscpo" increment=true form=editProtocole size="2"}} {{tr}}night{{/tr}}(s)</td>
          </tr>
        {{/if}}
        
        <tr>
          <th>{{mb_label object=$protocole field=duree_preop}}</th>
          <td colspan="2">{{mb_field object=$protocole field=duree_preop form=editProtocole }}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field=presence_preop}}</th>
          <td colspan="2">{{mb_field object=$protocole field=presence_preop form=editProtocole }}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$protocole field=presence_postop}}</th>
          <td colspan="2">{{mb_field object=$protocole field=presence_postop form=editProtocole }}</td>
        </tr>
        
        <tr>
          <td colspan="3"><hr /></td>
        </tr>
        
        {{if $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
          <tr>
            <td></td>
            <td>
              {{mb_include module=dPbloc template=inc_button_besoins_ressources object_id=$protocole->_id type=protocole_id}}
            </td>
            <td></td>
        {{/if}}
        
        <tr>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="examen"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="materiel"}}</td>
          <td class="text" style="width: 33%;">{{mb_label object=$protocole field="exam_per_op"}}</td>
        </tr>

        <tr>
          <td>{{mb_field object=$protocole field="examen" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="materiel" rows="3"}}</td>
          <td>{{mb_field object=$protocole field="exam_per_op" rows="3"}}</td>
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
        <tr>
          <td colspan="3">{{mb_label object=$protocole field="rques_operation"}}
              {{mb_field object=$protocole field="rques_operation"}}
          </td>
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

        {{if "dPplanningOp CSejour required_uf_soins"|conf:"CGroups-$g" != "no"}}
          <tr>
            <th>{{mb_label object=$protocole field="uf_soins_id"}}</th>
            <td>
              <select name="uf_soins_id" class="ref">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$ufs.soins item=_uf}}
                  <option value="{{$_uf->_id}}" {{if $protocole->uf_soins_id == $_uf->_id}}selected="selected"{{/if}}>
                    {{mb_value object=$_uf field=libelle}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          </tr>
        {{/if}}
        
        <tr>
          <th>
            {{mb_label object=$protocole field="DP"}}
          </th>
          <td>
            <script>
            Main.add(function(){
              var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
              url.autoComplete(getForm("editProtocole").keywords_code, '', {
                minChars: 1,
                dropdown: true,
                width: "250px",
                select: "code",
                afterUpdateElement: function(oHidden) {
                  $V(getForm("editProtocole").DP, oHidden.value);
                }
              });
            });
            </script>
            
            <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$protocole->DP}}" style="width: 12em;" />
            <input type="hidden" name="DP" value="{{$protocole->DP}}" onchange="$V(this.form.keywords_code, this.value)"/>
            <button type="button" class="cancel notext" onclick="$V(this.form.DP, '');"></button>
            <button type="button" class="search notext" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
              <script>
                CIM10Selector.init = function(){
                  this.sForm = "editProtocole";
                  this.sView = "DP";
                  this.sChir = "chir_id";
                  this.pop();
                }
              </script>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$protocole field="duree_hospi"}}</th>
          <td>{{mb_field object=$protocole field="duree_hospi" size="2" onchange="editHour();"}} {{tr}}night{{/tr}}(s)</td>
        </tr>

        <tr id="duree_heure_hospi_view">
          <th>{{mb_label object=$protocole field="duree_heure_hospi"}}</th>
          <td>{{mb_field object=$protocole field="duree_heure_hospi" size="2"}} {{tr}}hour{{/tr}}(s)</td>
        </tr>
        {{if $use_charge_price_indicator != "no"}}
          <tr>
            <th>{{mb_label object=$protocole field="type"}}</th>
            <td>{{mb_field object=$protocole field="type" style="width: 15em;" onchange="updateListCPI(this.form); editHour();"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$protocole field="charge_id"}}</th>
            <td><select class="ref" name="charge_id"></select></td>
          </tr>
        {{else}}
          <tr>
            <th>{{mb_label object=$protocole field="type"}}</th>
            <td>{{mb_field object=$protocole field="type" style="width: 15em;" onchange="editHour();"}}</td>
          </tr>
        {{/if}}
        <tr>
          <th></th>
          <td>
            {{mb_include module=planningOp template=inc_ufs_sejour_protocole object=$protocole}}
          </td>
        </tr>
        {{if $conf.dPplanningOp.CSejour.show_type_pec}}
          <tr>
            <th>{{mb_label object=$protocole field="type_pec"}}</th>
            <td>{{mb_field object=$protocole field="type_pec" typeEnum="radio"}}</td>
          </tr>
        {{/if}}
        {{if $conf.dPplanningOp.CSejour.show_facturable}}
          <tr>
            <th>{{mb_label object=$protocole field="facturable"}}</th>
            <td>{{mb_field object=$protocole field="facturable" typeEnum="radio"}}</td>
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
            <script>
            Main.add(function(){
              var form = getForm("editProtocole");
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
                  return (queryString + "&praticien_id=" + $V(form.chir_id) + "&function_id=" + $V(form.function_id));
                }
              });
            });
            </script>
            {{mb_label object=$protocole field="protocole_prescription_chir_id"}}
          </th>
          <td>
            <input type="text" name="libelle_protocole" id="editProtocole_libelle_protocole" class="autocomplete str"
              value="{{if $protocole->_id && $protocole->_ref_protocole_prescription_chir}}{{$protocole->_ref_protocole_prescription_chir->libelle}}{{/if}}"  style="width: 12em;"/>
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
      <button class="copy" type="button" onclick="copier()">Dupliquer</button>
      <button class="submit" type="button" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{ajax: 1, typeName:'le {{$protocole->_view|smarty:nodefaults|JSAttribute}}'}, {onComplete: applyModifProtocole})">
        {{tr}}Delete{{/tr}}
      </button>
    {{else}}
      <button id="didac_button_create_edit_protocole" class="submit" type="button" onclick="this.form.onsubmit();">{{tr}}Create{{/tr}}</button>
    {{/if}}
    </td>
  </tr>
</table>

</form>
