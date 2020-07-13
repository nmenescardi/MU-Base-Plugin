<?php

namespace MUBase\Core\PostTypes;

class CptExample extends AbstractCPT
{

  public static function key(): string
  {
    return 'cpt-example';
  }

  public static function meta()
  {
    return [
      'meta_example_1' => [
        'type'    => 'integer',
        'single'  => true,
      ],
      'meta_example_2' => [
        'type'    => 'string',
        'single'  => false,
      ],
    ];
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
