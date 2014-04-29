<!--[if lte IE 7]>
<div class="deprecated-browser-warning">
  <img src="images/icons/error.png" />
  Votre navigateur web est trop ancien, Mediboard ne peut pas fonctionner correctement. 
  Veuillez mettre à jour votre navigateur à la version 11 ou
  <a href="http://mediboard.org/public/Firefox" target="_blank"><strong>cliquez ici</strong></a> 
  pour installer Firefox et profiter d'une meilleure expérience.
</div>

<script>
window.onerror = function(){};
</script>
<![endif]-->

{{if @$browser.ie8}}
  <div class="deprecated-browser-warning">
    <img src="images/icons/error.png" />
    Vous utilisez le navigateur Internet Explorer 8, qui ne sera plus pris en charge par Mediboard à partir de la version de juillet 2014.
    <a href="#1" onclick="window.open('modules/system/public/ie8_deprecated_info.html', 'ie8information', 'width=800,height=600,toolbar=no,menubar=no,scrollbars=no,location=no')" style="font-weight: bold;">En savoir plus</a>
  </div>
{{/if}}
