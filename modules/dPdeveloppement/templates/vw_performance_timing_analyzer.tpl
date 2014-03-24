<script>
analyze = function(form){
  var input = form.elements.export;
  var file = input.files[0];

  if (file.name.indexOf(".json") == -1) {
    alert("Veuillez choisir un fichier JSON");
    return false;
  }

  var reader = new FileReader();
  reader.readAsText(file);

  reader.onloadend = function(e){
    var blob = e.target.result;
    var report = blob.evalJSON();
    loadReport(report);
  };

  return false;
};

loadReport = function(report) {
  var container = $('profiling-report');

  container.update();
  container.insert(DOM.h2({},
    DOM.small({style: "float: right;"},
      "v. "+report.version,
      " - ",
      Date.fromDATETIME(report.date).toLocaleDateTime(),
      " - ",
      report.platform,
      " - ",
      "<em>"+report.userAgent+"</em>"
    ),
    report.label
  ));

  var table = DOM.table({
    className: "main tbl"
  });
  table.insert(DOM.tr({},
    DOM.th({className: "narrow"}, "Navigation"),
    DOM.th({className: "narrow"}, "Date"),
    DOM.th({}, "Page"),
    DOM.th({}, "Module"),
    DOM.th({}, "Script")
  ));
  report.pages.each(function(page){
    if (!page.timeline.length) {
      return;
    }

    var navigation = {
      navigate:     "Navigation",
      reload:       "Rafraîchissement",
      back_forward: "Retour/avanc."
    };

    var date = new Date();
    date.setTime(page.time);

    var item = DOM.tr({},
      DOM.td({}, date.toLocaleDateTime()),
      DOM.td({}, navigation[page.timeline[0].pageInfo.navigation]),
      DOM.td({style: "text-align: right;"}, DOM.strong({title: page.view.m}, $T("module-"+page.view.m+"-court"))),
      DOM.td({}, DOM.strong({title: page.view.a}, $T("mod-"+page.view.m+"-tab-"+page.view.a))),
      DOM.td({},
        DOM.a({href: "#0"},
          page.url
        ).observe("click", function(event){
          Event.stop(event);
          MbPerformance.showTimingDetails(page.timeline);
        })
      )
    );

    table.insert(item);
  });

  container.insert(table);
};

Main.add(function(){
  var report = MbPerformance.dump();
  report.label = "Session actuelle";
  loadReport(report);
})
</script>

<form name="profiling" method="get" onsubmit="return analyze(this)">
  <input type="file" accept="application/json,text/json" name="export" onchange="this.form.onsubmit()" style="width: 30em;" />
  <button class="gantt notext"></button>
</form>

<div id="profiling-report"></div>