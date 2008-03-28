<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

function addAnt(ant) {
  oTypesAnt = document.editConfig["dPpatients[CAntecedent][types]"];
  if(oTypesAnt.value) {
    oTypesAnt.value = oTypesAnt.value + "|" + ant;
  } else {
    oTypesAnt.value = ant;
  }
}

</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{assign var="class" value="CPatient"}}
  <tr>
    <th class="category" colspan="100">Tag pour les IPP</th>
  </tr>
  
  <tr>
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
  
  <!-- Merge only for admin -->
  {{assign var="var" value="merge_only_admin"}}
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
  
  {{assign var="class" value="CAntecedent"}}
  <tr>
    <th class="category" colspan="100">Tag pour les IPP</th>
  </tr>
  
  <tr>
    {{assign var="var" value="types"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <input class="str" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="80" />
    </td>
  </tr>
  <tr>
    <th>Valeurs possibles</th>
    <td class="text" colspan="3">
      <span onclick="addAnt('med')">{{tr}}CAntecedent.type.med{{/tr}}</span>,
      <span onclick="addAnt('alle')">{{tr}}CAntecedent.type.alle{{/tr}}</span>,
      <span onclick="addAnt('trans')">{{tr}}CAntecedent.type.trans{{/tr}}</span>,
      <span onclick="addAnt('obst')">{{tr}}CAntecedent.type.obst{{/tr}}</span>,
      <span onclick="addAnt('chir')">{{tr}}CAntecedent.type.chir{{/tr}}</span>,
      <span onclick="addAnt('fam')">{{tr}}CAntecedent.type.fam{{/tr}}</span>,
      <span onclick="addAnt('anesth')">{{tr}}CAntecedent.type.anesth{{/tr}}</span>,
      <span onclick="addAnt('gyn')">{{tr}}CAntecedent.type.gyn{{/tr}}</span>,
      <span onclick="addAnt('cardio')">{{tr}}CAntecedent.type.cardio{{/tr}}</span>,
      <span onclick="addAnt('pulm')">{{tr}}CAntecedent.type.pulm{{/tr}}</span>,
      <span onclick="addAnt('stomato')">{{tr}}CAntecedent.type.stomato{{/tr}}</span>,
      <span onclick="addAnt('plast')">{{tr}}CAntecedent.type.plast{{/tr}}</span>,
      <span onclick="addAnt('ophtalmo')">{{tr}}CAntecedent.type.ophtalmo{{/tr}}</span>,
      <span onclick="addAnt('digestif')">{{tr}}CAntecedent.type.digestif{{/tr}}</span>,
      <span onclick="addAnt('gastro')">{{tr}}CAntecedent.type.gastro{{/tr}}</span>,
      <span onclick="addAnt('stomie')">{{tr}}CAntecedent.type.stomie{{/tr}}</span>,
      <span onclick="addAnt('uro')">{{tr}}CAntecedent.type.uro{{/tr}}</span>,
      <span onclick="addAnt('ortho')">{{tr}}CAntecedent.type.ortho{{/tr}}</span>,
      <span onclick="addAnt('traumato')">{{tr}}CAntecedent.type.traumato{{/tr}}</span>,
      <span onclick="addAnt('amput')">{{tr}}CAntecedent.type.amput{{/tr}}</span>,
      <span onclick="addAnt('neurochir')">{{tr}}CAntecedent.type.neurochir{{/tr}}</span>,
      <span onclick="addAnt('greffe')">{{tr}}CAntecedent.type.greffe{{/tr}}</span>,
      <span onclick="addAnt('thrombo')">{{tr}}CAntecedent.type.thrombo{{/tr}}</span>,
      <span onclick="addAnt('cutane')">{{tr}}CAntecedent.type.cutane{{/tr}}</span>,
      <span onclick="addAnt('hemato')">{{tr}}CAntecedent.type.hemato{{/tr}}</span>,
      <span onclick="addAnt('rhumato')">{{tr}}CAntecedent.type.rhumato{{/tr}}</span>,
      <span onclick="addAnt('neuropsy')">{{tr}}CAntecedent.type.neuropsy{{/tr}}</span>,
      <span onclick="addAnt('infect')">{{tr}}CAntecedent.type.infect{{/tr}}</span>,
      <span onclick="addAnt('endocrino')">{{tr}}CAntecedent.type.endocrino{{/tr}}</span>,
      <span onclick="addAnt('carcino')">{{tr}}CAntecedent.type.carcino{{/tr}}</span>,
      <span onclick="addAnt('orl')">{{tr}}CAntecedent.type.orl{{/tr}}</span>
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>
</form>

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

<script type="text/javascript">

var Process = {
  running: false,
  curl: 0,
	step: 0,
	pass: {{$pass|json}},

	setRunning: function(value) {
	  this.running = value;
	  $("start-process")[this.running ? "hide" : "show"]();
	  $("stop-process" )[this.running ? "show" : "hide"]();
	},
	
  total: {
  	medecins: 0,
  	time: 0.0,
  	updates: 0.0,
  	errors: 0
  },
  
	start: function() {
		this.setRunning(true);	
		this.doStep();
	},
	
	stop: function() {
		this.setRunning(false);
	},
	
	doStep: function() {
	  if (!this.running) {
	    this.step = 0;
	    return;
	  }
	  		
	  url = new Url();
	  url.setModuleAction("dPpatients", "import_medecin");
	  url.addParam("step", ++this.step);
	  url.addParam("curl", this.curl);
	  url.addParam("pass", this.pass);
	  url.requestUpdate("process");
	},
	
	updateScrewed: function(medecins, time, updates, errors) {
		var tr = document.createElement("tr");
	  td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = "XPAth Screwed, try again"; tr.appendChild(td);
	  $("results").appendChild(tr);
	},
	
	updateTotal: function(medecins, time, updates, errors) {
	  var tr = document.createElement("tr");
	  td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = medecins ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = time     ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = updates  ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = errors   ; tr.appendChild(td);
	  
	  var node = { tr: { td : [this.step, medecins, time, updates, errors] } };

		
	  $("results").appendChild(tr);
	  
	  $("total-medecins").innerHTML = this.total.medecins += medecins;
	  $("total-time"    ).innerHTML = this.total.time     += time;
	  $("total-updates" ).innerHTML = this.total.updates  += updates;
	  $("total-errors"  ).innerHTML = this.total.errors   += errors;	  
	}
}

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
}

</script>

<h2>Import de la base données de médecins</h2>

<table class="tbl">
	<tr>
	  <th colspan="3" style="width: 50%">Action</th>
	  <th colspan="2" style="width: 50%">Status</th>
	</tr>
	  
	<tr>
	  <td colspan="3">
	    <input type="checkbox" name="curl" id="curl" onchange="Process.curl = this.checked ? 1 : 0" />
	    <label for="curl">Import distant</label>
	    <br/>
			<button class="tick" id="start-process" onclick="Process.start()">
			  Commencer le processus
			</button>
			
			<button class="cancel" style="display:none" id="stop-process" onclick="Process.stop()">
			  Arrêter le processus après l'étape courante
			</button>
	  </td>
	  <td id="process" colspan="3">
	  </td>
	</tr>

	<tbody id="results">
	  <tr>
	    <th>Etape #</th>
	    <th>Nombre de médecins</th>
	    <th>Temps pris</th>
	    <th>Mises à jour</th>
	    <th>Erreurs</th>
	  </tr>
	</tbody>

  <tr id="total">
    <th>Total</th>
    <td id="total-medecins" />
    <td id="total-time" />
    <td id="total-updates" />
    <td id="total-errors" />
  </tr>
</table>