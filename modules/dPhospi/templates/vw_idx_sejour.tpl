{{assign var="do_subject_aed" value="do_sejour_aed"}}
{{assign var="module" value="dPhospi"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script language="Javascript" type="text/javascript">

   
   
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



function loadSejour(sejour_id) {
  url_sejour = new Url;
  url_sejour.setModuleAction("system", "httpreq_vw_complete_object");
  url_sejour.addParam("object_class","CSejour");
  url_sejour.addParam("object_id",sejour_id);
  url_sejour.requestUpdate('sejour', {
	onComplete: initPuces
  } );
}

function popEtatSejour(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du Séjour');
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");

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
          <td>
            <form name="selService" action="?m={{$m}}" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="service_id" onChange="submit()">
                <option value="">&mdash; Choix d'un service &mdash;</option>
                {{foreach from=$services item=curr_service}}
	            <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $service->_id}} selected="selected" {{/if}}>{{$curr_service->nom}}</option>
              {{/foreach}}
              </select>
            </form>
          </td>
        </tr>
        <tr>
          <td>
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
              <a href="#nothing" onclick="loadSejour({{$curr_affectation->_ref_sejour->_id}}); loadActes('{{$curr_affectation->_ref_sejour->_id}}', '{{$curr_affectation->_ref_sejour->praticien_id}}');">
                {{$curr_affectation->_ref_sejour->_ref_patient->_view}}
              </a>
              </td>
              <td>
                <a style="float: right;" href="index.php?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_affectation->_ref_sejour->_ref_patient->_id}}">
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
         <div id="sejour">
         </div>
      </div>
     </div>
  
    <div id="Actes">
     <div id="ActesHeader" class="accordionTabTitleBar">
         Actes CCAM
     </div>
     <div id="ActesContent"  class="accordionTabContentBox">
       <table class="tbl">
         <tr>
           <th>Actes<br /><br />
             Séjour
           </th>
           <td>
             <div id="ccam"></div>
           </td>
         </tr>
       </table>
     </div>
    </div>
  
  </div>


    </td>
  </tr>
</table>


<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), { 
  panelHeight: ViewPort.SetAccordHeight('accordionConsult'),
  showDelay:50,
  showSteps:3,
  onShowTab: storeVoletAcc,
  onLoadShowTab: showTabAcc
} );

</script>  