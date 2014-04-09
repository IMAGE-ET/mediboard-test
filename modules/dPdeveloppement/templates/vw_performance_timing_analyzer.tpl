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
    DOM.th({className: "narrow"}, "Date"),
    DOM.th({className: "narrow"}),
    DOM.th({className: "narrow"}, "n"),
    DOM.th({}, "Page"),
    DOM.th({}, "Module"),
    DOM.th({}, "Script")
  ));
  report.pages.each(function(page){
    if (!page.timeline.length) {
      return;
    }

    var navigation = {
      navigate:     ["style/mediboard/images/buttons/link.png", "Navigation"],
      reload:       ["style/mediboard/images/buttons/change.png", "Rafraîchissement"],
      back_forward: ["style/mediboard/images/buttons/back-forward.png", "Retour/avanc."]
    };

    var nav = null;
    var date = new Date();
    date.setTime(page.time);

    var navType = page.timeline[0].pageInfo.navigation;
    if (navType !== undefined) {
      nav = navigation[navType];
    }

    var item = DOM.tr({},
      DOM.td({}, date.toLocaleDateTime()),
      DOM.td({}, nav ? DOM.img({src: nav[0], title: nav[1]}) : ""),
      DOM.td({}, page.timeline.length),
      DOM.td({style: "text-align: right;"},
        DOM.span({title: page.view.m}, $T("module-"+page.view.m+"-court"))
      ),
      DOM.td({},
        DOM.a({href: "#0", title: page.view.a, style: "font-weight: bold;"}, $T("mod-"+page.view.m+"-tab-"+page.view.a)).observe("click", function(event){
          Event.stop(event);
          MbPerformance.showTimingDetails(page.timeline, report.label);
        })
      ),
      DOM.td({className: "compact", title: page.url}, page.url.truncate(90))
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
</form>

<div id="profiling-report"></div>