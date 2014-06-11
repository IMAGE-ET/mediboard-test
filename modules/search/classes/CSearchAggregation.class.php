<?php
/**
* $Id$
*
* @package    Mediboard
* @subpackage search
* @author     SARL OpenXtrem <dev@openxtrem.com>
* @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
* @version    $Revision$
*/

CAppUI::requireLibraryFile("elastica/autoloader", false);
// The autoloader must be triggered, so the second argument must be true
if (!class_exists("CSearch", true)) {
  return;
}
use Elastica\Aggregation;
/**
 * Class CSearchAggregation
 * Manage Elastica Library in order to index documents
 */
class CSearchAggregation {
  /** @var  Elastica\Aggregation\AbstractAggregation */
  public $_aggregation;

  /**
   * Constructor
   *
   * @param string $type  The type of the aggregation
   * @param string $name  The name of the aggregation
   * @param string $field The index field where you want to make the aggregation.
   *
   * @return Elastica\Aggregation\Terms |mixed
   */
  function __construct ($type, $name, $field) {
    switch ($type) {
      case "Terms":
        $this->_aggregation = new Elastica\Aggregation\Terms($name);
        $this->_aggregation->setField($field);
        $this->_aggregation->setOrder("_count", "desc");
        $this->_aggregation->setSize(100);
        break;
      default:
        return null;
    }
    return $this->_aggregation;
  }
}