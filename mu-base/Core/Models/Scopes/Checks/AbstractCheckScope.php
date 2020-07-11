<?php

namespace MUBase\Core\Models\Scopes\Checks;

use MUBase\Core\Models\Scopes\AbstractScope;

abstract class AbstractCheckScope extends AbstractScope
{
  abstract public function evaluate(): bool;
}
