<script language="Javascript" type="text/javascript">
{{if $can->admin}}
function filterAllEi(field){
  if (field.name == "selected_user_id") {
    $("tab-incident").select('a[href="#ALL_TERM"] span.user')[0].update(field.options[field.selectedIndex].text);
  }
  
  var url = new Url;
  url.setModuleAction("dPqualite", "httpreq_vw_allEi");
  url.addElement(field);
  url.requestUpdate('ALL_TERM');
}

function annuleFiche(oForm,annulation){
  oForm.annulee.value = annulation;
  oForm._validation.value = 1;
  oForm.submit();
}

function refusMesures(oForm){
  if(oForm.remarques.value == ""){
    alert("{{tr}}CFicheEi-msg-validdoc{{/tr}}");
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

function loadListFiches(type, first) {
  if ($(type).empty() || first) {
    var url = new Url;
    url.setModuleAction("dPqualite", "httpreq_vw_allEi");
    url.addParam("selected_fiche_id", '{{$selected_fiche_id}}');
    url.addParam("type", type);
    url.addParam("first", first);
    url.requestUpdate(type);
  }
}

function printIncident(ficheId){
  var url = new Url;
  url.setModuleAction("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, "printFicheEi");
  return;
}

Main.add(function() {
  var tab = Control.Tabs.create('tab-incident', true);
  loadListFiches(tab.activeContainer.id, '{{$first}}');
});
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <ul id="tab-incident" class="control_tabs full_width">
        {{if !$can->admin && $can->edit}}
        <li onclick="loadListFiches('ATT_CS')"><a href="#ATT_CS">{{tr}}_CFicheEi_acc-ATT_CS{{/tr}} (<span>{{$listCounts.ATT_CS}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ATT_QUALITE')"><a href="#ATT_QUALITE">{{tr}}_CFicheEi_acc-ATT_QUALITE{{/tr}} (<span>{{$listCounts.ATT_QUALITE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ALL_TERM')"><a href="#ALL_TERM">{{tr}}_CFicheEi_acc-ALL_TERM{{/tr}} (<span>{{$listCounts.ALL_TERM}}</span>)</a></li>
        {{elseif $can->admin}}
        <li onclick="loadListFiches('VALID_FICHE')"><a href="#VALID_FICHE">{{tr}}_CFicheEi_acc-VALID_FICHE{{/tr}} (<span>{{$listCounts.VALID_FICHE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ATT_CS')"><a href="#ATT_CS">{{tr}}_CFicheEi_acc-ATT_CS_adm{{/tr}} (<span>{{$listCounts.ATT_CS}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ATT_QUALITE')"><a href="#ATT_QUALITE">{{tr}}_CFicheEi_acc-ATT_QUALITE_adm{{/tr}} (<span>{{$listCounts.ATT_QUALITE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ATT_VERIF')"><a href="#ATT_VERIF">{{tr}}_CFicheEi_acc-ATT_VERIF{{/tr}} (<span>{{$listCounts.ATT_VERIF}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ATT_CTRL')"><a href="#ATT_CTRL">{{tr}}_CFicheEi_acc-ATT_CTRL{{/tr}} (<span>{{$listCounts.ATT_CTRL}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ALL_TERM')"><a href="#ALL_TERM">{{tr}}_CFicheEi_allfiches{{/tr}} <span class="user">{{if $selectedUser->_id}}pour {{$selectedUser->_view}}{{/if}}</span> (<span>{{$listCounts.ALL_TERM}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onclick="loadListFiches('ANNULE')"><a href="#ANNULE">{{tr}}_CFicheEi_acc-ANNULE{{/tr}} (<span>{{$listCounts.ANNULE}}</span>)</a></li>
        {{/if}}
        <li class="linebreak"></li>
        <li onclick="loadListFiches('AUTHOR')"><a href="#AUTHOR">{{tr}}_CFicheEi_acc-AUTHOR{{/tr}} (<span>{{$listCounts.AUTHOR}}</span>)</a></li>
      </ul>
      <hr class="control_tabs" />
      
      {{if !$can->admin && $can->edit}}
        <div id="ATT_CS" style="display: none;"></div>
        <div id="ATT_QUALITE" style="display: none;"></div>
        <div id="ALL_TERM" style="display: none;"></div>
      {{elseif $can->admin}}
        <div id="VALID_FICHE" style="display: none;"></div>
        <div id="ATT_CS" style="display: none;"></div>
        <div id="ATT_QUALITE" style="display: none;"></div>
        <div id="ATT_VERIF" style="display: none;"></div>
        <div id="ATT_CTRL" style="display: none;"></div>
        <div id="ALL_TERM" style="display: none;"></div>
        <div id="ANNULE" style="display: none;"></div>
      {{/if}}
      
      <div id="AUTHOR" style="display: none;"></div>
    </td>
    
    <td class="halfPane">
    {{if $fiche->_id}}
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
        
        {{if $can->admin && !$fiche->date_validation && !$fiche->annulee}}
        <tr>
          <th>{{mb_label object=$fiche field="degre_urgence"}}</th>
          <td>
            <select name="degre_urgence" class="notNull {{$fiche->_props.degre_urgence}}">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{html_options options=$fiche->_enumsTrans.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="gravite"}}</th>
          <td>
            <select name="gravite" class="notNull {{$fiche->_props.gravite}}">
              <option value="">&mdash;{{tr}}Choose{{/tr}} &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.gravite selected=$fiche->gravite}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="vraissemblance"}}</th>
          <td>
            <select name="vraissemblance" class="notNull {{$fiche->_props.vraissemblance}}">
              <option value="">&mdash;{{tr}}Choose{{/tr}} &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.vraissemblance selected=$fiche->vraissemblance}}
            </select>
          </td>
        </tr>
        <tr>     
          <th>{{mb_label object=$fiche field="plainte"}}</th>
          <td>
            <select name="plainte" class="notNull {{$fiche->_props.plainte}}">
              <option value="">&mdash;{{tr}}Choose{{/tr}} &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.plainte selected=$fiche->plainte}}
            </select>
          </td>
        </tr>
        <tr> 
          <th>{{mb_label object=$fiche field="commission"}}</th>
          <td>
            <select name="commission" class="notNull {{$fiche->_props.commission}}">
              <option value="">&mdash;{{tr}}Choose{{/tr}} &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.commission selected=$fiche->commission}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="service_valid_user_id"}}</th>
          <td>
            <select name="service_valid_user_id" class="notNull {{$fiche->_props.service_valid_user_id}}">
            <option value="">&mdash; {{tr}}Choose{{/tr}} &mdash;</option>
            {{foreach from=$listUsersEdit item=currUser}}
            <option value="{{$currUser->user_id}}">{{$currUser->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valid_user_id" value="{{$app->user_id}}" />
            <button class="edit" type="button" onclick="window.location.href='?m={{$m}}&amp;tab=vw_incident&amp;fiche_ei_id={{$fiche->fiche_ei_id}}';">
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
			    	<script type="text/javascript">
			    		Main.add(function() { Calendar.regField("ProcEditFrm", "qualite_date_controle"); } );
			    	</script>
          
            <input type="hidden" name="qualite_user_id" value="{{$app->user_id}}" />
            <input type="hidden" name="qualite_date_controle" value="" />
            <button class="modify" type="submit">
              {{tr}}button-CFicheEi-valid{{/tr}}
            </button>
            <button class="cancel" type="button" onclick="refusMesures(this.form);">
              {{tr}}button-CFicheEi-refus{{/tr}}
            </button>
            <button class="tick" type="button" onclick="this.form.qualite_date_controle.value = '{{$today|date_format:"%Y-%m-%d"}}'; this.form.submit()">
              {{tr}}button-CFicheEi-classer{{/tr}}
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

        {{if !$fiche->qualite_date_verification && !$fiche->qualite_date_controle}}
        <tr>
          <th>{{mb_label object=$fiche field="qualite_date_verification"}}</th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_verification_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_verification" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_verification_trigger" src="./images/icons/calendar.gif" alt="calendar" title="{{tr}}CFicheEi-qualite_date_verification-desc{{/tr}}" />
            <button type="button" class="tick" onclick="this.form.qualite_date_verification.name = 'qualite_date_controle'; this.form.submit();">
              {{tr}}button-CFicheEi-classer{{/tr}}
            </button>
			    	<script type="text/javascript">
			    		Main.add(function() { Calendar.regField("ProcEditFrm", "qualite_date_verification"); } );
			    	</script>
            
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
      
        {{if $can->edit && ($fiche->annulee || $fiche->date_validation)}}
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
    {{else}}
    <div class="big-info">
      Veuillez sélectionner un incident
    </div>
    {{/if}}
    </td>
  </tr>
</table>