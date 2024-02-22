<?php declare ( strict_types = 1 );

namespace Northrook\Types;

class Url extends Type
{
	private static string $rootPath;

	protected bool   $isAbsolute = false;
	public readonly string $path;
	public readonly string $url;


	public function __construct(
		?string $string = null,
		?string $rootPath = null,
	) {
		$this->rootPath($rootPath ?? $_SERVER['DOCUMENT_ROOT']);


	}

	private function rootPath( string $path ): void {
		if ( isset( $this::$rootPath ) ) {
			return;
		}

		$this::$rootPath = $path;

	}

	public static function type( ?string $string = null, ?string $rootPath = null ) : Path {
		return new static( $string, $rootPath );
	}
}