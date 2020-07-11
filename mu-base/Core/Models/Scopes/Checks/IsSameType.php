<?php

namespace MUBase\Core\Models\Scopes\Checks;

class IsSameType extends AbstractCheckScope
{

  protected function filterParams()
  {
    $this->otherPostIdToCheck =
      (is_array($this->rawParams) && isset($this->rawParams[0]))
      ? $this->rawParams[0]
      : false;

    $this->currentModelPostTypeKey = $this->model::key();
  }

  public function evaluate(): bool
  {
    return
      $this->otherPostIdToCheck &&
      $this->currentModelPostTypeKey &&
      $this->currentModelPostTypeKey === get_post_type($this->otherPostIdToCheck);
  }
}
