<?php 

/**
 * $Id$
 *
 *  Affiche le code xml du datatype choisi
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$name = CValue::get("name");

echo "<h1>$name</h1>";

$dom = new CMbXMLDocument();
$dom->load("modules/cda/resources/datatypes-base.xsd");

$xpath = new CMbXPath($dom);
$xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
$node = $xpath->queryUniqueNode("//xs:*[@name='".$name."']");

echo CMbString::highlightCode("xml", $dom->saveXML($node));