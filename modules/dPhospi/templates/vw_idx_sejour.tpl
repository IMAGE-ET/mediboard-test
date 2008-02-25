{{mb_include_script module="dPcompteRendu" script="document"}}

{{assign var="do_subject_aed" value="do_sejour_aed"}}
{{assign var="module" value="dPhospi"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script language="Javascript" type="text/javascript">
     
function loadActesNGAP(sejour_id){
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_actes_ngap");
  url.addParam("object_id", sejour_id);
  url.addParam("object_class", "CSejour");
  url.requestUpdate('listActesNGAP', { waitingText: null } );
}

function reloadAfterSaveDoc(sejour_id){
  var url = new Url;
  url.setModuleAction("dPhospi", "httpreq_vw_documents");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate('documents', { waitingText: null } );
}
     
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

function loadDocuments(sejour_id){
  reloadAfterSaveDoc(sejour_id);
}

function loadSejour(sejour_id) {
  url_sejour = new Url;
  url_sejour.setModuleAction("system", "httpreq_vw_complete_object");
  url_sejour.addParam("object_class","CSejour");
  url_sejour.addParam("object_id",sejour_id);
  url_sejour.requestUpdate('viewSejourHospi', {
  waitingText: null,
	onComplete: initPuces
  } );
}

function popEtatSejour(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du Séjour');
}


function loadViewSejour(sejour_id, praticien_id){
  loadSejour(sejour_id); 
  loadDocuments(sejour_id); 
  if($('listActesNGAP')){
    loadActesNGAP(sejour_id);
  }
  if($('ccam')){
    ActesCCAM.refreshList(sejour_id, praticien_id);
  }
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");

  {{if $object->_id}}
    loadViewSejour({{$object->_id}});
  {{/if}}
}

</script>

<table class="main">
  <tr>
    <td style="width:200px;" rowspan="3">
      <table>
        <tr>
          <th>
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        <tr>
          {{include file="inc_mode_hospi.tpl"}}
        </tr>
        <tr>
          <td>
            <form name="selService" action="?m={{$m}}" method="get">
              <label for="service_id">Service</label>
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="service_id" onChange="submit()">
                <option value="">&mdash; Choix d'un service</option>
                {{foreach from=$services item=curr_service}}
                <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service->_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
                {{/foreach}}
                <option value="NP" {{if $service_id == "NP"}} selected="selected" {{/if}}>Non placés</option>
              </select>
            </form>
          </td>
        </tr>
        <tr>
          <td>
            {{if $service->_id}}
            <table class="tbl">
            {{foreach from=$service->_ref_chambres item=curr_chambre}}
            
            {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
            <tr>
              <th class="category" colspan="5">
                {{$curr_chambre->_view}} - {{$curr_lit->_view}}
              </th> 
                {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
              
              {{if $curr_affectation->_ref_sejour->_id != ""}}
              <tr>
              <td>
              <a href="#nothing" onclick="popEtatSejour({{$curr_affectation->_ref_sejour->_id}});">
                <img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
              </a>
              </td>
              <td>
              <a href="#nothing" onclick="loadViewSejour({{$curr_affectation->_ref_sejour->_id}}, {{$curr_affectation->_ref_sejour->praticien_id}});">
                {{$curr_affectation->_ref_sejour->_ref_patient->_view}}
              </a>
              </td>
              <td>
                <a style="float: right;" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_affectation->_ref_sejour->_ref_patient->_id}}">
                  <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
                </a>
                </td>
                <td>
                <a style="float: right;" href="{{$curr_affectation->_ref_sejour->_ref_patient->_dossier_cabinet_url}}&amp;patient_id={{$curr_affectation->_ref_sejour->_ref_patient->_id}}">
                  <img src="images/icons/search.png" alt="view" title="Afficher le dossier complet" />
                </a>                             
              </td>
              <td class="action" style="background:#{{$curr_affectation->_ref_sejour->_ref_praticien->_ref_function->color}}">
              {{$curr_affectation->_ref_sejour->_ref_praticien->_shortview}}
              </td>
            </tr>
            {{/if}}
            {{/foreach}}
            
            {{/foreach}}
            
            {{/foreach}}
            </table>
            {{elseif $service_id == "NP"}}
            {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
            <table class="tbl">
              <tr>
                <th class="title" colspan="5">
                  {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
                </th>
              </tr>
              {{foreach from=$sejourNonAffectes item=curr_sejour}}
              
              {{if $curr_sejour->_id != ""}}
              <tr>
	              <td>
	              <a href="#nothing" onclick="popEtatSejour({{$curr_sejour->_id}});">
	                <img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
	              </a>
	              </td>
	              <td>
	              <a href="#nothing" onclick="loadViewSejour({{$curr_sejour->_id}},{{$curr_sejour->praticien_id}})">
	                {{$curr_sejour->_ref_patient->_view}}
	              </a>
	              </td>
	              <td>
	                <a style="float: right;" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sejour->_ref_patient->_id}}">
	                  <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
	                </a>
	                </td>
	                <td>
	                <a style="float: right;" href="{{$curr_sejour->_ref_patient->_dossier_cabinet_url}}&amp;patient_id={{$curr_sejour->_ref_patient->_id}}">
	                  <img src="images/icons/search.png" alt="view" title="Afficher le dossier complet" />
	                </a>                             
	              </td>
	              <td class="action" style="background:#{{$curr_sejour->_ref_praticien->_ref_function->color}}">
	              {{$curr_sejour->_ref_praticien->_shortview}}
	              </td>
              </tr>
              {{/if}}
              {{/foreach}}
            </table>
            {{/foreach}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
    <td>
    
   <div class="accordionMain" id="accordionConsult">
     
     <div id="AntTrait">
      <div id="AntTraitHeader" class="accordionTabTitleBar">
        Sejour
      </div>
      <div id="AntTraitContent"  class="accordionTabContentBox">
         <div id="viewSejourHospi">
         </div>
      </div>
     </div>
  
   {{if $app->user_prefs.ccam_sejour == 1 }}
   <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Gestion des actes
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">
      <table class="form">
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Actes CCAM</a></li>
              <li><a href="#two">Actes NGAP</a></li>
            </ul>
          </td>
        </tr>
        <tr id="one">
          <td>
            <div id="ccam">
            </div>
          </td>
        </tr>
        <tr id="two">
          <td>
            <div id="listActesNGAP">
            </div>
          </td>
        </tr>
      </table>
      <script type="text/javascript">new Control.Tabs('main_tab_group');</script>
    </div>
  </div>
  {{/if}}
  
   <div id="Docs">
      <div id="DocsHeader" class="accordionTabTitleBar">
        Documents
      </div>
      <div id="DocsContent"  class="accordionTabContentBox">
         <div id="documents">
         </div>
      </div>
     </div>
    </div>
     
    </td>
  </tr>
</table>


<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), { 
  panelHeight: ViewPort.SetAccordHeight('accordionConsult' ,{ iBottomMargin : 10 } ),
  showDelay:50,
  showSteps:3,
  onShowTab: storeVoletAcc,
  onLoadShowTab: showTabAcc
} );


        
</script>  