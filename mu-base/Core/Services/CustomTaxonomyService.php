<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;

class CustomTaxonomyService implements ServiceProviderInterface
{

  public function register($container)
  {
    $this->container = $container;

    $this->init_custom_taxonomy([
      'MUBase\Core\Taxonomies\TaxExample' => [
        \MUBase\Core\PostTypes\CptExample::key()
      ]
    ]);
  }

  protected function init_custom_taxonomy(array $taxonomies): void
  {
    foreach ($taxonomies as $tax_class => $associated_cpts) {

      $taxonomy = new $tax_class($associated_cpts);
      $taxonomy->init();

      // Update Array containing all the Taxonomies for a CPT using plural name.
      foreach ($associated_cpts as $cpt) {

        $cpt_taxonomies = $this->container['cpt_taxonomies'][$cpt] ?? [];

        $cpt_taxonomies[$cpt][$taxonomy->pluralLabel()] = $taxonomy::key();

        $this->container->offsetSet('cpt_taxonomies', $cpt_taxonomies);
      }
    }
  }
}
