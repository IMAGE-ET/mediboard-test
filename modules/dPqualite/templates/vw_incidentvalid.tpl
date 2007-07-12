<script language="Javascript" type="text/javascript">
{{if $can->edit}}
var oCookie = new CJL_CookieUtil("EIAccordion");
var showTabAcc = 0;

if(oCookie.getSubValue("showTab")){
  showTabAcc = oCookie.getSubValue("showTab");
}

function storeVoletAcc(objAcc){
  var aArray = oAccord.accordionTabs;
  for ( var i=0 ; i < aArray.length ; i++ ){
    if(objAcc == aArray[i]){
      oCookie.setSubValue("showTab", i.toString());
    }
  }
}

function writeHeader(iId, sValue){
  $(iId).innerHTML = sValue;
}
{{/if}}

{{if $can->admin}}
function search_AllEI(){
  var oForm = document.EiALL_TERM;
  var url = new Url;
  url.setModuleAction("dPqualite", "httpreq_vw_allEi");
  url.addParam("allEi_user_id", oForm.allEi_user_id.value);
  url.requestUpdate('QualAllEIContent');
}

function annuleFiche(oForm,annulation){
  oForm.annulee.value = annulation;
  oForm._validation.value = 1;
  oForm.submit();
}

function refusMesures(oForm){
  if(oForm.remarques.value == ""){
    alert("{{tr}}msg-CFicheEi-validdoc{{/tr}}");
    oForm.remarques.focus();
  }else{
    oForm.service_date_validation.value = "";
    oForm._validation.value= 1;
    oForm.submit();
  }
}

function saveVerifControle(oForm){
  oForm._validation.value= 1;
  oForm.submit();  
}
{{/if}}

