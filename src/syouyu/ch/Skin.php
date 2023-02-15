<?php

declare(strict_types=1);

namespace syouyu\ch;

use stdClass;

class Skin extends stdClass{

	private static Skin $instance;

	public function __construct(){
		self::$instance = $this;
	}

	public static function getInstance(): Skin{
		return self::$instance;
	}
}