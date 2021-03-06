<?php

namespace ActiveRecord;

class ConnectionInfo {

	public $adapter = null;
	public $host = null;
	public $port = null;
	public $database = null;

	public $username = null;
	public $password = null;

	public $charset = null;

	public $decode = false;

	public function __construct($input = array())
	{
		foreach($input as $prop => $value)
		{
			if(property_exists($this, $prop))
			{
				$this->{$prop} = $value;
			}
		}
	}

	/**
	 * Parses a connection url and return a ConnectionInfo object
	 *
	 * Use this for any adapters that can take connection info in the form below
	 * to set the adapters connection info.
	 *
	 * <code>
	 * adapter://username:password@host[:port]/dbname
	 * adapter://urlencoded%20username:urlencoded%20password@host[:port]/dbname?decode=true
	 * adapter://username:password@unix(/some/file/path)/dbname
	 * </code>
	 *
	 * Sqlite has a special syntax, as it does not need a database name or user authentication:
	 *
	 * <code>
	 * sqlite://file.db
	 * sqlite://../relative/path/to/file.db
	 * sqlite://unix(/absolute/path/to/file.db)
	 * sqlite://windows(c%2A/absolute/path/to/file.db)
	 * </code>
	 *
	 * @param string $connection_url A connection URL
	 * @return ConnectionInfo the parsed URL as an object.
	 */
	public static function from_connection_url($connection_url)
	{
		$url = @parse_url($connection_url);

		if (!isset($url['host']))
			throw new DatabaseException('Database host must be specified in the connection string. If you want to specify an absolute filename, use e.g. sqlite://unix(/path/to/file)');

		$info = new self();
		$info->adapter = $url['scheme'];
		$info->host = $url['host'];

		if (isset($url['query']))
		{
			parse_str($url['query'], $params);

			if(isset($params['charset']))
				$info->charset = $params['charset'];

			if(isset($params['decode']))
				$info->decode = ($params['decode'] == 'true');
		}

		if(isset($url['path']))
			$info->database = substr($url['path'], 1);

		$allow_blank_db = ($info->adapter == 'sqlite');

		if ($info->host == 'unix(')
		{
			$socket_database = $info->host . '/' . $info->database;

			sscanf($socket_database, 'unix(%[^)])/%s', $host, $database);

			$info->host = $host;
			$info->database = $database;
		}
		elseif (substr($info->host, 0, 8) == 'windows(')
		{
			$info->host = urldecode(substr($info->host, 8) . '/' . substr($info->database, 0, -1));
			$info->database = null;
		}
		else
		{
			if ($allow_blank_db && $info->database)
				$info->host .= '/' . $info->database;
		}

		if (isset($url['port']))
			$info->port = $url['port'];

		if (isset($url['user']))
			$info->username = $info->decode ? urldecode($url['user']) : $url['user'];

		if (isset($url['pass']))
			$info->password = $info->decode ? urldecode($url['pass']) : $url['pass'];

		return $info;
	}

}