function printIncident(ficheId){
  var url = new Url;
  url.setModuleAction("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, "printFicheEi");
  return;
}

{{if  $can->admin && $fiche->qualite_date_validation && (!$fiche->qualite_date_verification || !$fiche->qualite_date_controle)}}
function pageMain() {
  {{if !$fiche->qualite_date_verification}}
  regFieldCalendar("ProcEditFrm", "qualite_date_verification");
  {{else}}
  regFieldCalendar("ProcEditFrm", "qualite_date_controle");
  {{/if}}  
}
{{/if}}
</script>
{{assign var="listeFichesTitle" value=""}}
{{assign var="voletAcc" value=""}}
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $can->admin || $can->edit}}
      <div class="accordionMain" id="accordionConsult">
        {{if !$can->admin}}
        
        <div id="CSATraiter">
          <div id="CSATraiterHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_CS{{/tr}} ({{$listFiches.ATT_CS|@count}})
		  </div>
		  <div id="CSATraiterContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CS}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="CSEnCours">
          <div id="CSEnCoursHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_QUALITE{{/tr}} ({{$listFiches.ATT_QUALITE|@count}})
		  </div>
		  <div id="CSEnCoursContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_QUALITE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
		<div id="CSAllEI">
          <div id="CSAllEIHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ALL_TERM{{/tr}} ({{$listFiches.ALL_TERM|@count}})
		  </div>
		  <div id="CSAllEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ALL_TERM}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
        {{else}}
        
        <div id="QualNewFiches">
          <div id="QualNewFichesHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-VALID_FICHE{{/tr}} ({{$listFiches.VALID_FICHE|@count}})
		  </div>
		  <div id="QualNewFichesContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.VALID_FICHE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualAttCS">
          <div id="QualAttCSHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_CS_adm{{/tr}} ({{$listFiches.ATT_CS|@count}})
		  </div>
		  <div id="QualAttCSContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CS}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualValidMesures">
          <div id="QualValidMesuresHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_QUALITE_adm{{/tr}} ({{$listFiches.ATT_QUALITE|@count}})
		  </div>
		  <div id="QualValidMesuresContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_QUALITE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualVerif">
          <div id="QualVerifHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_VERIF{{/tr}} ({{$listFiches.ATT_VERIF|@count}})
		  </div>
		  <div id="QualVerifContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_VERIF}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualCtrl">
          <div id="QualCtrlHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ATT_CTRL{{/tr}} ({{$listFiches.ATT_CTRL|@count}})
		  </div>
		  <div id="QualCtrlContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CTRL}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
		<div id="QualAllEI">
          <div id="QualAllEIHeader" class="accordionTabTitleBar">
		    {{if $allEi_user_id}}{{tr}}_CFicheEi_allfichesuser{{/tr}} {{$listUsersTermine.$allEi_user_id->_view}}{{else}}{{tr}}_CFicheEi_allfiches{{/tr}}{{/if}} ({{$listFiches.ALL_TERM|@count}})
		  </div>
		  <div id="QualAllEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ALL_TERM}}
            {{assign var="voletAcc" value="ALL_TERM"}}
            {{include file="inc_ei_liste.tpl"}}
            {{assign var="voletAcc" value=""}}
		  </div>
		</div>
		
		<div id="QualAnnuleEI">
          <div id="QualAnnuleEIHeader" class="accordionTabTitleBar">
		    {{tr}}_CFicheEi_acc-ANNULE{{/tr}} ({{$listFiches.ANNULE|@count}})
		  </div>
		  <div id="QualAnnuleEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ANNULE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
        {{/if}}

      </div>

      {{/if}}
      <br />
      {{assign var="listeFichesTitle" value="Mes fiches d'EI"}}
      {{assign var="listeFiches" value=$listFiches.AUTHOR}}
      {{include file="inc_ei_liste.tpl"}}
    </td>

    <script language="Javascript" type="text/javascript">
      var oAccord = new Rico.Accordion( $('accordionConsult'), { 
        panelHeight: ViewPort.SetAccordHeight('accordionConsult','ei_liste'),
        showDelay:50,
        onShowTab: storeVoletAcc,
        showSteps:3,
        onLoadShowTab: showTabAcc
      } );
      </script>

    <td class="halfPane">
      {{if $fiche->fiche_ei_id}}
      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="annulee" value="{{$fiche->annulee|default:"0"}}" />
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
      <input type="hidden" name="_validation" value="0" />
      <input type="hidden" name="service_date_validation" value="{{$fiche->service_date_validation}}" />
            
      <table class="form">
        {{include file="inc_incident_infos.tpl"}}
        
      {{if $can->admin && !$fiche->date_validation &&!$fiche->annulee}}
        <tr>
          <th><label for="degre_urgence" title="{{tr}}CFicheEi-degre_urgence-desc{{/tr}}">{{tr}}CFicheEi-degre_urgence{{/tr}}</label></th>
          <td>
            <select name="degre_urgence" class="notNull {{$fiche->_props.degre_urgence}}">
            <option value="">&mdash; {{tr}}select-choice{{/tr}}</option>
            {{html_options options=$fiche->_enumsTrans.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="service_valid_user_id" title="{{tr}}CFicheEi-service_valid_user_id-desc{{/tr}}">{{tr}}CFicheEi-service_valid_user_id{{/tr}}</label></th>
          <td>
            <select name="service_valid_user_id" class="notNull {{$fiche->_props.service_valid_user_id}}">
            <option value="">&mdash; {{tr}}select-choice{{/tr}} &mdash;</option>
            {{foreach from=$listUsersEdit item=currUser}}
            <option value="{{$currUser->user_id}}">{{$currUser->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valid_user_id" value="{{$user_id}}" />
            <button class="edit" type="button" onclick="window.location.href='index.php?m={{$m}}&amp;tab=vw_incident&amp;fiche_ei_id={{$fiche->fiche_ei_id}}';">
              {{tr}}Edit{{/tr}}
            </button>
            <button class="modify" type="submit">
              {{tr}}button-CFicheEi-transmit{{/tr}}
            </button>
            <button class="cancel" type="button" onclick="annuleFiche(this.form,1);" title="{{tr}}_CFicheEi_cancel{{/tr}}">
              {{tr}}Cancel{{/tr}}
            </button>
          </td>
        </tr>
      {{/if}}
      
      {{if $fiche->service_valid_user_id && $fiche->service_valid_user_id==$user && !$fiche->service_date_validation}}
        <tr>
          <th colspan="2" class="category">
            {{tr}}_CFicheEi_validchefserv{{/tr}}
          </th>
        </tr>
        {{if $fiche->remarques}}
        <tr>
          <th><strong>{{tr}}_CFicheEi_invalidBy{{/tr}}</strong></th>
          <td class="text">
            {{$fiche->_ref_qualite_valid->_view}}
          </td>
        </tr>
        <tr>
          <th><strong>{{tr}}CFicheEi-remarques-court{{/tr}}</strong></th>
          <td class="text" style="color:#f00;">
            <strong>{{$fiche->remarques|nl2br}}</strong>
          </td>
        </tr>
        {{/if}}
        <tr>
          <th>
            <label for="service_actions" title="{{tr}}CFicheEi-service_actions-desc{{/tr}}">{{tr}}CFicheEi-service_actions{{/tr}}</label>
          </th>
          <td>
            <textarea name="service_actions" class="notNull {{$fiche->_props.service_actions}}">{{$fiche->service_actions}}</textarea>
          </td>
        </tr>
        <tr>
          <th>
            <label for="service_descr_consequences" title="{{tr}}CFicheEi-service_descr_consequences-desc{{/tr}}">{{tr}}CFicheEi-service_descr_consequences{{/tr}}</label>
          </th>
          <td>
            <textarea name="service_descr_consequences" class="notNull {{$fiche->_props.service_descr_consequences}}">{{$fiche->service_descr_consequences}}</textarea>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="remarques" value="" />
            <button class="modify" type="submit">
              {{tr}}button-CFicheEi-transmit{{/tr}}
            </button>
          </td>
        </tr>
      {{/if}}
      {{if $can->admin && $fiche->service_date_validation}}
        {{if !$fiche->qualite_date_validation}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="qualite_user_id" value="{{$user_id}}" />
            <button class="modify" type="submit">
              {{tr}}button-CFicheEi-valid{{/tr}}
            </button>
            <button class="cancel" type="button" onclick="refusMesures(this.form);">
              {{tr}}button-CFicheEi-refus{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <th>
            <label for="remarques" title="{{tr}}CFicheEi-remarques-desc{{/tr}}">
              {{tr}}CFicheEi-remarques{{/tr}}
            </label>
          </th>
          <td>
            <textarea name="remarques" class="{{$fiche->_props.remarques}}"></textarea>
          </td>
        </tr>
        {{else}}
        {{if !$fiche->qualite_date_verification}}
        <tr>
          <th>{{mb_label object=$fiche field="qualite_date_verification"}}</th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_verification_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_verification" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_verification_trigger" src="./images/icons/calendar.gif" alt="calendar" title="{{tr}}CFicheEi-qualite_date_verification-desc{{/tr}}" />
          </td>
        </tr>
        {{elseif !$fiche->qualite_date_controle}}
        <tr>
          <th>{{mb_label object=$fiche field="qualite_date_controle"}}</th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_controle_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_controle" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_controle_trigger" src="./images/icons/calendar.gif" alt="calendar" title="{{tr}}CFicheEi-qualite_date_controle-desc{{/tr}}" />
          </td>
        </tr>
        {{/if}}
        {{if !$fiche->qualite_date_verification || !$fiche->qualite_date_controle}}
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="button" onclick="saveVerifControle(this.form);">
              {{tr}}Save{{/tr}}
            </button>
          </td>
        </tr>
        {{/if}}
        {{/if}}
      {{/if}}
      
      {{if $can->admin && ($fiche->annulee || $fiche->date_validation)}}
        <tr>
          <td colspan="2" class="button">
            {{if $fiche->annulee}}
            <button class="change" type="button" onclick="annuleFiche(this.form,0);" title="{{tr}}button-CFicheEi-retablir{{/tr}}">
              {{tr}}button-CFicheEi-retablir{{/tr}}
            </button>
            {{else}}
            <button class="print" type="button" onclick="printIncident({{$fiche->fiche_ei_id}});">
              {{tr}}Print{{/tr}}
            </button>
            {{/if}}
          </td>
        </tr>
      {{/if}}

      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>