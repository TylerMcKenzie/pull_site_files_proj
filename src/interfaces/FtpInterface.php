<?php

interface FtpInterface
{
	public function chdir($directory);

	public function close();

	public function download($remote, $local);

	public function login($user, $pass);

	public function list($directory);

	public function pasv($bool);

	public function upload($remote, $local);
}
