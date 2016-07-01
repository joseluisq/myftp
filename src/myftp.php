<?php

/**
 * A smallest abstraction class for FTP handling.
 *
 * @version   1.0.0
 * @author    José Luis Quintana <joseluisquintana20@gmail.com>
 * @link      https://github.com/quintana-dev/myftp
 */
class MyFTP {

  var $_port;
  var $_passive;
  var $_hostname = '';
  var $_username = '';
  var $_password = '';
  var $_logged = FALSE;
  var $_connection_id;

  function __construct($setup) {
    $this->_hostname = isset($setup['hostname']) ? $setup['hostname'] : 'localhost';
    $this->_port = isset($setup['port']) ? $setup['port'] : 21;
    $this->_username = $setup['username'];
    $this->_password = $setup['password'];
    $this->_passive = isset($setup['passive']) ? $setup['passive'] : TRUE;
  }

  /**
   * Opens an FTP connection and start login.
   * 
   * @return boolean  Returns a FTP stream on success or FALSE on error.
   */
  function connect() {
    $connected = FALSE;
    $this->_connection_id = ftp_connect($this->_hostname, $this->_port);

    if ($this->_connection_id) {
      $this->_logged = ftp_login($this->_connection_id, $this->_username, $this->_password);

      if ($this->_logged) {
        $connected = $this->_connection_id;
        ftp_pasv($this->_connection_id, TRUE);
      }
    }

    return $connected;
  }

  /**
   * Checks if it has successful logged.
   * 
   * @return boolean
   */
  function is_logged() {
    return $this->_logged;
  }

  /**
   * Upload a file from local to server.
   * 
   * @param string $filepath_local
   * @param string $filepath_remote
   * @param string $transfer_mode  The transfer mode. Must be either FTP_ASCII or FTP_BINARY.
   * @return boolean
   */
  function upload($filepath_local, $filepath_remote, $transfer_mode = NULL) {
    $result = FALSE;

    if ($this->_logged) {
      $transfer_mode = $transfer_mode ? $transfer_mode : $this->get_transfer_mode($filepath_local);
      $result = ftp_put($this->_connection_id, $filepath_remote, $filepath_local, $transfer_mode);
    }

    return $result;
  }

  /**
   * Get FTP transfer mode
   * 
   * @param string $filepath  The transfer mode. Must be either FTP_ASCII or FTP_BINARY.
   * @return int
   */
  private function get_transfer_mode($filepath) {
    $ascii_array = array('txt', 'csv', 'tsv', 'js', 'html', 'css');
    $extension = end(explode('.', $filepath));
    return (in_array($extension, $ascii_array)) ? FTP_ASCII : FTP_BINARY;
  }

  /**
   * Downloads a file from the FTP server.
   * 
   * @param string $filepath_remote  Filename of specified file
   * @param string $transfer_mode  The transfer mode. Must be either FTP_ASCII or FTP_BINARY.
   * @return int Returns TRUE on success or FALSE on failure.
   */
  function get_file($filepath_remote, $filepath_local, $transfer_mode = NULL) {
    $result = FALSE;

    if ($this->_logged) {
      $transfer_mode = $transfer_mode ? $transfer_mode : $this->get_transfer_mode($filepath_remote);
      $result = ftp_get($this->_connection_id, $filepath_local, $filepath_remote, $transfer_mode);
    }

    return $result;
  }

  /**
   * Returns a list of files in the given directory.
   * 
   * @param $dirname  Directory path.
   * @return array  Returns an array of filenames from the specified directory 
   * on success or FALSE on error.
   */
  function get_list($dirname = '.') {
    return ($this->_logged) ? ftp_nlist($this->_connection_id, $dirname) : FALSE;
  }

  /**
   * Creates a directory.
   * 
   * @return string Returns the newly created directory name on success or FALSE on error.
   */
  function mkdir($dirname) {
    return ftp_mkdir($this->_connection_id, $dirname);
  }

  /**
   * Removes a directory
   * 
   * @return boolean Returns TRUE on success or FALSE on failure.
   */
  function rmdir($dirname) {
    return ftp_rmdir($this->_connection_id, $dirname);
  }

  /**
   * Deletes a file on the FTP server.
   * 
   * @return boolean Returns TRUE on success or FALSE on failure.
   */
  function unlink($filename) {
    return ftp_delete($this->_connection_id, $filename);
  }

  /**
   * Closes an FTP connection.
   * 
   * @return boolean Returns TRUE on success or FALSE on failure.
   */
  function close() {
    return ftp_close($this->_connection_id);
  }

}
