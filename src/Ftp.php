<?php

class Ftp implements FtpInterface
{
	private $conn;

	public function __construct(
		$host,
		$port = 21
	) {
		$this->conn = ftp_connect($host, $port);

		if (!$this->conn) throw new \Exception("Could not connect to $host on port $port.");
	}

	public function chdir($dir)
	{
		ftp_chdir($this->conn, $dir);
	}

	public function close()
	{
		return ftp_close($this->conn);
	}

	public function download($remote, $local, $mode = FTP_BINARY)
	{
		return ftp_get($this->conn, $remote, $local, $mode);
	}

	public function list($dir)
	{
		return ftp_nlist($this->conn, $dir);
	}

	public function login($user, $pass)
	{
		return ftp_login($this->conn, $user, $pass);
	}

	public function pasv($bool)
	{
		return ftp_pasv($this->conn, $bool);
	}

	public function upload($remote, $local, $mode = FTP_BINARY)
	{
		return ftp_put($this->conn, $remote, $local, $mode);
	}
}
