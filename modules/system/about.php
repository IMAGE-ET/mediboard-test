<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

?>

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
      <a href="http://www.mediboard.org/" title="Site du projet Mediboard" target="_blank">Mediboard</a>
      est un <strong>système web open source de gestion d'établissements de santé</strong>.
      Il se définit plus précisément comme un <strong>SIH</strong> (Système d'Information Hospitalier)
      c'est-à-dire un PGI (Progiciel de Gestion Integré) adapté aux <strong>établissements de santé de toute taille</strong>,
      du simple cabinet de praticien au centre médical multi-sites.
    </div>
    <div class="nav">
      -
      <a href="#1" onclick="return Slideshow.next()">L'equipe &gt;&gt;</a>
    </div>
  </div>
  
  <div class="about" style="display: none;">
    <div class="text">
      <h2>L'equipe</h2>
      <ul>
        <li>Romain Ollivier</li>
        <li>Thomas Despoix</li>
        <li>Alexis Granger</li>
        <li>Fabien Ménager</li>
      </ul>
      <h2>Sociétés actives</h2>
      <ul>
        <li>OpenXtrem</li>
        <li>Kheops Technologies</li>
      </ul>
    </div>
    <div class="nav">
      <a href="#1" onclick="return Slideshow.previous()">&lt;&lt; Présentation</a>
      -
      <a href="#1" onclick="return Slideshow.next()">Remerciements &gt;&gt;</a>
    </div>
  </div>
  
  <div class="about" style="display: none;">
    <div class="text">
      <h2>Remerciements</h2>
      Merci à tous les praticiens qui ont pris du temps sur leur emploi du temps déjà chargé
      pour nous aider à mener ce projet à maturation.
    </div>
    <div class="nav">
      <a href="#" onclick="return Slideshow.previous()">&lt;&lt; L'équipe</a>
      -
    </div>
  </div>
</div>