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

$name = CValue::get("name");

echo CMbString::purifyHTML("<h1>$name</h1>");

echo CMbString::highlightCode("xml", CCdaTools::showNodeXSD($name, "modules/cda/resources/datatypes-base_original.xsd"));