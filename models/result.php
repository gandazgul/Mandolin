<?php
/**
 * this is the default result class for all models
 *
 * @author gandazgul
 */

class Result
{
	public $isError;
	public $errorCode;
	public $errorStr;
	public $data;

	function  __construct()
	{
		$this->isError = false;
		$this->errorCode = 0;
		$this->errorStr = "";
		$this->data = array();
	}

	function  __destruct()
	{
		$this->isError = $this->errorCode = $this->errorStr = $this->data = null;
	}
}
?>
