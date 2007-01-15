{{assign var="module" value="mediusers"}}
{{assign var="script" value="vw_idx_mediusers"}}

<script type="text/javascript">

var Chronometer = Class.create();

Chronometer.prototype = {
  benchmark: null,

  startTime: null,
  stopTime: null,
  duration: null,

  initialize: function(benchmark) {
    this.benchmark = benchmark;
  },

  start: function() {
    this.startTime = new Date;
    this.benchmark.start();
  },
  
  stop: function() {
    this.stopTime = new Date;
    this.duration = this.stopTime - this.startTime;
    this.benchmark.stop(this.duration);
  }
}

var Benchmark = {
  totalDuration : 0,
  requestCount : 0,
  responseCount : 0,
  averageDuration : 0,
  
  startTime: null,
  stopTime: null,
  duration: null,
  
  executer : null,

  start: function() {
  	this.requestCount++;
    $("requestCount").innerHTML = this.requestCount;
  },
  
  stop: function (duration) {
  	this.responseCount++;
    this.totalDuration += duration;
    this.averageDuration = this.totalDuration / this.responseCount;
    $("responseCount").innerHTML = this.responseCount;
    $("lastDuration").innerHTML = duration;
    $("averageDuration").innerHTML = Math.round(this.averageDuration);
  },
  
  send: function() {
    var oChrono = new Chronometer(this);
    
    var oOptions = {
      onLoading: oChrono.start.bind(oChrono),
      onComplete: oChrono.stop.bind(oChrono),
    }
  
    var url = new Url;
    url.setModuleAction("{{$module}}", "{{$script}}");
    url.requestUpdate("response", oOptions);
  },
  
  sendEvery: function(fMilliseconds) {
  	fMilliseconds = parseFloat(fMilliseconds);
  	
    
    if (this.executer) {
      Console.trace("Shutting down executer");
      this.executer.stop();
    }
    
    if (fMilliseconds != 0.0) {
      Console.debug(fMilliseconds, "Create executer with frequency");
      this.executer = new PeriodicalExecuter(this.fake, fMilliseconds);
    }
  },
  
  fake: function() {
    Console.debug((new Date).getTime(), "Fake Sand");
  }
}

</script>

<h2>Analyse de performance du serveur</h2>

<table class="tbl">
  <tr>
	<th>Module</th>
	<th>Script</th>
	<th>Fréquences</th>
	<th>Action</th>
	<th>Requêtes</th>
	<th>Réponses</th>
	<th>Dernière durée</th>
	<th>Durée moyenne</th>
  </tr>
  <tr>
    <td>{{tr}}module-{{$module}}-court{{/tr}}</td>
    <td>{{tr}}script-{{$script}}{{/tr}}</td>
    <td>
      <select name="frequency" onchange="Benchmark.sendEvery(this.value)">
        <option value="0">&mdash Arret</option>
        <option value="3600">1 heure</option>
        <option value="900">15 minutes</option>
        <option value="240">4 minutes</option>
        <option value="60">1 minute</option>
        <option value="15">15 secondes</option>
        <option value="4">4 secondes</option>
        <option value="1">1 seconde</option>
        <option value="0.25">250 millisecondes</option>
        <option value="0.10">100 millisecondes</option>
      </select>
    </td>
    <td>
      <button type="tick" onclick="Benchmark.send()">Send</button>
    </td>
    <td id="requestCount"></td>
    <td id="responseCount"></td>
    <td id="lastDuration"></td>
    <td id="averageDuration"></td>
  </tr>
</table>

<div id="response" style="Display:none"></div>