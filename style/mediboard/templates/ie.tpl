<!--[if lte IE 6]>
<div style="background-color: #ffc; padding: 0.5em; border-bottom: 1px solid #333; font-size: 1.2em;">
  <img src="images/icons/warning.png" />Votre navigateur web est trop ancien, Mediboard ne peut pas fonctionner correctement, 
  <a href="http://mediboard.org/public/Firefox" target="_blank">cliquez ici</a> 
  pour installer Firefox et profiter d'une meilleure expérience.
</div>
<![endif]-->

<!--[if IE 7]>
<style type="text/css">
hr {
  height: 0px;
}

hr.control_tabs {
  margin-top: -7px;
  margin-bottom: -7px;
}
</style>

<script type="text/javascript">
function processIE7Button(button) {
  if (button._processed || (window.Main && !Main.initialized)) return;
  button.runtimeStyle.behavior = "none";
  button._processed = true;
  
  setTimeout(function(){
    var className = $w(button.className).without("button", "notext", "ltr")[0];
  
    if (className)
      button.insertAdjacentHTML("afterBegin", '<img src="./style/mediboard/images/buttons/'+className+'.png" width="16" />');
  }, 0.1);
}

var cookiejar = new CookieJar();
Main.add(function () {
  if (cookiejar.get('IE7WarningClosed') != 'closed') {
    $('ie7warning').show();
  }
});
</script>

<div id="ie7warning" style="background-color: #ffc; border-bottom: 1px solid #333; padding: 0.3em; height: 1.4em; display: none;">
  <a href="#1" style="float: right;" onclick="$('ie7warning').hide(); cookiejar.put('IE7WarningClosed', 'closed');">{{tr}}Close{{/tr}}</a>
  <img src="images/icons/warning.png" style="float: left;"/>
  <span style="margin: 0.2em;">
  	Pour un meilleur confort d'utilisation, nous vous conseillons d'utiliser le navigateur Firefox. 
  	<a href="http://mediboard.org/public/Firefox" target="_blank" style="font-weight: bold; text-decoration: underline;">Cliquez ici</a> 
  	pour plus d'informations.
  </span>
</div>
<![endif]-->
