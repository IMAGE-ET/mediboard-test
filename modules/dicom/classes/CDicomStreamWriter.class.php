<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * A stream writer who can write several types from a binary stream, in Big Endian or Little Endian syntax
 */
class CDicomStreamWriter {
  
  /**
   * The stream (usually a stream to the socket connexion)
   * 
   * @var resource
   */
  var $stream = null;
  
  /**
   * The content of the stream, used to keep a trace of the DICOM exchanges
   * 
   * @var binary data
   */
  var $buf = null;
  
  /**
   * The constructor of CDicomStreamwriteer
   * 
   * @param resource $stream The stream
   */
  function __construct($stream) {
    $this->stream = $stream;
    $this->buf = "";
  }
  
  /**
   * Write a number of bytes equal to 0
   * 
   * @param int $bytes The number of bytes you want to skip. This number can't be negative
   * 
   * @return null
   */
  function skip($bytes) {
    if ($bytes > 0) {
      $bin = "";
      for ($i = 0; $i < $bytes; $i++) {
        $bin .= pack("H*", "00");
      }
      $this->buf .= $bin;
      return fwrite($this->stream, $bin, $bytes);
    }
  }
  
  /**
   * Return the current position of the stream pointer.
   * 
   * @return int The current position of the stream pointer
   */
  function getPos() {
    return ftell($this->stream);
  }
  
  
  /**
   * Write hexadecimal numbers from the stream
   *  
   * @param hexadecimal $hexa       The hexadecimal string
   * 
   * @param int         $length     The length of the number, equal to 1 if not given
   * 
   * @param string      $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer or false on error
   */
  function writeHexByte($hexa, $length = 1, $endianness = "BE") {
    if ($endianness == "BE") {
      return $this->writeHexByteBE($hexa, $length);
    }
    elseif ($endianness == "LE") {
      return $this->writeHexByteLE($hexa, $length);
    }
  }
  
  /**
   * Write hexadecimal numbers from the stream. Use Big Endian syntax
   * 
   * @param hexadecimal $hexa   The hexadecimal string
   * 
   * @param int         $length The length of the number, equal to 1 if not given
   * 
   * @return integer or false on error
   */
  function writeHexByteBE($hexa, $length = 1) {
    $bin = pack("H*", $hexa);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, $length);
  }
  
  /**
   * Write hexadecimal numbers from the stream. Use Little Endian syntax
   * 
   * @param hexadecimal $hexa   The hexadecimal string
   * 
   * @param int         $length The length of the number, equal to 1 if not given
   * 
   * @return integer or false on error
   */
  function writeHexByteLE($hexa, $length = 1) {
    $bin = pack("h*", $hexa);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, $length);
  }
  
  /**
   * Write unsigned 32 bits numbers.
   * 
   * @param integer $int        The unsigned integer
   * 
   * @param string  $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt32($int, $endianness = "BE") {
    if ($endianness == "BE") {
      return $this->writeUnsignedInt32BE($int);
    }
    elseif ($endianness == "LE") {
      return $this->writeUnsignedInt32LE($int);
    }
  }
  
  /**
   * Write unsigned 32 bits numbers, in Big Endian syntax.
   * 
   * @param integer $int The unsigned integer
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt32BE($int) {
    $bin = pack("N", $int);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, 4);
  }
  
  /**
   * Write unsigned 32 bits numbers, in Little Endian syntax.
   * 
   * @param integer $int The unsigned integer
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt32LE($int) {
    $bin = pack("V", $int);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, 4);
  }
  
  /**
   * Write unsigned 16 bits numbers.
   * 
   * @param integer $int        The unsigned integer
   * 
   * @param string  $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt16($int, $endianness = "BE") {
    if ($endianness == "BE") {
      return $this->writeUnsignedInt16BE($int);
    }
    elseif ($endianness == "LE") {
      return $this->writeUnsignedInt16LE($int);
    }
  }
  
  /**
   * Write unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @param integer $int The unsigned integer
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt16BE($int) {
    $bin = pack("n", $int);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, 2);
  }
  
  /**
   * Write unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @param integer $int The unsigned integer
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt16LE($int) {
    $bin = pack("v", $int);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, 2);
  }
  
  /**
   * Write unsigned 8 bits numbers.
   * 
   * @param integer $int The unsigned integer
   * 
   * @return integer or false on error
   */
  function writeUnsignedInt8($int) {
    $bin = pack("C", $int);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, 1);
  }
  
  /**
   * Write a string
   * 
   * @param string $str    The string
   * 
   * @param int    $length The length of the string
   * 
   * @return integer or false on error
   */
  function writeString($str, $length) {
    $bin = pack("A*", $str);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, $length);
  }
  
  /**
   * Write a Dicom UID (series of integer, separated by ".")
   * 
   * @param string $uid    The UID
   * 
   * @param int    $length The length of the UID, equal to 64 if not given
   * 
   * @return integer or false on error
   */
  function writeUID($uid, $length = 64) {
    $bin = pack("A*", $uid);
    $this->buf .= $bin;
    return fwrite($this->stream, $bin, $length);
  }
} 
?>