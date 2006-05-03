<script language="JavaScript" type="text/javascript">
{literal}

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
  url.setModuleAction("dPpatients", "medecin");
  url.addParam("step", step)
  url.popup(400, 100, 'import');
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
  var INSEEUrl = new Url;
  INSEEUrl.setModuleAction("dPpatients", "httpreq_do_add_insee");
  INSEEUrl.requestUpdate("INSEE");
}

function pageMain() {
  setRunning(false);
}

{/literal}
</script>

<h2>Import de la base données de l'ordre des médecin</h2>
<button id="start_process" onclick="startProcess()">
  Commencer le processus
</button>

<button id="stop_process" onclick="stopProcess()">
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

<h2>Import de la base de données des codes INSEE</h2>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Status</th>
</tr>
  
<tr>
  <td>
    <button onclick="startINSEE()">
      Importer les codes INSEE
    </button>
  </td>
  <td id="INSEE" />
</tr>

</table>