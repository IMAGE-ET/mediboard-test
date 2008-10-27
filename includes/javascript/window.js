/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

window.getInnerDimensions = function() {
  var viewH = document.documentElement.clientHeight;
  var viewW = document.documentElement.clientWidth;
  return {x:viewW,y:viewH}
}

Element.getOffsetHeightByClassName = function(class_name){
  var fHeightElem = 0;
  var aElementList = document.getElementsByClassName(class_name);
  for( var i = 0 ; i < aElementList.length ; i++ ){
	  fHeightElem += aElementList[i].offsetHeight;
  }
  return fHeightElem;
}

Element.getInnerWidth = function(element){
  element = $(element);
  var aBorderLeft = element.getStyle("border-left-width").split("px");
  var aBorderRight = element.getStyle("border-right-width").split("px");
  var fwidthElem = element.offsetWidth - parseFloat(aBorderLeft[0]) - parseFloat(aBorderRight[0])
  return fwidthElem; 
}

Element.getInnerHeight = function(element){
  element = $(element);
  var aBorderTop = element.getStyle("border-top-width").split("px");
  var aBorderBottom = element.getStyle("border-bottom-width").split("px");
  var fheightElem = element.offsetHeight - parseFloat(aBorderTop[0]) - parseFloat(aBorderBottom[0])
  return fheightElem; 
}
