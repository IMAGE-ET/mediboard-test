<script language="JavaScript" type="text/javascript">

function show(elementId, doIt) {
  element = document.getElementById(elementId);
  element.style.display = doIt ? "" : "none";
}

function write(elementId, text) {
  element = document.getElementById(elementId);
  element.innerHTML = text;
}

is_running = null;

function setRunning(running) {
  is_running = running;
  
  show("running_step", is_running);
  show("start_process", !is_running);
  show("stop_process", is_running);
}

function addCell(tr, content) {
  td = document.createElement("td");
  tr.appendChild(td);
  t = document.createTextNode(content);
  td.appendChild(t);
}

function addHeader(tr, content) {
  th = document.createElement("th");
  tr.appendChild(th);
  t = document.createTextNode(content);
  th.appendChild(t);
}

nb_medecins_total = 0;
time_total = 0.0;
parse_errors_total = 0;
sibling_errors_total = 0;
stores_total = 0;

function endStep(from, to, nb_medecins, time, parse_errors, sibling_errors, stores) {
  nb_medecins_total += nb_medecins;
  time_total += time;
  parse_errors_total += parse_errors;
  sibling_errors_total += sibling_errors;
  stores_total += stores;

  table = document.getElementById("process");
  tbody = table.getElementsByTagName("tbody")[0];
  
  tr = document.createElement("tr"); 
  table.appendChild(tr);

  var sStep = printf("De %d à %d", from, to);
  addCell(tr, sStep);
  addCell(tr, time + ' seconds');
  addCell(tr, nb_medecins);
  addCell(tr, parse_errors);
  addCell(tr, sibling_errors);
  addCell(tr, stores);
  
  if (!nb_medecins) {
    endProcess();
  }
  
  if (is_running) {
    step++;
  	startStep();
  }  
}

var step = 0;

function startStep() {
  setRunning(true);
  url = new Url();
  url.setModuleAction("dPpatients", "import_medecin");
  url.addParam("step", step);
  url.addParam("curl", 0);
  url.popup(600, 600, 'import');
}

function startProcess() {
  startStep();
}

function stopProcess() {
  setRunning(false);
}

function endProcess() {
  setRunning(false);
  show("start_process", false);
  
  table = document.getElementById("process");
  tbody = table.getElementsByTagName("tbody")[0];
  
  tr = document.createElement("tr"); 
  table.appendChild(tr);

  addHeader(tr, "Total");
  addCell(tr, time_total + ' seconds');
  addCell(tr, nb_medecins_total);
  addCell(tr, parse_errors_total);
  addCell(tr, sibling_errors_total);
  addCell(tr, stores_total);
}

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

function pageMain() {
  setRunning(false);
}

</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  <tr>
    <th class="category" colspan="100">Tag pour les IPP</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CPatient"}}
    {{assign var="var" value="tag_ipp"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
    
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
   {{assign var="var" value="date_naissance"}}
  <tr>
   <th class="category" colspan="6">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>    
   </th>
  </tr>
  <tr>  
    <td colspan="6" style="text-align: center">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>


<h2>Import de la base données de l'ordre des médecin</h2>
<button class="tick" id="start_process" onclick="startProcess()">
  Commencer le processus
</button>

<button class="tick" id="stop_process" onclick="stopProcess()">
  Arrêter le processus après l'étape courante
</button>

<table class="tbl" id="process">
  <thead>
    <tr>
      <th>Etape #</th>
      <th>Temps pris</th>
      <th>Nombre de médecins importés</th>
      <th>Erreurs de parsing</th>
      <th>Erreurs de doublons</th>
      <th>Sauvegardes réussies</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
  <tfoot>
    <tr id="running_step">
      <td colspan="10">Etape <span id="step_number"/> en cours...
    <tr>
  </tfoot>
</table>

<h2>Import de la base de données des codes INSEE / ISO</h2>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Status</th>
</tr>
  
<tr>
  <td>
    <button class="tick" onclick="startINSEE()">
      Importer les codes INSEE / ISO
    </button>
  </td>
  <td id="INSEE" />
</tr>

</table>