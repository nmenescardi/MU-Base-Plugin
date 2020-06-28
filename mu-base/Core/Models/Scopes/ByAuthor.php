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
  }

  public function getArgs(): array
  {
    $author_args =  $this->filteredAuthorIDs
      ? ['author' => $this->filteredAuthorIDs]
      : [];

    return array_merge(
      $author_args,
      [
        'posts_per_page'  => 200,
        'orderby'         => 'date',
        'order'           => 'ASC',
      ]
    );
  }
}
