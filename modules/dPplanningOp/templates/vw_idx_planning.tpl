<!-- $Id$ -->

<script type="text/javascript">

function createDocument(modele_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("modele_id", modele_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function createPack(pack_id, operation_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("pack_id", pack_id);
  url.addParam("object_id", operation_id);
  url.popup(700, 700, "Document");
}

function editDocument(compte_rendu_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
  url.addParam("compte_rendu_id", compte_rendu_id);
  url.popup(700, 700, "Document");
}

function reloadAfterSaveDoc(operation_id){
  var url = new Url;
  url.setModuleAction("dPsalleOp", "httpreq_vw_list_documents");
  url.addParam("operation_id" , operation_id);
  url.requestUpdate('document-'+operation_id);
}

function updateListOperations(date, urgence) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("chirSel" , "{{$selChir}}");
  url.addParam("date"    , date);
  if(!urgence){
    url.addParam("urgences", "{{$urgences}}");
  }else{
    url.addParam("urgences", urgence);
  }

  url.requestUpdate('operations', { waitingText:null } );
}

function pageMain() {
  updateListOperations("{{$date}}");
}

</script>

<table class="main">
  <tr>
    <td style="height: 16px;">
      <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="selChir">Chirurgien</label>
      <select name="selChir" onchange="this.form.submit()">
        <option value="-1">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listChir item=curr_chir}}
        <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $curr_chir->user_id == $selChir}} selected="selected" {{/if}}>
          {{$curr_chir->_view}}
        </option>
        {{/foreach}}
      </select>
      </form>
    </td>
    <td rowspan="3" class="greedyPane" style="vertical-align:top;">
      <div id="operations">
      
      </div>
    </td>
  </tr>
  <tr>
    <th style="height: 16px;">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage</th>
          <th>Opérations</th>
          <th>Temps pris</th>
        </tr>
        {{foreach from=$listPlages item=curr_plage}}
        {{if $curr_plage.spec_id}}
        <tr>
          <td style="background: #aae" align="right">{{$curr_plage.date|date_format:"%a %d %b %Y"}}</td>
          <td style="background: #aae" align="center">{{$curr_plage.debut|date_format:"%Hh%M"}} à {{$curr_plage.fin|date_format:"%Hh%M"}}</td>
          <td style="background: #aae" align="center">{{$curr_plage.total}}</td>
          <td style="background: #aae" align="center">Plage de spécialité</td>
        </tr>
        {{else}}
        <tr>
          <td align="right"><a href="#nothing" onclick="updateListOperations('{{$curr_plage.date|date_format:"%Y-%m-%d"}}', '0')">{{$curr_plage.date|date_format:"%a %d %b %Y"}}</a></td>
          <td align="center">{{$curr_plage.debut|date_format:"%Hh%M"}} à {{$curr_plage.fin|date_format:"%Hh%M"}}</td>
          <td align="center">{{$curr_plage.total}}</td>
          <td align="center">{{$curr_plage.duree|date_format:"%Hh%M"}}</td>
        </tr>
        {{/if}}
        {{/foreach}}
        {{if $listUrgences|@count}}
        <tr>
          <td align="right"><a href="?m={{$m}}&amp;tab=vw_idx_planning&amp;urgences=1">Urgences</a></td>
          <td align="center">-</td>
          <td align="center">{{$listUrgences|@count}}</td>
          <td align="center">-</td>
        </tr>
        {{/if}}
      </table>
      
      
    </td>
  </tr>
</table>