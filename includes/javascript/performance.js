/**
 *
 * @param {String} type
 * @param {Object} pageInfo
 * @param {Number} time
 * @param {Number} duration
 * @param {PerformanceResourceTiming,PerformanceTiming} perfTiming
 * @param {Object} serverTiming
 *
 * @constructor
 */
function MbPerformanceTimeEntry(type, pageInfo, time, duration, perfTiming, serverTiming){
  this.type = type;
  this.pageInfo = pageInfo;

  this.time = time;
  this.duration = duration;

  this.perfTiming = perfTiming;
  this.serverTiming = serverTiming;
}

MbPerformance = {
  version: "0.2",

  /** {MbPerformanceTimeEntry[]} timeline */
  timeline: [],
  timers: {},
  intervalTimer: null,
  profiling: false,
  pageDetail: null,
  timingSupport: window.performance && window.performance.timing,
  timeScale: 5,
  timeOffset: null,
  types: {
    page:   1,
    ajax:   2,
    mark:   3,
    chrono: 4
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
      getValue: function(serverTiming, perfTiming){
        var total = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "init") {
            total += step.dur;
          }
        });
        return total;
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "init") {
            start = step.time;
          }
        });
        return start;
      }
    },
    session: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Session",
      desc:  "Ouverture de la session",
      getValue: function(serverTiming, perfTiming){
        var total = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "session") {
            total += step.dur;
          }
        });
        return Math.round(total);
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "session") {
            start = step.time;
          }
        });
        return start;
      }
    },
    framework: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Framework",
      desc:  "Suite du chargement du framework",
      getValue: function(serverTiming, perfTiming){
        var total = 0;
        serverTiming.steps.each(function(step){
          if (["init", "session", "app"].indexOf(step.label) == -1) {
            total += step.dur;
          }
        });
        return total;
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "session") {
            start = step.time + step.dur;
          }
        });
        return start;
      }
    },
    app: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "App.",
      desc:  "Code applicatif (dépend de la page affichée) et construction de la page",
      getValue: function(serverTiming, perfTiming){
        var total = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "app") {
            total += step.dur;
          }
        });
        return total;
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "app") {
            start = step.time;
          }
        });
        return start;
      }
    },
    other: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Autre",
      desc:  "Autre temps, passé dans le serveur",
      getValue: function(serverTiming, perfTiming){
        if (!perfTiming.responseStart || !perfTiming.requestStart) {
          return null;
        }

        var serverTime = perfTiming.responseStart - perfTiming.requestStart;
        return serverTime - (serverTiming.end - serverTiming.start);
      },
      getStart: function(serverTiming, perfTiming){
        if (!perfTiming.responseStart || !perfTiming.requestStart) {
          return null;
        }

        return perfTiming.responseStart - (perfTiming.responseStart - perfTiming.requestStart) + (serverTiming.end - serverTiming.start);
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
    domInit: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Init. DOM",
      desc:  "Temps d'init arbre DOM",
      start: "responseEnd",
      end:   "domLoading"
    },
    domLoading: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Constr. DOM",
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

  /**
   * Don't use cookiejar as it may not be ready yet
   *
   * @returns {String,null}
   */
  readCookie: function() {
    var value = /mediboard-profiling=([^;]+)/.exec(document.cookie);
    if (!value) {
      return null;
    }

    return decodeURI(value[1]);
  },

  init: function(){
    if (!MbPerformance.timingSupport) {
      return;
    }

    // defer, but not with defer() because prototype is not here yet !
    try {
      MbPerformance.profiling = MbPerformance.readCookie() == '"1"';
      MbPerformance.addEvent("load", function(){
        setTimeout(function(){
          MbPerformance.startPlotting();
        }, 1);
      });
    }
    catch (e) {}
  },

  startPlotting: function(){
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

  logScriptEvent: function(type, pageInfo, serverTiming, time, duration) {
    var perfTiming;

    switch (type) {
      case "page":
        perfTiming = performance.timing;
        break;

      case "ajax":
        perfTiming = MbPerformance.searchEntry(pageInfo.id);

        if (perfTiming) {
          duration = perfTiming.responseEnd - perfTiming.startTime;
        }
        break;
    }

    var timeEntry = new MbPerformanceTimeEntry(type, pageInfo, time, duration, perfTiming, serverTiming);

    MbPerformance.append(timeEntry);
  },
  log: function(type, name, data, time, duration) {
    /*var entryData;

    switch (type) {
      case "page":
        entryData = performance.timing;
        break;

      case "ajax":
        entryData = MbPerformance.searchEntry(name);

        if (entryData) {
          duration = entryData.responseEnd - entryData.startTime;
        }

      default: // find previous entry
    }

    if (entryData)

    var timeEntry = new MbPerformanceTimeEntry(type, name, time, duration, data, adv);

    MbPerformance.append(timeEntry);*/
  },

  searchEntry: function(id) {
    var entries = performance.getEntries();

    if (entries && entries.length) {
      for (var i = 0, l = entries.length; i < l; i++) {
        var entry = entries[i];
        if (entry.initiatorType === "xmlhttprequest" && entry.name.indexOf("__uniqueID=|"+id+"|") > -1) {
          return entry;
        }
      }
    }

    return null;
  },

  mark: function(label){
    //this.append(new MbPerformanceTimeEntry("mark", label, performance.now()));
  },

  /**
   *
   * @param {HTMLSelectElement} select
   * @param {String=}           way
   */
  zoom: function (select, way) {
    var index = select.selectedIndex;
    switch (way) {
      case "+":
        if (index == 0) {
          return;
        }

        select.selectedIndex--;
        break;

      case "-":
        if (index == select.options.length-1) {
          return;
        }

        select.selectedIndex++;
        break;
    }

    MbPerformance.timeScale = select.value;
    MbPerformance.buildTimingDetails();
  },

  buildTimingDetails: function(){
    var left, right, table;

    if (MbPerformance.timingDetailsTable) {
      table = MbPerformance.timingDetailsTable;
      left  = table.down(".left-col").update("");
      right = table.down(".right-col").update("");
    }
    else {
      var zoom = DOM.div({className: "zoom"},
        "<button class='zoom-out notext' onclick='MbPerformance.zoom(this.next(), \"-\")'></button>",
        "<select style='display: none;' onchange='MbPerformance.zoom(this)'>" +
          "<option>0.125</option>" +
          "<option>0.25</option>" +
          "<option>0.5</option>" +
          "<option>1</option>" +
          "<option>2</option>" +
          "<option selected>5</option>" +
          "<option>10</option>" +
          "<option>20</option>" +
          "<option>50</option>" +
          "<option>100</option>" +
          "<option>200</option>" +
          "<option>400</option>" +
        "</select>",
        "<button class='zoom-in notext' onclick='MbPerformance.zoom(this.previous(), \"+\")'></button>"
      );
      table = DOM.table({className: "main layout timeline"},
        DOM.tr({},
          left  = DOM.td({className: "left-col"}),
          DOM.td({className: "right-col-cell"},
            right = DOM.div({className: "right-col"})
          )
        ),
        DOM.tr({},
          DOM.td({}, zoom),
          DOM.td({})
        )
      );
    }

    var addBar = function(type, container, perfTiming, perfOffset, serverTiming, serverOffset){
      var t = MbPerformance.markingTypes[type];

      if (perfTiming && (perfTiming[t.end] && perfTiming[t.start] || t.getValue && t.getStart)) {
        var start, length;

        if (t.getValue) {
          start  = t.getStart(serverTiming, perfTiming) - serverOffset;
          length = t.getValue(serverTiming, perfTiming);
        }
        else {
          start  = perfTiming[t.start] - perfOffset;
          length = perfTiming[t.end]   - perfTiming[t.start];
        }

        var title = t.label+"\n"+t.desc+"\nDébut: "+Math.round(start)+"ms\nDurée: "+Math.round(length)+"ms";
        var bar = DOM.div({
          title: title,
          className: "bar bar-"+t.resp+" bar-type-"+type+(t.sub ? " sub" : "")
        });

        bar.setStyle({
          left:  (start  / MbPerformance.timeScale)+"px",
          width: (length / MbPerformance.timeScale)+"px"
        });

        container.insert(bar);
      }
    };

    var ruler = DOM.div({className: "ruler"});

    for (var i = 0; i < 100; i++) {
      var ms = i*1000;
      var tick = DOM.span({}, ms);
      tick.style.left = (ms / MbPerformance.timeScale)+"px";
      ruler.insert(tick);
    }

    left.insert(DOM.div({className: "ruler"}));
    right.insert(ruler);

    MbPerformance.timeline.each(function(d){
      var title = DOM.div({title: d.pageInfo.a},
          $T("module-"+d.pageInfo.m+"-court")+"<br />"+
          $T("mod-"+d.pageInfo.m+"-tab-"+d.pageInfo.a));

      var container = DOM.div({});
      var perfTiming = d.perfTiming;
      var perfOffset;
      var serverTiming = d.serverTiming;
      var serverOffset;

      if (d.type == "page") {
        perfOffset = perfTiming.navigationStart;
        serverOffset = perfTiming.navigationStart;

        if (MbPerformance.timeOffset === null) {
          MbPerformance.timeOffset = perfTiming.navigationStart - serverTiming.start;
        }
      }
      else {
        perfOffset = 0;
        serverOffset = performance.timing.navigationStart;
      }

      serverOffset -= MbPerformance.timeOffset;

      Object.keys(MbPerformance.markingTypes).each(function(type) {
        addBar(type, container, perfTiming, perfOffset, serverTiming, serverOffset);
      });

      left.insert(title);
      right.insert(container);
    });

    if (!MbPerformance.timingDetailsTable) {
      document.body.insert(table);
      MbPerformance.timingDetailsTable = table;
    }
  },

  showTimingDetails: function() {
    MbPerformance.buildTimingDetails();

    Modal.open(MbPerformance.timingDetailsTable, {
      showClose: true,
      width: -10,
      height: -10
    });
  },

  plot: function(){
    var series = [
      {data: [], label: "Page"},
      {data: [], label: "Ajax"},
      {data: [], label: "Mark"},
      {data: [], label: "Chrono"}
    ];

    var timeline = this.timeline;

    var g = 24;
    var y = 0;
    var xmax = 0;

    var chronos = [];

    timeline.each(function(d){
      var s = MbPerformance.types[d.type]-1;
      var ord = 0;

      xmax = Math.max(xmax, d.time+d.duration);

      series[s].data.push([
        d.time,
        (ord*s)+0.8,
        d.time+d.duration,

        $T("module-"+d.pageInfo.m+"-court")+"<br />"+
        $T("mod-"+d.pageInfo.m+"-"+d.pageInfo.a)+"<br />"+
          "Time: "+d.time+" ms<br />"+
          "Duration: "+d.duration+ " ms"
      ]);
    });

    /*[].each(function(d){
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

          xmax = Math.max(xmax, time+serverEvent.dur);

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

      xmax = Math.max(xmax, d.time + d.duration);

      series[s-1].data.push([
        d.time,
        ord,
        d.time + d.duration,
        text,
        uid
      ]);
    });*/

    var container = jQuery("#profiling-plot");

    if (!container.size()) {
      container = jQuery('<div id="profiling-plot"><div id="profiling-graph"></div></div>').hide().appendTo("body");

      var profilingToolbar = jQuery('<div id="profiling-toolbar"></div>').hide().appendTo("body");

      // Toggle plot
      jQuery('<button class="stats notext" title="Afficher le graphique"></button>').click(function(){
        MbPerformance.showTimingDetails();
        //MbPerformance.plot();
        //container.toggle();
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
      jQuery('<div id="profiler-overview"><div class="profiler-buttons"><!--<button class="lookup notext"></button>--></div><div id="profiler-timebar"></div></div>').appendTo("body");
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
        var line = jQuery('<div title="'+marking.desc+'" class="marking '+(marking.sub ? 'sub' : '')+'" style="background: '+marking.color+'; border-color: '+resp+';">'+(marking.sub ? '&nbsp;- ' : '')+marking.label+"<span style='float:right;'>"+Math.round(marking.value)+" ms</span></div>");
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

      //overview.find("button.lookup").click(MbPerformance.showTimingDetails);
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
        min: 0,
        max: xmax
      },
      yaxis: {
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
      marking.value = type.getValue(MbPerformance.pageDetail, timing);
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

  timeEnd: function(label, ajaxId) {
    if (!this.profiling) {
      return;
    }

    if (!this.timers[label]) {
      return;
    }

    var now = performance.now();
    var time = this.timers[label];
    var timeEntry;

    delete this.timers[label];

    (function(timeEntry, ajaxId){
      if (!ajaxId) {
        timeEntry = MbPerformance.timeline[0];
      }
      else {
        timeEntry = MbPerformance.timeline.find(function(t){
          return t.pageInfo.id == ajaxId;
        });
      }

      if (timeEntry) {
        timeEntry.perfTiming = timeEntry.perfTiming || {};

        if (label == "eval") {
          timeEntry.perfTiming.domLoading = time;
          timeEntry.perfTiming.domContentLoadedEventStart = time;
          timeEntry.perfTiming.domContentLoadedEventEnd = now;
          timeEntry.perfTiming.domComplete = now;
        }
      }
    }).delay(2, timeEntry, ajaxId);
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

if (window.performance && performance.setResourceTimingBufferSize) {
  performance.onresourcetimingbufferfull = function(){
    console.error("Resource timing buffer full");
  }

  performance.setResourceTimingBufferSize(500);
  performance.clearResourceTimings();
}

MbPerformance.init();
