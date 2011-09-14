<script type="text/javascript">

var Process = {
  running: false,
  step: null,
	pass: {{$pass|json}},
	
  total: {
  	medecins: 0,
  	time: 0.0,
  	updates: 0.0,
  	errors: 0
  },
  		
	doStep: function() {
		var form = document.import;
		
	  this.step =  $V(form.step);
	  		
	  var url = new Url("dPpatients", "import_medecin");
	  url.addElement(form.step);	  
	  url.addParam("mode", $V(form.mode));
	  url.addParam("pass", this.pass);
    url.addParam("departement", $V(form.departement));
    url.addParam("mode_import", $V(form.mode_import));
	  url.requestUpdate("process");
	},
	
	updateScrewed: function(medecins, time, updates, errors) {
		var tr = document.createElement("tr");
	  td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = "XPAth Screwed, try again"; tr.appendChild(td);
	  $("results").appendChild(tr);
	},
	
	updateTotal: function(medecins, time, updates, errors) {
	  this.total.medecins += medecins;
	  this.total.time     += time;
	  this.total.updates  += updates;
	  this.total.errors   += errors;	  

	  var tr = document.createElement("tr");
	  td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = medecins ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = time.toFixed(2)     ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = updates  ; tr.appendChild(td);
	  td = document.createElement("td"); td.textContent = errors   ; tr.appendChild(td);
	  
	  var node = { tr: { td : [this.step, medecins, time, updates, errors] } };

	  $("results").appendChild(tr);
	  
	  $("total-medecins").innerHTML = this.total.medecins;
	  $("total-time"    ).innerHTML = this.total.time.toFixed(2);
	  $("total-updates" ).innerHTML = this.total.updates;
	  $("total-errors"  ).innerHTML = this.total.errors;	  
	},
	
	endStep: function() {
		var form = document.import
		var step = form.step;
	  $V(step, parseInt($V(step))+1);
	  if ($V(form.auto)) {
	  	Process.doStep.bind(Process).defer();
	  }
	}
}

</script>

{{assign var=class value=CMedecin}}
<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{mb_include module=system template=inc_config_bool var=medecin_strict}}

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>

<h2>Import de la base données de médecins</h2>

<table class="tbl">
	<tr>
	  <th colspan="3" style="width: 50%">{{tr}}Action{{/tr}}</th>
	  <th colspan="2" style="width: 50%">{{tr}}Status{{/tr}}</th>
	</tr>
	  
	<tr>
	  <td colspan="3">
	    <form name="import" action="#" onsubmit="return false">
	    
      <label>
	      <input type="radio" name="mode" value="get" />Import distant
      </label>
      <label>
	      <input type="radio" name="mode" value="xml" />Fichiers XML
      </label>
      <label>
	      <input type="radio" name="mode" value="csv" checked="checked" />Fichiers CSV
      </label>
	    
	    <input type="checkbox" name="auto" />
	    <label for="auto">Automatique</label>
      &mdash;
      Département :
      <select name="departement">
        {{foreach from=$departements item=_departement}}
          <option value="{{$_departement}}">{{$_departement}}</option>
        {{/foreach}}
      </select>
      
      Mode d'import :
      <select name="mode_import">
        <option value="comp">Import complet</option>
        <option value="rpps">Mise à jour RPPS</option>
      </select>
	    <br/>

	    <label for="step">Etape</label>
	    <input type="text" name="step" value="1" size="2" />

			<button class="tick" id="start-process" onclick="Process.doStep()">
			  Traiter étape
			</button>
			
			</form>
	  </td>
	  <td id="process" colspan="3">
	  </td>
	</tr>

	<tbody id="results" style="text-align: right">
	  <tr>
	    <th>Etape #</th>
	    <th>Nombre de médecins</th>
	    <th>Temps pris</th>
	    <th>Mises à jour</th>
	    <th>Erreurs</th>
	  </tr>
	</tbody>

  <tr id="total" style="text-align: right">
    <th>Total</th>
    <td id="total-medecins" />
    <td id="total-time" />
    <td id="total-updates" />
    <td id="total-errors" />
  </tr>
</table>