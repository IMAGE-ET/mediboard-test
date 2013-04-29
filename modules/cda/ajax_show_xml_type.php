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

echo CMbString::purifyHTML("<h1>$name</h1>");

$cdatools = new CCdaTools();

echo CMbString::highlightCode("xml", $cdatools->showNodeXSD($name, "modules/cda/resources/datatypes-base_original.xsd"));