<?php /* $Id:$ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision: $
 *  @author Romain OLLIVIER
*/

?>

<script type="text/javascript">

function switchDiv(div1, div2) {
  divFade(div1);
  divAppear(div2);
}

function divAppear(div) {
  Effect.Appear(div, Effect.Appear, {speed:0.05, afterFinishInternal:function(){}});
  return false;
}

function divFade(div) {
  Effect.Fade(div, Effect.Fade, {speed:0.05, afterFinishInternal:function(){}});
  return false;
}

function pageMain() {
}

</script>

<div class="about" id="div1">
  <div class="text">
    <h2>Présentation</h2>
    <a href="http://www.mediboard.org/" title="Site du projet Mediboard" target="_blank">Mediboard</a>
    est un <strong>système web open source de gestion d'établissement de santé</strong>.
    Il se définit plus précisément comme un <strong>SIH</strong> (Système d'Information Hospitalier)
    c'est-à-dire un PGI (Progiciel de Gestion Integré) adapté aux <strong>établissements de santé de toute taille</strong>,
    du simple cabinet de praticien au centre médical multi-sites.
  </div>
  <div class="nav">
    -
    <a href="#" onclick="switchDiv('div1', 'div2')">L'equipe &gt;&gt;</a>
  </div>
</div>

<div class="about" id="div2" style="display: none;">
  <div class="text">
    <h2>L'equipe</h2>
    <ul>
      <li>Romain Ollivier</li>
      <li>Thomas Despoix</li>
      <li>Sebastien Fillonneau</li>
      <li>Alexis Granger</li>
    </ul>
    <h2>Sociétés actives</h2>
    <ul>
      <li>openXtrem</li>
      <li>Kheops Technologies</li>
    </ul>
  </div>
  <div class="nav">
    <a href="#" onclick="switchDiv('div2', 'div1')">&lt;&lt; Présentation</a>
    -
    <a href="#" onclick="switchDiv('div2', 'div3')">Remerciements &gt;&gt;</a>
  </div>
</div>



<div class="about" id="div3" style="display: none;">
  <div class="text">
    <h2>Remerciements</h2>
    Merci à tous les praticiens qui ont pris du temps sur leurs emplois du temps déjà chargé
    pour nous aider à ammener ce projet à maturation.
  </div>
  <div class="nav">
    <a href="#" onclick="switchDiv('div3', 'div2')">&lt;&lt; L'équipe</a>
    -
  </div>
</div>
