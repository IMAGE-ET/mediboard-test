{{if @$conf.system_date}}
<script>
  (function(){
    // Ne fonctionne pas sous IE8
    if (document.documentMode == 8) {
      alert("La fonctionnalité 'Date système' ne fonctionne que sur des navigateurs récents, vous utilisez Internet Explorer 8, veuillez le mettre à jour.");
      return;
    }

    var bind = Function.prototype.bind;
    var unbind = bind.bind(bind);

    function instantiate(constructor, args) {
      return new (unbind(constructor, null).apply(null, args));
    }

    window.DateOrig = Date;

    var systemDate = "{{$conf.system_date}}".match(/^(\d{4})-(\d{2})-(\d{2})/);
    DateOrig.systemDate = [
      parseInt(systemDate[1], 10),
      parseInt(systemDate[2], 10),
      parseInt(systemDate[3], 10)
    ];

    window.Date = function () {
      var date = instantiate(DateOrig, arguments);

      if (arguments.length == 0) {
        date.setFullYear(DateOrig.systemDate[0]);
        date.setMonth(DateOrig.systemDate[1] - 1);
        date.setDate(DateOrig.systemDate[2]);
      }

      return date;
    };

    Date.prototype = DateOrig.prototype;
  })();
</script>
{{/if}}