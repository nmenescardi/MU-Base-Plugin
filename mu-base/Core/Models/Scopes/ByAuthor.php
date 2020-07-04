<?php

namespace MUBase\Core\Models\Scopes;

class ByAuthor extends AbstractScope
{

  protected function filterParams()
  {
    $this->filteredAuthorIDs =
      (is_array($this->rawParams) && isset($this->rawParams[0]))
      ? $this->rawParams[0]
      : false;

    // If array -> convert to comma separated string
    if (is_array($this->filteredAuthorIDs))
      $this->filteredAuthorIDs = implode(',', $this->filteredAuthorIDs);
  }

  protected function concreteArgs(): array
  {
    return  $this->filteredAuthorIDs
      ? ['author' => $this->filteredAuthorIDs]
      : [];
  }
}
