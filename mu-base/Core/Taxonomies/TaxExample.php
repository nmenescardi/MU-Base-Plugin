<?php

namespace MUBase\Core\Taxonomies;

class TaxExample extends AbstractTaxonomy
{

  protected $taxonomy_slug;
  protected $associated_post_types;

  public function __construct(array $associated_cpts)
  {
    $this->associated_cpts = $associated_cpts;

    $this->taxonomy_slug = 'example';
  }

  static public function key(): string
  {
    return 'tax-example';
  }

  public function pluralLabel(): string
  {
    return 'tax_examples';
  }

  public function singleLabel(): string
  {
    return 'tax_example';
  }
}
