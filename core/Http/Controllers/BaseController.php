<?php

namespace AcfJsonField\Http\Controllers;

use AcfJsonField\Contracts\Prefixer;
use AcfJsonField\Contracts\StaticInitiator;

class BaseController {
	use StaticInitiator;
	use Prefixer;
}
