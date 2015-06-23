<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Abastraction layer for access logs and data source logs
 * @todo Most content is yet to be abstract
 */
class CModuleActionLog extends CStoredObject {
  /**
   * Among plain fields get non summable log signature fields
   *
   * @return array()
   */
  static function getSignatureFields() {
    return array();
  }

  /**
   * Fast store for multiple access logs using ON DUPLICATE KEY UPDATE MySQL feature
   *
   * @param self[] $logs Logs to be stored
   *
   * @return string Store-like message
   */
  static function fastMultiStore($logs) {
    /** @var self $self */
    $self = new static;
    $fields = $self->getPlainFields();
    unset($fields[$self->_spec->key]);

    // Columns and updates
    $columns = array();
    $updates = array();
    $signature_fields = static::getSignatureFields();
    foreach ($fields as $_name => $_value) {
      $columns[] = "$_name";
      if (!in_array($_name, $signature_fields)) {
        $updates[] = "$_name = $_name + VALUES($_name)";
      }
    }

    // Values
    $values = array();
    foreach ($logs as $_log) {
      $row = array();
      foreach (array_keys($fields) as $_name) {
        $value = $_log->$_name;
        $row[] = "'$value'";
      }
      $row = implode(", ", $row);
      $row = "($row)";
      $values[] = $row;
    }

    $columns = implode(", ", $columns);
    $updates = implode(", ", $updates);
    $values  = implode(",\n", $values);

    $table = $self->_spec->table;
    $query = "INSERT INTO $table ($columns)
      VALUES \n$values
      ON DUPLICATE KEY UPDATE $updates";

    $ds = $self->_spec->ds;
    if (!$ds->exec($query)) {
      return $ds->error();
    }

    return null;
  }

  /**
   * Assemble logs based on logical key fields
   *
   * @param self[] $logs Raw access log collection
   *
   * @return self[] $logs Assembled access log colletion
   */
  static function assembleLogs($logs) {
    $signature_fields = static::getSignatureFields();

    $assembled_logs = array();
    foreach ($logs as $_log) {
      // Signature values
      $signature_values = array();
      foreach ($signature_fields as $_field) {
        $signature_values[] = $_log->$_field;
      }

      // Make signature
      $signature = implode(",", $signature_values);

      // First log for this signature
      if (!isset($assembled_logs[$signature])) {
        $assembled_logs[$signature] = $_log;
        continue;
      }

      // Assembling (summing) other log for the same signature
      $log = $assembled_logs[$signature];

      foreach ($_log->getPlainFields() as $_name => $_value) {
        if (!in_array($_name, $signature_fields)) {
          $log->$_name += $_value;
        }
      }
    }

    return $assembled_logs;
  }

  /**
   * Put logs in buffer and store them.
   * Use direct storage if buffer_life time config is 0
   *
   * @param self[] $logs Log collection to put in buffer
   *
   * @return void
   */
  static function bufferize($logs) {
    $class = get_called_class();

    // No buffer use standard unique fast store
    $buffer_lifetime = CAppUI::conf("access_log_buffer_lifetime");
    if (!$buffer_lifetime) {
      if ($msg = static::fastMultiStore($logs)) {
        mbLog("Could not store logs: $msg", $class);
        trigger_error($msg, E_USER_WARNING);
      }
      return;
    }

    // Buffer logs into file
    $buffer = CAppUI::getTmpPath("$class.buffer");
    foreach ($logs as $_log) {
      file_put_contents($buffer, serialize($_log) . PHP_EOL, FILE_APPEND);
    }

    // Unless lifetime is reached by random, don't unbuffer logs
    if (rand(1, $buffer_lifetime) !== 1) {
      return;
    }

    // Move to temporary buffer to prevent concurrent unbuffering
    $tmpbuffer = tempnam(dirname($buffer), basename($buffer). ".");
    if (!rename($buffer, $tmpbuffer)) {
      // Keep the log for a while, should not be frequent with buffer lifetime 100+
      mbLog("Probable concurrent logs unbuffering", $class);
      return;
    }

    // Read lines from temporary buffer
    $lines = file($tmpbuffer);
    $buffered_logs = array();
    foreach ($lines as $_line) {
      $buffered_logs[] = unserialize($_line);
    }

    $assembled_logs = static::assembleLogs($buffered_logs);
    if ($msg = static::fastMultiStore($assembled_logs)) {
      trigger_error($msg, E_USER_WARNING);
      return;
    }

    // Remove the useless temporary buffer
    unlink($tmpbuffer);
    $buffered_count = count($buffered_logs);
    $assembled_count = count($assembled_logs);
    mbLog("'$buffered_count' logs buffered, '$assembled_count' logs assembled", $class);
  }

}
