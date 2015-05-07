<?php 

/**
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$modele_id = CValue::get("modele_id");

$modele = new CCompteRendu();
$modele->load($modele_id);
$modele->loadContent(true);

$doc = new CMbXMLDocument(null);

$root = $doc->createElement("CCompteRendu");

$doc->appendChild($root);

foreach (CCompteRendu::$fields_import_export as $_field) {
  ${$_field} = $doc->createElement($_field);
  $textnode = $doc->createTextNode(utf8_encode($modele->$_field));
  ${$_field}->appendChild($textnode);
  $root->appendChild(${$_field});
}

// Attribut modele_id
$key = $doc->createAttribute("modele_id");
$value = $doc->createTextNode($modele->_id);
$key->appendChild($value);
$root->appendChild($key);

// Catégorie
$cat = $modele->loadRefCategory();
$key = $doc->createAttribute("cat");
$value = $doc->createTextNode($cat->nom);
$key->appendChild($value);
$root->appendChild($key);

$content = $doc->saveXML();

header('Content-Type: text/xml');
header('Content-Disposition: inline; filename="'.$modele->nom.'.xml"');
header('Content-Length: '.strlen($content).';');

echo $content;
