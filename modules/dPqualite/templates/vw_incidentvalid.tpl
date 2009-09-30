<script type="text/javascript">
{{if $can->admin}}
function filterAllEi(field){
  if (field.name == "selected_user_id") {
    $("tab-incident").select('a[href="#ALL_TERM"] span.user')[0].update(field.options[field.selectedIndex].text);
  }
  
  var url = new Url("dPqualite", "httpreq_vw_allEi");
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
  }
  else {
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
    var url = new Url("dPqualite", "httpreq_vw_allEi");
    url.addParam("selected_fiche_id", '{{$selected_fiche_id}}');
    url.addParam("type", type);
    url.addParam("first", first);
    url.addFormData(getForm("filter-ei"));
    url.requestUpdate(type);
  }
}

function filterFiches() {
  $$("#tab-incident a").each(function(a){
    $(Url.parse(a.href).fragment).update();
  });
  loadListFiches(tab.activeContainer);
  return false;
}

function printIncident(ficheId){
  var url = new Url("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, "printFicheEi");
}

var tab;
Main.add(function() {
  tab = Control.Tabs.create('tab-incident', true);
  loadListFiches(tab.activeContainer, '{{$first}}');
});
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="filter-ei" action="?" method="get" onsubmit="return filterFiches()">
        {{mb_field object=$filterFiche field=elem_concerne defaultOption=" &ndash; Cet élément concerne" onchange="this.form.onsubmit()"}}
        
        <!--<select name="evenements" onchange="this.form.onsubmit()">
          <option value=""> &ndash; Catégorie</option>
          {{foreach from=$listCategories item=category}}
            <option value="{{$category->_id}}">{{$category}}</option>
          {{/foreach}}
        </select>-->
        
        <button class="search">{{tr}}Filter{{/tr}}</button>
      </form>
      
      <ul id="tab-incident" class="control_tabs full_width">
        {{if !$can->admin && $can->edit}}
        <li onmouseup="loadListFiches('ATT_CS')"><a href="#ATT_CS">{{tr}}_CFicheEi_acc-ATT_CS{{/tr}} (<span>{{$listCounts.ATT_CS}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ATT_QUALITE')"><a href="#ATT_QUALITE">{{tr}}_CFicheEi_acc-ATT_QUALITE{{/tr}} (<span>{{$listCounts.ATT_QUALITE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ALL_TERM')"><a href="#ALL_TERM">{{tr}}_CFicheEi_acc-ALL_TERM{{/tr}} (<span>{{$listCounts.ALL_TERM}}</span>)</a></li>
        {{elseif $can->admin}}
        <li onmouseup="loadListFiches('VALID_FICHE')"><a href="#VALID_FICHE">{{tr}}_CFicheEi_acc-VALID_FICHE{{/tr}} (<span>{{$listCounts.VALID_FICHE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ATT_CS')"><a href="#ATT_CS">{{tr}}_CFicheEi_acc-ATT_CS_adm{{/tr}} (<span>{{$listCounts.ATT_CS}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ATT_QUALITE')"><a href="#ATT_QUALITE">{{tr}}_CFicheEi_acc-ATT_QUALITE_adm{{/tr}} (<span>{{$listCounts.ATT_QUALITE}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ATT_VERIF')"><a href="#ATT_VERIF">{{tr}}_CFicheEi_acc-ATT_VERIF{{/tr}} (<span>{{$listCounts.ATT_VERIF}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ATT_CTRL')"><a href="#ATT_CTRL">{{tr}}_CFicheEi_acc-ATT_CTRL{{/tr}} (<span>{{$listCounts.ATT_CTRL}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ALL_TERM')"><a href="#ALL_TERM">{{tr}}CFicheEi.all{{/tr}} <span class="user">{{if $selectedUser->_id}}pour {{$selectedUser->_view}}{{/if}}</span> (<span>{{$listCounts.ALL_TERM}}</span>)</a></li>
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('ANNULE')"><a href="#ANNULE">{{tr}}_CFicheEi_acc-ANNULE{{/tr}} (<span>{{$listCounts.ANNULE}}</span>)</a></li>
        {{/if}}
        <li class="linebreak"></li>
        <li onmouseup="loadListFiches('AUTHOR')"><a href="#AUTHOR">{{tr}}_CFicheEi_acc-AUTHOR{{/tr}} (<span>{{$listCounts.AUTHOR}}</span>)</a></li>
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
          <td>{{mb_field object=$fiche field="degre_urgence" emptyLabel="Choose"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="gravite"}}</th>
          <td>{{mb_field object=$fiche field="gravite" emptyLabel="Choose"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="vraissemblance"}}</th>
          <td>{{mb_field object=$fiche field="vraissemblance" emptyLabel="Choose"}}</td>
        </tr>
        <tr>     
          <th>{{mb_label object=$fiche field="plainte"}}</th>
          <td>{{mb_field object=$fiche field="plainte" emptyLabel="Choose"}}</td>
        </tr>
        <tr> 
          <th>{{mb_label object=$fiche field="commission"}}</th>
          <td>{{mb_field object=$fiche field="commission" emptyLabel="Choose"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fiche field="service_valid_user_id"}}</th>
          <td>
            <select name="service_valid_user_id" class="notNull {{$fiche->_props.service_valid_user_id}}">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$listUsersEdit item=_user}}
            <option value="{{$_user->_id}}">{{$_user}}</option>
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
			    		Main.add(function() { Calendar.regField(getForm("ProcEditFrm").qualite_date_controle); } );
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
            <input type="hidden" name="qualite_date_verification" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <button type="button" class="tick" onclick="this.form.qualite_date_verification.name = 'qualite_date_controle'; this.form.submit();">
              {{tr}}button-CFicheEi-classer{{/tr}}
            </button>
			    	<script type="text/javascript">
			    		Main.add(function() { Calendar.regField(getForm("ProcEditFrm").qualite_date_verification); } );
			    	</script>
            
          </td>
        </tr>

        {{elseif !$fiche->qualite_date_controle}}
        <tr>
          <th>{{mb_label object=$fiche field="qualite_date_controle"}}</th>
          <td>
            <input type="hidden" name="qualite_date_controle" value="{{$today|date_format:"%Y-%m-%d"}}" class="date" />
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
    <div class="small-info">
      Veuillez séléctionner un incident
    </div>
    {{/if}}
    </td>
  </tr>
</table>