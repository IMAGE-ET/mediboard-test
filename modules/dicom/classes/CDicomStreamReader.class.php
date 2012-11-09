<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * A stream reader who can read several types from a binary stream, in Big Endian or Little Endian syntax
 */
class CDicomStreamReader {
  
  /**
   * The stream (usually a stream to the socket connexion)
   * 
   * @var resource
   */
  var $stream = null;
  
  /**
   * The content of the stream, used to keep a trace of the DICOM exchanges
   * 
   * @var string
   */
  var $buf = null;
  
  /**
   * The stream length
   * 
   * @var integer
   */
  protected $stream_length = null;
  
  /**
   * The constructor of CDicomStreamReader
   * 
   * @param resource $stream The stream
   */
  function __construct($stream) {
    $this->stream = $stream;
    $this->buf = "";
  }
  
  /**
   * Return the stream length
   * 
   * @return integer
   */
  function getStreamLength() {
    return $this->stream_length;
  }
  
  /**
   * Set the stream length
   * 
   * @param integer $length The stream length
   * 
   * @return void
   */
  function setStreamLength($length) {
    $this->stream_length = $length;
  }
  
  /**
   * Move forward the stream pointer
   * 
   * @param int $bytes The number of bytes you want to skip. This number can't be negative
   * 
   * @return void
   */
  function skip($bytes) {
    if ($bytes > 0) {
      $this->read($bytes);
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
   * Move the stream pointer
   * 
   * @param int $pos the position
   * 
   * @return void
   */
  function seek($pos) {
    fseek($this->stream, $pos, SEEK_CUR);
  }
  
  /**
   * Rewind the position of the stream pointer
   * 
   * @return void
   */
  function rewind() {
    rewind($this->stream);
  }
  
  /**
   * Read data from the stream, and check if the length of the PDU is passed
   * 
   * @param integer $length The number of byte to read
   * 
   * @return string
   */
  function read($length = 1) {
    $tmp = fread($this->stream, $length);
    $this->buf .= $tmp;
    return $tmp;
  }
  
  /**
   * Close the stream
   * 
   * @return void
   */
  function close() {
    fclose($this->stream);
  }
  
  /**
   * Read hexadecimal numbers from the stream
   * 
   * @param int    $length     The length of the number, equal to 1 if not given
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return int
   */
  function readHexByte($length = 1, $endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readHexByteBE($length);
    }
    elseif ($endianness == "LE") {
      return $this->readHexByteLE($length);
    }
  }
  
  /**
   * Read hexadecimal numbers from the stream. Use Big Endian syntax
   * 
   * @param int $length The length of the number, equal to 1 if not given
   * 
   * @return int
   */
  function readHexByteBE($length = 1) {
    $tmp = unpack("H*", $this->read($length));
    return $tmp[1];
  }
  
  /**
   * Read hexadecimal numbers from the stream. Use Little Endian syntax
   * 
   * @param int $length The length of the number, equal to 1 if not given
   * 
   * @return int
   */
  function readHexByteLE($length = 1) {
    $tmp = unpack("H*", $this->read($length));
    return str_pad(strrev(dechex($tmp[1])), $length*2, 0, STR_PAD_LEFT);
  }
  
  /**
   * Read unsigned 32 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readUInt32($endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readUInt32BE();
    }
    elseif ($endianness == "LE") {
      return $this->readUInt32LE();
    }
  }
  
  /**
   * Read unsigned 32 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUInt32BE() {
    $tmp = unpack("N", $this->read(4));
    return $tmp[1];
  }
  
  /**
   * Read unsigned 32 bits numbers, in Little Endian syntax.
   * 
   * @return integer
   */
  function readUInt32LE() {
    $tmp = unpack("V", $this->read(4));
    return $tmp[1];
  }
  
  /**
   * Read 32 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readInt32($endianness = "BE") {
    $int = 0;

    if ($endianness == "BE") {
      $int = $this->readUInt32BE();
    }
    elseif ($endianness == "LE") {
      $int = $this->readUInt32LE();
    }
    
    if ($int >= 0x80000000) {
      $int -= 0x100000000;
    }

    return $int;
  }
  
  /**
   * Read unsigned 16 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readUInt16($endianness = "BE") {
    if ($endianness == "BE") {
      return $this->readUInt16BE();
    }
    elseif ($endianness == "LE") {
      return $this->readUInt16LE();
    }
  }
  
  /**
   * Read unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUInt16BE() {
    $tmp = unpack("n", $this->read(2));
    return $tmp[1];
  }
  
  /**
   * Read unsigned 16 bits numbers, in Big Endian syntax.
   * 
   * @return integer
   */
  function readUInt16LE() {
    $tmp = unpack("v", $this->read(2));
    return $tmp[1];
  }
  
  /**
   * Read 16 bits numbers.
   * 
   * @param string $endianness Equal to BE if you need Big Endian, LE if Little Endian. Equal to BE if not given
   * 
   * @return integer
   */
  function readInt16($endianness = "BE") {
    $int = 0;

    if ($endianness == "BE") {
      $int = $this->readUInt16BE();
    }
    elseif ($endianness == "LE") {
      $int = $this->readUInt16LE();
    }
    
    if ($int >= 0x8000) {
      $int -= 0x10000;
    }

    return $int;
  }
  
  /**
   * Read 8 bits numbers.
   * 
   * @return integer
   */
  function readUInt8() {
    $tmp = unpack("C", $this->read(1));
    return $tmp[1];
  }
  
  /**
   * Read unsigned 8 bits numbers.
   * 
   * @return integer
   */
  function readInt8() {
    $int = $this->readUInt8();
    if ($int >= 0x80) {
      $int -= 0x100;
    }
    return $int;
  }
  
  /**
   * Read a string
   * 
   * @param int $length The length of the string
   * 
   * @return string
   */
  function readString($length) {
    $tmp = unpack("A*", $this->read($length));
    return $tmp[1];
  }
  
  /**
   * Read a Dicom UID (series of integer, separated by ".")
   * 
   * @param int $length The length of the UID, equal to 64 if not given
   * 
   * @return string
   */
  function readUID($length = 64) {
    $tmp = unpack("A*", $this->read($length));
    return $tmp[1];
  }
} 
