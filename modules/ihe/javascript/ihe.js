/**
 * $Id$
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

IHE = {
  addFunction : function (element) {
    var value = $V(element);
    if (!value) {
      return false;
    }

    var form = getForm("editiheConfig");
    var tokenfield = new TokenField(form.elements["ihe[function_ids]"]);

    if (tokenfield.contains(value)) {
      return false;
    }

    tokenfield.add(value);
    var text = element.options[element.options.selectedIndex].text;
    var color = element.options[element.options.selectedIndex].get("color");

    IHE.createTag(text, value, color);
    $V(element, "");
  },

  delFunction : function (element, id) {
    var form = getForm("editiheConfig");
    var tokenfield = new TokenField(form.elements["ihe[function_ids]"]);
    tokenfield.remove(id);
    Element.remove(element.up());
  },

  createTag : function (name, id, color) {
    var list = $("listFunctions");
    list.appendChild(DOM.li({className:"tag", style:"background-color: #"+color}, name,
      DOM.button({type:'button', className:"delete", onclick:'IHE.delFunction(this, '+id+')'})
    ));
  }
};