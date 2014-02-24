/**
 *
 * @param {String}  type
 * @param {String}  name
 * @param {Number}  time
 * @param {Number=} duration
 * @param {Object=} data
 * @param {Object=} advanced
 *
 * @constructor
 */
function MbPerformanceTimeEntry(type, name, time, duration, data, advanced){
  this.type = type;
  this.name = name;
  this.time = time;

  this.duration = duration;
  this.data     = data;
  this.advanced = advanced;
}

MbPerformance = {
  version: "0.1",
  timeline: [],
  timers: {},
  intervalTimer: null,
  profiling: false,
  pageDetail: null,
  timingSupport: window.performance && window.performance.timing,
  types: {
    page:   1,
    mark:   2,
    chrono: 3,
    ajax:   4
  },
  responsabilityColors: {
    network: "#0000FF",
    server:  "#00FF00",
    client:  "#FF0000",
    other:   "#999999"
  },
  markingTypes: {
    redirect: {
      color: "rgba(184,125,0,0.2)",
      resp:  "other",
      label: "Redirection",
      desc:  "Temps de la redirection",
      start: "redirectStart",
      end:   "redirectEnd"
    },

    fetch: {
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Cache",
      desc:  "Temps de recherche dans le cache",
      start: "fetchStart",
      end:   "domainLookupStart"
    },

    // network request
    networkRequest: {
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Requête",
      desc:  "Temps d'initalisation de la connexion en envoi de la requête",
      start: "domainLookupStart",
      end:   "requestStart"
    },
    domainLookup: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "DNS",
      desc:  "Résolution du nom de domaine (DNS)",
      start: "domainLookupStart",
      end:   "domainLookupEnd"
    },
    connect: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Connexion",
      desc:  "Initalisation de la connexion",
      start: "connectStart",
      end:   "connectEnd"
    },
    request: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Requête",
      desc:  "Envoi de la requête",
      start: "connectEnd",
      end:   "requestStart"
    },

    // Server
    server: {
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Serveur",
      desc:  "Temps passé sur le serveur",
      start: "requestStart",
      end:   "responseStart"
    },
    frameworkInit: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Fx init",
      desc:  "Initalisation du framework",
      getValue: function(){
        var total = 0;
        MbPerformance.pageDetail.steps.each(function(step){
          if (step.label == "init") {
            total += step.dur;
          }
        });
        return Math.round(total);
      }
    },
    session: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Session",
      desc:  "Ouverture de la session",
      getValue: function(){
        var total = 0;
        MbPerformance.pageDetail.steps.each(function(step){
          if (step.label == "session") {
            total += step.dur;
          }
        });
        return Math.round(total);
      }
    },
    framework: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Framework",
      desc:  "Suite du chargement du framework",
      getValue: function(){
        var total = 0;
        MbPerformance.pageDetail.steps.each(function(step){
          if (["init", "session", "app"].indexOf(step.label) == -1) {
            total += step.dur;
          }
        });
        return Math.round(total);
      }
    },
    app: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "App.",
      desc:  "Code applicatif (dépend de la page affichée) et construction de la page",
      getValue: function(){
        var total = 0;
        MbPerformance.pageDetail.steps.each(function(step){
          if (step.label == "app") {
            total += step.dur;
          }
        });
        return Math.round(total);
      }
    },
    other: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Autre",
      desc:  "Autre temps, passé dans le serveur",
      getValue: function(){
        var serverTime = performance.timing.responseStart - performance.timing.requestStart;
        return serverTime - Math.round(MbPerformance.pageDetail.end - MbPerformance.pageDetail.start);
      }
    },

    // response
    response: {
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Réponse",
      desc:  "Temps de téléchargement de la réponse",
      start: "responseStart",
      end:   "responseEnd"
    },

    // client
    dom: {
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Page",
      desc:  "Temps de lecture de la page",
      start: "domLoading",
      end:   "domComplete"
    },
    domLoading: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Init. DOM",
      desc:  "Temps de construction de l'arbre DOM",
      start: "domLoading",
      end:   "domContentLoadedEventStart"
    },
    domContentLoadedEvent: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Charg. DOM",
      desc:  "Temps de l'évènement d'éxecution des scripts suivant le chargement de l'arbe DOM",
      start: "domContentLoadedEventStart",
      end:   "domComplete"
    },
    loadEvent: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Charg. contenu",
      desc:  "Temps de téléchargement des contenus externes (images, etc)",
      start: "domComplete",
      end:   "loadEventEnd"
    }
  },

  addEvent: function(eventName, callback) {
    if (window.addEventListener) {
      window.addEventListener(eventName, callback, false);
    }
    else {
      window.attachEvent("on"+eventName, callback);
    }
  },

  toggleProfiling: function(){
    var cookie =  new CookieJar();
    var profiling = cookie.get("profiling");

    this.profiling = false;

    if (!MbPerformance.timingSupport) {
      alert("Votre navigateur ne permet pas d'activer le profilage de performances.");
      return;
    }

    if (profiling == 1) {
      cookie.put("profiling", 0);
      clearTimeout(this.intervalTimer);
    }
    else {
      if (confirm("Vous allez activer le mode 'profilage de performances' de Mediboard, ce qui peut ralentir Mediboard, voulez-vous continuer ?")) {
        cookie.put("profiling", 1);
        this.profiling = true;
        MbPerformance.startPlotting();
      }
    }
  },

  init: function(){
    if (!MbPerformance.timingSupport) {
      return;
    }

    // defer, but not with defer() because prototype is not here yet !
    try {
      MbPerformance.addEvent("load", function(){
        setTimeout(function(){
          MbPerformance.startPlotting();
        }, 1);
      });
    }
    catch (e) {}
  },

  startPlotting: function(){
    var cookie =  new CookieJar();
    var profiling = cookie.get("profiling");

    MbPerformance.profiling = profiling == 1;

    if (MbPerformance.profiling) {
      MbPerformance.plot();

      MbPerformance.addEvent("unload", function(){
        var pages = store.get("profiling-pages") || [];

        pages.push(MbPerformance.getCurrentPageData());

        store.set("profiling-pages", pages);
      });
    }
  },

  append: function(entry){
    this.timeline.push(entry);
  },
  log: function(type, name, data, time, duration) {
    var adv = null;

    switch (type) {
      case "page":
        adv = performance.timing;
        break;

      case "ajax":
        adv = MbPerformance.searchEntry(name);

        if (adv) {
          duration = adv.responseEnd - adv.startTime;
        }
    }

    var timeEntry = new MbPerformanceTimeEntry(type, name, time, duration, data, adv);

    MbPerformance.append(timeEntry);
  },

  searchEntry: function(name) {
    var entries = performance.getEntries();
    var id = name.split(/\|/)[2];

    if (entries && entries.length) {
      for (var i = 0, l = entries.length; i < l; i++) {
        var entry = entries[i];
        if (entry.name.indexOf("__uniqueID=|"+id+"|") > -1) {
          return entry;
        }
      }
    }

    return null;
  },

  mark: function(label){
    this.append(new MbPerformanceTimeEntry("mark", label, performance.now()));
  },

  plot: function(){
    var series = [
      {data: [], label: "Page"},
      {data: [], label: "Mark"},
      {data: [], label: "Chrono"},
      {data: [], label: "Ajax"}
    ];

    var timeline = this.timeline;/*.sort(function(a, b){
      return a.time - b.time;
    });*/

    var g = 24;
    var y = 0;

    var chronos = [];

    timeline.each(function(d){
      var s = MbPerformance.types[d.type];
      var ord;

      switch (d.type) {
        case "page":
          ord = 0;
          break;

        case "ajax":
          ord = 3 + (y*2)%g;
          y++;
          break;

        case "chrono":
          ord = 2;
          if (d.data) {
            chronos.push(d);
            //return;
          }
          break;

        default:
          ord = 3;
          break;
      }

      ord += 0.5;

      // Display server data for page and ajax
      if (d.type === "ajax" || d.type === "page") {
        var refTime = null;
        var steps = d.data.steps;

        steps.each(function(serverEvent){
          var serverTime = serverEvent.time;
          if (refTime === null) {
            refTime = serverTime - d.time;
          }

          var time = serverTime - refTime;

          series[1].data.push([
            time,
            ord+0.8,
            time+serverEvent.dur,

            serverEvent.label + "<br />" +
              d.name + "<br />" +
              "Time: "+time + " ms<br />"+
              "Duration: "+serverEvent.dur + " ms"
          ]);
        });
      }

      var name = d.name;
      var text = "";
      var uid = null;
      var parts;

      if (parts = name.match(/(\w+)\|(\w+)((?:@)\d+)?/i)) {
        var m = parts[1];
        var a = parts[2];
        if (parts[3]) {
          uid = parts[3].match(/@(\d+)/)[1];
        }

        text = "m = "+m+"<br />a = "+a+"<br />";
        text += $T("mod-"+m+"-tab-"+a)+"<br />";
      }
      else {
        text = name + "<br />";

        if (d.data) {
          text += d.data+"<br />";
        }
      }

      text += "Time: "+d.time + " ms<br />"+
              "Duration: "+d.duration+" ms";

      series[s-1].data.push([
        d.time,
        ord,
        d.time + d.duration,
        text,
        uid
      ]);
    });

    /*chronos.each(function(chrono){
      console.log(chrono);
    });*/

    var container = jQuery("#profiling-plot");

    if (!container.size()) {
      container = jQuery('<div id="profiling-plot"><div id="profiling-graph"></div></div>').hide().appendTo("body");

      var profilingToolbar = jQuery('<div id="profiling-toolbar"></div>').hide().appendTo("body");

      // Toggle plot
      jQuery('<button class="stats notext" title="Afficher le graphique"></button>').click(function(){
        MbPerformance.plot();
        container.toggle();
      }).appendTo(profilingToolbar);

      // Download report
      jQuery('<button class="download notext" title="Télécharger le rapport"></button>').click(function(){
        MbPerformance.download();
      }).appendTo(profilingToolbar);

      // Remove report
      jQuery('<button class="trash notext" title="Supprimer le rapport courant"></button>').click(function(){
        MbPerformance.removeProfiling();
      }).appendTo(profilingToolbar);

      // Show toolbar
      jQuery('<button id="profiler-toggle" class="gantt notext" title="Profilage de performances en cours"></button>').click(function(){
        profilingToolbar.toggle();
      }).appendTo("body");

      // Show toolbar
      jQuery('<div id="profiler-overview" class="not-printable"><div id="profiler-timebar"></div></div>').appendTo("body");
    }

    var graph = jQuery("#profiling-graph");

    if (!graph._plothoverbound) {
      var previousPoint = null;
      graph.bind("plothover", function (event, pos, item){
        if(item){
          if (previousPoint != item.datapoint){
            previousPoint = item.datapoint;
            jQuery("#profiling-tooltip").remove();

            var point = item.series.data[item.dataIndex];

            MbPerformance.showTooltip(pos.pageX, pos.pageY, point[3]);
          }
        }
        else {
          jQuery("#profiling-tooltip").remove();
          previousPoint = null;
        }
      });

      graph._plothoverbound = true;
    }

    var markings = [];
    var overview = jQuery("#profiler-overview");

    var timeBar = [];
    var timeBarTotal = 0;
    var timeBarContainer = jQuery("#profiler-timebar");

    $H(MbPerformance.markingTypes).each(function(pair){
      this.addMarking(pair.key, markings);
    }, this);

    // Don't redraw markings bar
    if (!MbPerformance.markingsDrawn) {
      markings.each(function(marking){
        var resp = MbPerformance.responsabilityColors[marking.resp];
        var line = jQuery('<div title="'+marking.desc+'" class="marking '+(marking.sub ? 'sub' : '')+'" style="background: '+marking.color+'; border-color: '+resp+';">'+(marking.sub ? '&nbsp;- ' : '')+marking.label+"<span style='float:right;'>"+marking.value+" ms</span></div>");
        overview.append(line);

        if (!marking.sub) {
          timeBarTotal += marking.value;
          timeBar.push({
            type: marking.resp,
            value: marking.value
          });
        }
      });

      timeBar.each(function(time){
        timeBarContainer.append("<div style='width: "+(100 * (time.value / timeBarTotal))+"%; background-color: "+MbPerformance.responsabilityColors[time.type]+";'></div>");
      });
    }

    MbPerformance.markingsDrawn = true;

    jQuery.plot(graph, series, {
      series: {
        gantt: {
          active: true,
          show: true,
          barHeight: 1.0
        }
      },
      xaxis: {
        min: 0
      },
      grid: {
        hoverable: true,
        clickable: true,
        markings: markings
      },
      legend: {
        show: false
      }
    });
  },

  addMarking: function(key, markings){
    var timing = performance.timing;

    if (!timing) {
      return;
    }

    var ref = timing.navigationStart;
    var type = MbPerformance.markingTypes[key];

    if (timing[type.start] == 0 && !type.getValue) {
      return;
    }

    var marking = {
      label: type.label,
      desc:  type.desc,
      color: type.color,
      resp:  type.resp,
      sub:   type.sub,
      key:   key,
      lineWidth: 1
    };

    if (type.getValue) {
      marking.value = type.getValue();
      marking.xaxis = {
        from: 0,
        to:   0
      };
    }
    else {
      marking.xaxis = {
        from: timing[type.start] - ref,
        to:   timing[type.end]   - ref
      };
      marking.value = timing[type.end] - timing[type.start];
    }

    markings.push(marking);
  },

  showTooltip: function(x, y, contents){
    jQuery('<div id="profiling-tooltip">' + contents + '</div>').css({
      display: 'none',
      top: y + 5,
      left: x + 5
    }).appendTo("body").fadeIn(200);
  },

  timeStart: function(label) {
    if (!this.profiling) {
      return;
    }

    this.timers[label] = performance.now();
  },

  timeEnd: function(label, data) {
    if (!this.profiling) {
      return;
    }

    if (!this.timers[label]) {
      return;
    }

    var time = this.timers[label];
    delete this.timers[label];

    var entry = new MbPerformanceTimeEntry("chrono", label, time, performance.now() - time, data);

    this.append(entry);
  },

  timeIt: function(label, callback, data) {
    MbPerformance.timeStart(label);
    callback();
    MbPerformance.timeEnd(label, data);
  },

  dump: function(){
    var label = prompt("Libellé du profilage", "Profilage du "+(new Date()).toLocaleDateTime());

    if (label == null) {
      return;
    }

    var struct = {
      version: MbPerformance.version,
      date: (new Date()).toDATETIME(),
      label: label,
      userAgent: navigator.userAgent,
      platform: navigator.platform,
      screen: window.screen,
      plugins: [],
      pages: []
    };

    if (navigator.plugins) {
      $A(navigator.plugins).each(function(plugin){
        struct.plugins.push({
          name: plugin.name,
          filename: plugin.filename,
          description: plugin.description
        });
      });
    }

    struct.pages = store.get("profiling-pages") || [];
    struct.pages.push(MbPerformance.getCurrentPageData());

    return struct;
  },

  removeProfiling: function(){
    if (confirm("Supprimer le rapport de profilage courant ? Pensez à le télécharger auparavant si vous souhaitez le garder.")) {
      store.remove("profiling-pages");
    }
  },

  getCurrentPageData: function(){
    return {
      timeline: MbPerformance.timeline,
      url: document.location.href,
      time: Date.now(),
      view: {
        m: App.m,
        a: App.tab || App.a
      }
    };
  },

  download: function() {
    var data = MbPerformance.dump();

    if (data == null) {
      return;
    }

    var form = DOM.form({
      target: "_blank",
      action: "?m=system&a=download_data",
      method: "post"
    }, DOM.input({
      type: "hidden",
      name: "m",
      value: "system"
    }), DOM.input({
      type: "hidden",
      name: "a",
      value: "download_data"
    }), DOM.input({
      type: "hidden",
      name: "filename",
      value: data.label+".json"
    }), DOM.input({
      type: "hidden",
      name: "data",
      value: Object.toJSON(data)
    }));

    $$("body")[0].insert(form);
    form.submit();
  }
};

MbPerformance.init();
