<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

 
 // Préférences par Module
CPreferences::$modules["common"] = array (
  "LOCALE",
  "UISTYLE",
  "MenuPosition",
  "DEFMODULE",
  "touchscreen",
  "MobileUI",
  "MobileDefaultModuleView",
  "tooltipAppearenceTimeout",
  "showLastUpdate",
  "directory_to_watch",
  "debug_yoplet",
  "autocompleteDelay",
  "showCounterTip",
  "textareaToolbarPosition",
  "sessionLifetime",
  "planning_resize",
  "planning_dragndrop"
);
  
CPreferences::$modules["system"] = array (
  "INFOSYSTEM",
  "showTemplateSpans",
  "moduleFavicon",
  "notes_anonymous"
);