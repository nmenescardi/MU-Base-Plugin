<?php

namespace MUBase\Core\Taxonomies;

class TaxExample extends AbstractTaxonomy
{


  protected $taxonomy_slug;
  protected $associated_post_types;

  protected $single_label;
  protected $plural_label;

  public function __construct(array $associated_cpts)
  {
    $this->associated_cpts  = $associated_cpts;

    $this->taxonomy_slug = 'example';

    $this->single_label = 'Example';
    $this->plural_label = 'Examples';
  }

  static public function key(): string
  {
    return 'base-example';
  }
}
