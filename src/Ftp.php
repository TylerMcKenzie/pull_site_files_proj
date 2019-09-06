<?php

class Ftp
{
	private $conn;

	public function __construct(
		$host,
		$port = 21
	) {
		$this->conn = ftp_connect($host, $port);
	}

	public function chdir($dir)
	{
		ftp_chdir($this->conn, $dir);
	}

	public function close()
	{
		return ftp_close($this->conn);
	}

	public function login($user, $pass)
	{
		return ftp_login($this->conn, $user, $pass);
	}

	public function nlist($dir)
	{
		return ftp_nlist($this->conn, $dir);
	}

	public function pasv($bool)
	{
		return ftp_pasv($this->conn, $bool);
	}
}
