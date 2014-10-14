<script>
  SuiviSallesPresentation = {
    page: 1,
    update: function() {
      var url = new Url("dPbloc", "ajax_vw_suivi_salle");
      url.addParam('bloc_id', '{{$bloc_id}}');
      url.addParam('date', '{{$date}}');
      url.addParam('page', SuiviSallesPresentation.page++);
      url.requestUpdate("result_suivi");
    },

    startAutoRefresh: function(){
      SuiviSallesPresentation.update();

      SuiviSallesPresentation.timer = setInterval(function(){
        SuiviSallesPresentation.update();
      }, {{"dPbloc mode_presentation refresh_period"|conf:"CGroups-$g"}}*1000);
    }
  };

  Main.add(function() {
    SuiviSallesPresentation.startAutoRefresh();
  });
</script>

<button onclick="App.fullscreen();" style="position: absolute; right: 0">Plein écran</button>
<div id="result_suivi"></div>


