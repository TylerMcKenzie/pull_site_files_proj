<?php

class SFtp implements FtpInterface
{
	private $conn;

	private $sftp;

	public function __construct(
		$host,
		$port = 22
	) {
		$this->conn = @ssh2_connect($host, $port);

		if (!$this->conn) throw new \Exception("Could not connect to $host on port $port.");
	}

	public function chdir($dir) {}

	public function close()
	{
		return @ssh2_disconnect($this->conn);
	}

	public function download($remote, $local)
	{
		$remote_stream = @fopen("ssh2.sftp://{$this->sftp}/{$remote}", "r");
		$local_stream = @fopen($local, "w");

		if (!$remote_stream)
			throw new \Exception("Could not open file: {$remote}");

		$download_data = @fgets($remote_stream);

		if ($download_data === false)
			throw new \Exception("Could not read remote file {$remote}.");

		if (@fwrite($local_stream, $download_data) === false)
			throw new \Exception("Could not download data from file: {$remote}.");

		@fclose($remote_stream);
		@fclose($local_stream);
	}

	public function login($user, $pass)
	{
		if (!@ssh2_auth_password($this->conn, $user, $pass))
			throw new \Exception("Could not authenticate with username {$user} " . "and password {$pass}.");

		$this->sftp = @ssh2_sftp($this->conn);

		if (!$this->sftp)
			throw new \Exception("Could not initialize SFTP subsystem.");
		else
			return true;
	}

	public function list($dir)
	{
		$handle = opendir("ssh2.sftp://{$this->sftp}/{$dir}");

		$files = [];

		while ($file = readdir($handle)) {
			if ($file !== "." && $file !== "..") {
				$files[] = $file;
			}
		}

		closedir($handle);

		return $files;
	}

	public function upload($remote, $local)
	{
		$stream = @fopen("ssh2.sftp://{$this->sftp}/{$remote}", "w");

		if (!$stream)
			throw new \Exception("Could not open file: {$remote}");

		$upload_data = @file_get_contents($local);

		if ($upload_data === false)
			throw new \Exception("Could not read local file {$local}.");

		if (@fwrite($stream, $upload_data) === false)
			throw new \Exception("Could not upload data from file: {$local}.");

		@fclose($stream);
	}
}
