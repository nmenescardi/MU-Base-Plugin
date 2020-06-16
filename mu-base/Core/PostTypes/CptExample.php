<?php

namespace MUBase\Core\PostTypes;

class CptExample extends AbstractCPT
{

  public function key(): string
  {
    return 'cpt-example';
  }

  public function args(): array
  {
    return [
      'labels' => [
        'name'          => __('Examples'),
        'singular_name' => __('Example')
      ]
    ];
  }
}
