/**
 * Micro JS framework
 */

/**
 * @param {NodeList,Array} list
 * @returns {Array}
 */
$A = function(list) {
  return Array.prototype.slice.call(list);
};

/**
 * @param {String} selector
 * @returns {Array}
 */
$$ = function(selector) {
  return $A(document.querySelectorAll(selector));
};

/**
 *
 * @param {HTMLElement} element
 */
hideElement = function(element) {
  element.style.display = "none";
};

/**
 *
 * @param {HTMLElement} element
 */
showElement = function(element) {
  element.style.display = "";
};

/**
 * @param {HTMLElement} element
 * @param {Boolean}     visible
 */
setVisibleElement = function(element, visible) {
  visible ? showElement(element) : hideElement(element);
};

/**
 * @param {NodeList,Array} list
 * @param {Function}       callback
 */
each = function(list, callback) {
  list = $A(list);
  for (var i = 0, l = list.length; i < l; i++) {
    callback(list[i]);
  }
  return list;
};