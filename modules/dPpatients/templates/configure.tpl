<script type="text/javascript">

function startINSEE() {
  var url = new Url;
  url.setModuleAction("dPpatients", "httpreq_do_add_insee");
  url.requestUpdate("INSEE");
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
		this.step = 0;
	},
	
	doStep: function() {
	  if (!this.running) {
	    return;
	  }
	  		
	  url = new Url();
	  url.setModuleAction("dPpatients", "import_medecin");
	  url.addParam("step", ++this.step);
	  url.addParam("curl", this.curl);
	  url.requestUpdate("process");
	},
	
	updateTotal: function(medecins, time, updates, errors) {
	  var tr = document.createElement("tr");
	  td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = medecins ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = time     ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = updates  ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = errors   ; tr.appendChild(td);
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

<h2>Import de la base données de l'ordre des médecin</h2>


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

$