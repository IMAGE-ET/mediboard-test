{{* $Id: ajax_errors.tpl 7494 2009-12-02 16:34:38Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
var Slideshow = {
  container: null,
  
  next: function(){
    var i, slides = this.container.childElements();
    for (i = 0; i < slides.length; i++) {
      if (slides[i].visible()) {
        slides[i].fade({duration:0.5, afterFinishInternal:function(){
          slides[i].hide();
          slides[i+1].appear({duration:0.5, afterFinishInternal:function(){}});
        }});
        return false;
      }
    }
  },
  
  previous: function(){
    var i, slides = this.container.childElements();
    for (i = slides.length-1; i > 0; --i) {
      if (slides[i].visible()) {
        slides[i].fade({duration:0.5, afterFinishInternal:function(){
          slides[i].hide();
          slides[i-1].appear({duration:0.5, afterFinishInternal:function(){}});
        }});
        return false;
      }
    }
  }
};

Main.add(function(){
  Slideshow.container = $('slideshow');
});
</script>

<div id="slideshow">
  <div class="about">
    <div class="text">
      <h2>Présentation</h2>
      <p><a href="http://www.mediboard.org/" title="Site du projet Mediboard" target="_blank">Mediboard</a>
      est un <strong>système web open source de gestion d'établissements de santé</strong>.
      Il se définit plus précisément comme un <strong>SIH</strong> (Système d'Information Hospitalier)
      c'est-à-dire un PGI (Progiciel de Gestion Integré) adapté aux <strong>établissements de santé de toute taille</strong>,
      du simple cabinet de praticien au centre médical multi-sites.</p>

      <br />
      <h2>Version</h2>
	    {{$version.string}}

    </div>
    
      
    <div class="nav">
      <a class="button right rtl" href="#Team" onclick="return Slideshow.next()">L'équipe</a>
    </div>
  </div>
  
  <div class="about" style="display: none;">
    <div class="text">
      <h2>L'équipe</h2>
      Romain Ollivier : Directeur Produit<br />
      Thomas Despoix : Directeur Technique<br />
      Alexis Granger : Responsable Prescription &amp; Soins<br />
      Fabien Ménager : Responsable Stock &amp; Framework<br />
      Yohann Poiron : Responsable Intéropérabilité<br />
      Flavien Crochard : Responsable Production Documentaire<br />
      <br />
      
      <h2>Sociétés actives</h2>
      <a href="http://www.openxtrem.com" target="_blank">OpenXtrem</a><br />
      <a href="http://www.kheops.ch/" target="_blank">Kheops Technologies</a><br />
      
    </div>
    <div class="nav">
      <a class="button left ltr" href="#Summary" onclick="return Slideshow.previous()">Présentation</a>
      -
      <a class="button right rtl" href="#Thanks" onclick="return Slideshow.next()">Remerciements</a>
    </div>
  </div>
  
  <div class="about" style="display: none;">
    <div class="text">
      <h2>Remerciements</h2>
      Merci à tous les praticiens qui ont pris du temps sur leur emploi du temps déjà chargé
      pour nous aider à mener ce projet à maturation.
    </div>
    <div class="nav">
      <a class="button left ltr" href="#Team" onclick="return Slideshow.previous()">L'équipe</a>
    </div>
  </div>
</div>