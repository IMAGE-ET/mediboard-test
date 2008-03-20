{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

var Tarif = {
  add: function(value){
    var oForm = document.editFrm;
    if(oForm.secteur1.value==''){
      oForm.secteur1.value = 0;
    } 
    oForm.secteur1.value = parseFloat(oForm.secteur1.value) + parseFloat(value);
  },
  
  del: function(value){
    var oForm = document.editFrm;
    if(oForm.secteur1.value==''){
      oForm.secteur1.value = 0;
    } 
    oForm.secteur1.value = parseFloat(oForm.secteur1.value) - parseFloat(value);
    round(oForm.secteur1.value,2);
    
  }
}


// Permet d'ajouter un code NGAP au tokenField
function addCodeNgap(){
  var oForm = document.editFrm;
  var _quantite_ngap    = oForm._quantite_ngap.value;
  var _code_ngap        = oForm._code_ngap.value;
  var _coefficient_ngap = oForm._coefficient_ngap.value;
  var code_ngap = _quantite_ngap+"-"+_code_ngap+"-"+_coefficient_ngap;
  
  oNgapField.add(code_ngap);
}

function refreshTotal(){
  var oForm = document.editFrm;
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;
  if(secteur1 == ""){
    secteur1 = 0;
  }
  if(secteur2 == ""){
    secteur2 = 0;
  }
  oForm._somme.value = parseFloat(secteur1) + parseFloat(secteur2);
  oForm._somme.value = Math.round(oForm._somme.value*100)/100;
}

function modifSecteur2(){
  var oForm = document.editFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  if(somme == ""){
    somme = 0;
  }
  if(secteur1 == ""){
    secteur = 0;
  }
  oForm.secteur2.value = parseFloat(somme) - parseFloat(secteur1); 
  oForm.secteur2.value = Math.round(oForm.secteur2.value*100)/100;
}

var oCcamField = null;

function refreshListCCAM() {
  oCcamNode = document.getElementById("listCodesCcam");
  
  var oForm = document.editFrm;
  var aCcam = oForm.codes_ccam.value.split("|");

  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode.substr(0,11);
      sCodeNode += "<button class='cancel notext' type='button' onclick='oCcamField.remove(\"" + sCode + "\")'>";
      sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);  
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
}


function refreshListNGAP(){
  var oForm = document.editFrm;
  var aNgap = oForm.codes_ngap.value.split("|");
  aNgapNode = document.getElementById('listCodesNGAP');
  var aCodeNodesNgap = new Array();
  var iCodeNgap = 0;
  while(sCode = aNgap[iCodeNgap++]){
    var explodeCode = sCode.split("-");
    var _quantite = explodeCode[0];
    var _code = explodeCode[1];
    var _coefficient = explodeCode[2];
    var sCode_ = _quantite+"-"+_code+"-"+_coefficient;
    var sCodeNode = sCode_;
        sCodeNode += "<button class='cancel notext' type='button' onclick='oNgapField.remove(\"" + sCode + "\")'>";
        sCodeNode += "<\/button>";
    aCodeNodesNgap.push(sCodeNode);
  }
  aNgapNode.innerHTML = aCodeNodesNgap.join(" / ");
}



function updateTokenCcam(){
  refreshListCCAM();    
  document.editFrm._codeCCAM.value = "";
}

function updateTokenNgap(){
  refreshListNGAP();
  document.editFrm._quantite_ngap.value = "";
  document.editFrm._code_ngap.value = "";
  document.editFrm._coefficient_ngap.value = "";
}

