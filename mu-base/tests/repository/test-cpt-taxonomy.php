<?php

use MUBase\Core\Models\Posts\Example as ExampleModel;

class CptTaxonomyRepositoryTest extends WP_UnitTestCase
{

  public function setup()
  {
    parent::setUp();

    $this->author = $this->factory->user->create_and_get(array('role' => 'editor'));

    $this->exampleModel = new ExampleModel();
  }


  public function test_getting_cpt_terms_from_specific_taxonomy()
  {
    // Create Post
    $this->exampleModel->title = 'New Post';
    $post_id = $this->exampleModel->save();

    //Empty terms
    $expectedTerms = [];
    $actualTerms = $this->exampleModel->tax_examples;
    $this->assertEqualSets($actualTerms, $expectedTerms);

    // Create terms
    $cat1 = $this->factory->term->create_and_get(array(
      'taxonomy'  => 'tax-example'
    ));
    wp_set_object_terms($post_id, $cat1->name, 'tax-example', true);
    $cat2 = $this->factory->term->create_and_get(array(
      'taxonomy'  => 'tax-example'
    ));
    wp_set_object_terms($post_id, $cat2->name, 'tax-example', true);

    // Except receiving 2 terms
    $actualTerms = $this->exampleModel->tax_examples;
    $this->assertCount(2, $actualTerms);
    $this->assertInstanceOf('WP_Term', $actualTerms[0]);
  }

  public function merge_with_common_args($args = [])
  {
    return array_merge(
      ['post_type' => 'cpt-example'],
      $args
    );
  }
}
