<?php
/**
 * @namespace
 */
namespace CronManager\Ftp;

use CronManager\Ftp;

/**
 * Class File
 * @package CronManager\Ftp
 */
class File
{
	/**
	 * The FTP connection
	 *
	 * @var \CronManager\Ftp\Ftp
	 */
	protected $_ftp = null;
	 
	/**
	 * The file path and file name
	 *
	 * @var string
	 */
	protected $_path = null;
	 
	/**
	 * The file name without the path
	 *
	 * @var string
	 */
	protected $_name = null;
	 
	/**
	 * The transfer mode for this file
	 *
	 * @var int
	 */
	protected $_mode = null;
	
	/**
	 * File options
	 * @var array
	 */
	protected $_options = array();
	 
	/**
	 * Instantiate an FTP file
	 *
	 * @param string $path The full remote path to the file
	 * @param \CronManager\Ftp\Ftp $ftp The FTP connection
	 * @param array $options
	 */
	public function __construct($path, $ftp, array $options = array())
	{
		$this->_path = $path;
		$this->_ftp = $ftp;
		$this->_name = basename($this->path);
		$this->_options = $options;
	}
	 
	/**
	 * Provide read-only access to properties
	 *
	 * @param string $name The property to get
	 * @return mixed
	 */
	public function __get($name)
	{
		switch ($name) {
			case 'name':
				return $this->_name;
			case 'path':
				return $this->_path;
			case 'size':
				$size = ftp_size($this->_ftp->getConnection(), $this->_name);
				return ($size != -1) ? $size : false;
		}
		throw new Exception('Unknown property "' . $name . '"');
	}
	 
	/**
	 * Whether or not this FTP resource is a file
	 *
	 * @return boolean
	 */
	public function isFile()
	{
		return true;
	}
	 
	/**
	 * Whether or not this FTP resource is a directory
	 *
	 * @return boolean
	 */
	public function isDirectory()
	{
		return false;
	}
	 
	/**
	 * Set the transfer mode for this file, overrides the FTP connection default
	 *
	 * @param int $mode [optional] The transfer mode
	 * @return \CronManager\Ftp\File
	 */
	public function setMode($mode = null)
	{
		$this->_mode = $mode;
		 
		return $this;
	}
	 
	/**
	 * Save to a local path using the remote file name
	 *
	 * @param string $path The full path to save to
	 * @param int $mode [optional] The transfer mode
	 * @param int $offset [optional] The offset to start from for resuming
	 * @return \CronManager\Ftp\File
	 */
	public function saveToPath($path, $mode = null, $offset = 0)
	{
		if (substr($path, -1) != '/') {
			$path = $path . '/';
		}
		$this->saveToFile($path . basename($this->_name), $mode, $offset);
		 
		return $this;
	}
	 
	/**
	 * Save to a local file
	 *
	 * @param string $file The full path to the local file
	 * @param int $mode [optional] The transfer mode
	 * @param int $offset [optional] The offset to start from for resuming
	 * @return \CronManager\Ftp\File
	 */
	public function saveToFile($file, $mode = null, $offset = 0)
	{
		if ($mode === null) {
			$mode = ($this->_mode === null ? $this->_ftp->determineMode($this->_path) : $this->_mode);
		}
		
		$get = @ftp_get($this->_ftp->getConnection(), $file, $this->_path, $mode, $offset);
		if ($get === false) {
			//throw new Exception('Unable to save file "' . $this->path . '"')
		}
		 
		return $this;
	}
	 
	/**
	 * Upload a local file
	 *
	 * @param string $localFilepath The full path to the local file
	 * @param int $mode [optional] The transfer mode
	 * @param int $startPos [optional] The offset to start from for resuming
	 * @return \CronManager\Ftp\File
	 */
	public function put($localFilepath, $mode = null, $startPos = 0)
	{
		if ($mode === null) {
			$mode = ($this->_mode === null ? $this->_ftp->determineMode($localFilepath) : $this->_mode);
		}
		$put = @ftp_put($this->_ftp->getConnection(), $this->_path, $localFilepath, $mode, $startPos);
		if ($put === false) {
			//throw new Exception('Unable to put file "' . $this->path . '"')
		}
		 
		return $this;
	}
	 
	/**
	 * Change the file permissions
	 *
	 * @param int|string $mode
	 * @return \CronManager\Ftp\File
	 */
	public function chmod($mode)
	{
		$this->_ftp->chmod($this->_path, $mode);
		 
		return $this;
	}
	 
	/**
	 * Rename the file
	 *
	 * @param string $filename The new filename
	 * @return \CronManager\Ftp\File
	 */
	public function rename($filename)
	{
		// ftp_rename
		 
		return $this;
	}
	 
	/**
	 * Copy the file to another filename or location
	 *
	 * @param string $filename
	 * @return \CronManager\Ftp\File
	 */
	public function copy($filename)
	{
		// copy
	}
	 
	/**
	 * Move the file to another location
	 *
	 * @param string $path
	 * @return \CronManager\Ftp\File
	 */
	public function move($path)
	{
		// move
		 
		return $this;
	}
	 
	/**
	 * Delete the file
	 *
	 * @return \CronManager\Ftp\File
	 */
	public function delete()
	{
		// delete
		 
		return $this;
	}
	 
	/**
	 * Whether or not the file exists
	 *
	 * @return boolean
	 */
	public function exists()
	{
		// Unfinished
	}
}