<?php

namespace MUBase\Core\Models\Scopes;

class Related extends AbstractScope
{

  protected function filterParams()
  {
    $this->taxonomyKey =
      (is_array($this->rawParams) && isset($this->rawParams[0]))
      ? $this->rawParams[0]
      : '';
  }

  protected function concreteArgs(): array
  {
    $postID = $this->model->ID;

    $args = ['post__not_in'  => array($postID)];

    $terms = get_the_terms(
      $postID,
      $this->taxonomyKey
    );

    if ($terms) {

      $termSlug = array_shift($terms);

      $args['tax_query'] = [
        [
          'taxonomy'  => $this->taxonomyKey,
          'field'     => 'slug',
          'terms'     => $termSlug,
        ]
      ];
    }

    return $args;
  }
}