function pageMain() {
  refreshTotal();

  refreshListCCAM();
  refreshListNGAP();

  // Creation du tokenField
  oCcamField = new TokenField(document.editFrm.codes_ccam, {   
    onChange : updateTokenCcam
  } );
  
  oNgapField = new TokenField(document.editFrm.codes_ngap, {
    onChange : updateTokenNgap
  } );

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <a class="buttonnew" href="?m={{$m}}&amp;tarif_id=null">Créer un nouveau tarif</a>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="3">Tarifs du praticien</th>
        </tr>
        {{if $is_praticien=="0" && $is_admin_or_secretaire=="0"}}
        <tr>
          <td class="text">
            <div class="big-info">
              N'étant pas praticien, vous n'avez pas accès à la liste de tarifs personnels.
            </div>
          </td>
        </tr>
        {{/if}}
        {{if $is_admin_or_secretaire=="1"}}
        <tr>
          <td colspan="3">
            <form action="?" name="selection" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="tarifPrat" onchange="this.form.submit()">
                <option value="">&mdash; Aucun praticien</option>
                {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}"
                {{if $curr_prat->_id == $tarifMediuser->_id}}selected="selected"{{/if}}>
                  {{$curr_prat->_view}}
                </option>
                {{/foreach}}
              </select>
            </form>
          </td>
        </tr>
        {{/if}}
        {{if $is_praticien=="1" || $is_admin_or_secretaire=="1"}}
        <tr>
          <th>Nom</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
        </tr>
        {{foreach from=$listeTarifsChir item=curr_tarif}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} &euro;</a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} &euro;</a>
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
      </table>
    
      <table class="tbl">
        <tr>
          <th colspan="3">Tarifs du cabinet</th>
        </tr>
        <tr>
          <th>Nom</th>
          <th>Secteur 1</th>
          <th>Secteur 2</th>
        </tr>
        {{foreach from=$listeTarifsSpe item=curr_tarif}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} &euro;</a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} &euro;</a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_tarif_aed" />
      {{mb_field object=$tarif field="tarif_id" hidden=1 prop=""}}
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="chir_id" value="{{$tarifMediuser->user_id}}" />
      {{mb_field object=$tarifMediuser field="function_id" hidden=1 prop=""}}
      <table class="form">
        {{if $tarif->tarif_id}}
        <tr><th class="category" colspan="2">Modifier ce tarif</th></tr>
        {{else}}
        <tr><th class="category" colspan="2">Créer un nouveau tarif</th></tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$tarif field="_type"}}</th>
          <td>
            {{if $is_praticien}}
            <select name="_type">
              <option value="chir" {{if $tarif->chir_id}} selected="selected" {{/if}}>Tarif personnel</option>
              <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
            </select>
            {{elseif !$tarif->_id || $tarif->function_id}}
            <input type="hidden" name="_type" value="function" />
            Tarif de cabinet
            {{else}}
            <input type="hidden" name="_type" value="chir" />
            Tarif du Dr. {{$tarifMediuser->_view}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$tarif field="description"}}</th>
          <td>
            {{mb_field object=$tarif field="description"}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$tarif field="codes_ccam" defaultFor="_codeCCAM"}}
          </th>
          <td>
            <input type="text" name="_codeCCAM" size="10" value="" />
            <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)">{{tr}}Add{{/tr}}</button>
            <input type="hidden" name="_codable_class" value="CConsultation" />
         <button type="button" class="search notext" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>             
            <script type="text/javascript">
           CCAMSelector.init = function(){
             this.sForm  = "editFrm";
             this.sView  = "_codeCCAM";
             //this.sTarif = "_tarif";
             this.sChir  = "chir_id";
             this.sClass = "_codable_class";
             this.pop();
           }
         </script>
          </td>
          </tr>
          <tr>
            <th>
              Liste des codes CCAM
              {{mb_field object=$tarif field="codes_ccam" onchange="refreshListCCAM();" hidden=1 prop=""}}
            </th>
            <td colspan="2" class="text" id="listCodesCcam">
            </td>
          </tr>
          <tr>
            <th>
              Ajout d'un code NGAP
              {{mb_field object=$tarif field="codes_ngap" hidden="1"}}
            </th>
            <td>
                Quantite:<input name="_quantite_ngap" type="text" size="3" /> 
                Code:<input name="_code_ngap" type="text" size="3" />
                Coefficient:<input name="_coefficient_ngap" type="text" size="3" />
                <button class="tick notext" type="button" onclick="addCodeNgap()">Ajouter Code NGAP</button>
            </td>
          </tr>
          <tr>
            <th>Liste des codes NGAP</th>
            <td colspan="2" class="text" id="listCodesNGAP">
            </td>
          </tr>
        <tr>
          <th>{{mb_label object=$tarif field="secteur1"}}</th>
          <td>{{mb_field object=$tarif field="secteur1" size="6" onChange="refreshTotal();"}}<input type="hidden" name="_tarif" /></td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field="secteur2"}}</th>
          <td>{{mb_field object=$tarif field="secteur2" size="6" onChange="refreshTotal();"}}</td>
        </tr>
        <tr>
          <th>Somme</th>
          <td>
            {{mb_field object=$tarif field="_somme" onchange="modifSecteur2()"}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $tarif->tarif_id}}
            <button class="modify" type="submit">Modifier</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le tarif',objName:'{{$tarif->description|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit" name="btnFuseAction">Créer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>