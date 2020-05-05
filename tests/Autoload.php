<?php
namespace MBOLoader;

class Autoload
{
	/**
	 * @var string
	 */
	private $namespace;
	/**
	 * @var string
	 */
	private $dir;
	/**
	 * @var int
	 */
	private $length;
	/**
	 * Autoload constructor.
	 *
	 * @param string $namespace Pass the namespace unescaped.
	 * @param string $dir
	 */
	public function __construct( $namespace, $dir )
	{
		// Make sure it ends with a '\'.
		$namespace       = rtrim( $namespace, '\\' ) . '\\';
		$this->namespace = $namespace;
		$this->length    = strlen( $namespace );
		$this->dir       = rtrim( $dir, '/' ) . '/';
	}
	/**
	 * @param string $search
	 * @return void
	 */
	public function load( $search )
	{
		if ( strncmp( $this->namespace, $search, $this->length ) !== 0 ) {
			return;
		}
		$name = substr( $search, $this->length );
		$path = $this->dir . str_replace( '\\', '/', $name ) . '.php';
		print_r($path);
		print_r("## ### ## ");
		if ( is_readable( $path ) ) {
			require $path;
		}
	}
}
?>