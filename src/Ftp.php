<?php

class Ftp implements FtpInterface
{
	private $conn;

	private $host;

	private $pass;

	private $user;

	public $loggedin = false;

	public function __construct(
		$host,
		$port = 21
	) {
		$this->conn = ftp_connect($host, $port);

		$this->host = $host;

		if (!$this->conn) throw new \Exception("Could not connect to $host on port $port.");
	}

	public function chdir($dir)
	{
		@ftp_chdir($this->conn, $dir);
	}

	public function close()
	{
		return @ftp_close($this->conn);
	}

	public function download($remote, $local, $mode = FTP_BINARY)
	{
		return @ftp_get($this->conn, $local, $remote, $mode);
	}

	public function isDir($dir)
	{
		if ($this->loggedin === true) {
			return (is_dir("ftp://{$this->user}:{$this->pass}@{$this->host}{$dir}"));
		} else {
			throw new \Exception("Unable to check directory. Ftp not logged in.");
		}
	}

	public function list($dir)
	{
		return @ftp_nlist($this->conn, $dir);
	}

	public function login($user, $pass)
	{
		$this->user = $user;
		$this->pass = $pass;

		if (@ftp_login($this->conn, $user, $pass) === true) {
			$this->loggedin = true;
			return true;
		}

		return false;
	}

	public function pasv($bool)
	{
		return @ftp_pasv($this->conn, $bool);
	}

	public function upload($local, $remote, $mode = FTP_BINARY)
	{
		return @ftp_put($this->conn, $remote, $local, $mode);
	}
}
