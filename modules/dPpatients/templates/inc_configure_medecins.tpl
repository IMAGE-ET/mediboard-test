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
	  $("retry-process")[this.running ? "show" : "hide"]();
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

	retry: function() {
		this.step--;
		this.start();	
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
			
			<button class="change" id="retry-process" onclick="Process.retry()">
			  Recommencer l'étape
			</button>
			<br />

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