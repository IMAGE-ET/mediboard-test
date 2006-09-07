<script type="text/javascript">

var count = 0;
var enter = new Date();
var exit  = new Date();

function pageOK() {
  exit = new Date();
  var dif  = (exit.getTime()-enter.getTime())/1000;
  dif  = Math.round(dif);

  count++;

  debug("Iteration "+count+", en "+dif+" secondes");
}

var periodicalTimeUpdater = new PeriodicalExecuter(updateDiv1, 1);
periodicalTimeUpdater.currentlyExecuting = true;

function updateDiv1() {
  var updater1 = new Url;
  updater1.setModuleAction("dPhospi", "vw_affectations");
  updater1.requestUpdate('div1', { onComplete: pageOK });
}

function launchIt() {
  count = 0;
  enter = new Date();
  periodicalTimeUpdater.currentlyExecuting = false;
}

function stopIt() {
  periodicalTimeUpdater.currentlyExecuting = true;
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="commandDiv1">
      <button type="button" onclick="launchIt()">Go</button>
      <button type="button" onclick="stopIt()">Stop</button>
      </form>
      <div id="div1">
      </div>
    </td>
    <td class="halfPane">
      <div id="div2">
      </div>
    </td>
  </tr>
</table>