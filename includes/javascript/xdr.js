/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage pyxVital
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

/**
 * Simple HttpClient who act like a factory for XMLHttpRequest and XDomainRequest
 */
XDR = Class.create({
  url:     null,
  options: null,
  method:  null,
  xhr:     null,

  /**
   * Initialize the HttpClient.
   *
   * @param {String} url     The url
   * @param {String} method  The HTTP method to use
   * @param {Object} options Various options
   */
  initialize: function(url, method, options) {
    this.url = url;
    this.method = method;

    options = Object.extend({
      headers: {},
      timeout: 5000,
      onprogress: function(){},
      onerror:    function(){},
      onload:     function(){},
      ontimeout:  function(){}
    }, options);

    this.options = options;

    var xhr;

    // XMLHttpRequest
    if (window.XMLHttpRequest){
      xhr = new XMLHttpRequest();
      xhr.open(this.method, this.url, true);

      // We set the custom headers
      $H(options.headers).each(function(pair){
        xhr.setRequestHeader(pair.key, pair.value);
      });

      xhr.onreadystatechange = function() {
        if (xhr.readyState == xhr.DONE) {
          if (xhr.status == 200) {
            options.onload.bind(xhr)();
          }
          else {
            options.onerror.bind(xhr)();
          }
        }
      };
    }

    // IE 8/9
    else {
      xhr = new XDomainRequest();
      xhr.open(this.method, this.url);

      xhr.onerror = options.onerror.bind(xhr);
      xhr.onload  = options.onload.bind(xhr);
    }

    // Event handlers
    xhr.onprogress = options.onprogress.bind(xhr);
    xhr.ontimeout  = options.ontimeout.bind(xhr);

    xhr.timeout = options.timeout;

    this.xhr = xhr;
  },

  /**
   * Send the POST request
   *
   * @param {string} payload The POST's payload
   */
  send: function(payload) {
    this.xhr.send(payload);
  }
});