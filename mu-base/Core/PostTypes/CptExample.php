<?php

namespace MUBase\Core\PostTypes;

class CptExample extends AbstractCPT
{

  protected $meta = [
    'meta_example_1' => [
      'type'    => 'integer',
      'single'  => true,
    ],
    'meta_example_2' => [
      'type'    => 'integer',
      'single'  => true,
    ],
  ];

  public static function key(): string
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
