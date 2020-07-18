<?php

use MUBase\Core\Models\Posts\Example as ExampleModel;

class ScopeRepositoryTest extends WP_UnitTestCase
{

  public function setup()
  {
    parent::setUp();

    $this->author = $this->factory->user->create_and_get(array('role' => 'editor'));

    $this->exampleModel = new ExampleModel();
  }

  public function test_getting_an_exception_when_scope_is_not_defined()
  {
    $this->expectException(\BadMethodCallException::class);

    $this->exampleModel->notValidScope();
  }

  public function test_getting_an_exception_when_static_scope_is_not_defined()
  {
    $this->expectException(\BadMethodCallException::class);

    ExampleModel::notValidScope();
  }

  public function test_get_all_scope()
  {
    $posts = $this->factory->post->create_many(
      25,
      $this->merge_with_common_args()
    );

    $all = ExampleModel::all();

    $this->assertCount(25, $all);
  }

  public function test_get_byID_scope()
  {
    $new_post_ids = $this->factory->post->create_many(
      5,
      $this->merge_with_common_args()
    );

    $some_id = $new_post_ids[array_rand($new_post_ids)]; // Grab any ID

    $post_byID = ExampleModel::find($some_id);

    $this->assertNotNull($post_byID);


    $ridiculous_large_id = 9999999999;
    $post_byID = ExampleModel::find($ridiculous_large_id);

    $this->assertNull($post_byID);
  }

  public function test_get_latest_scope_in_proper_order()
  {

    $post_1 = $this->factory->post->create_and_get(
      $this->merge_with_common_args([
        'post_date' => strftime('%Y-%m-%d %H:%M:%S', strtotime('-10 day'))
      ])
    );

    $post_3 = $this->factory->post->create_and_get(
      $this->merge_with_common_args([
        'post_date' => strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 day'))
      ])
    );

    $post_2 = $this->factory->post->create_and_get(
      $this->merge_with_common_args([
        'post_date' => strftime('%Y-%m-%d %H:%M:%S', strtotime('-7 day'))
      ])
    );

    $latest = ExampleModel::latest(3);
    $this->assertEquals([$post_3, $post_2, $post_1], $latest);

    $post_4 = $this->factory->post->create_and_get(
      $this->merge_with_common_args([
        'post_date' => strftime('%Y-%m-%d %H:%M:%S', strtotime('-3 day'))
      ])
    );

    $latest = ExampleModel::latest(4);
    $this->assertEquals([$post_4, $post_3, $post_2, $post_1], $latest);

    $latest = ExampleModel::latest(3);
    $this->assertEquals([$post_4, $post_3, $post_2], $latest);
  }

  public function test_get_latest_scope_with_pagination()
  {
    $posts = $this->factory->post->create_many(
      25,
      $this->merge_with_common_args()
    );

    $latest = ExampleModel::latest(20);

    $this->assertCount(20, $latest);
  }

  public function test_get_by_author_scope()
  {
    $this->factory->post->create_many(
      2,
      $this->merge_with_common_args(['post_author' => 1])
    );

    $this->factory->post->create_many(
      3,
      $this->merge_with_common_args(['post_author' => 2])
    );

    $this->factory->post->create_many(
      5,
      $this->merge_with_common_args(['post_author' => 3])
    );

    // Author User ID 1
    $this->assertCount(2, ExampleModel::byAuthor(1));

    // Author User ID 2
    $this->assertCount(3, ExampleModel::byAuthor(2));

    // Author User ID 3
    $this->assertCount(5, ExampleModel::byAuthor(3));

    // Posts of Authors 1 and 3
    $this->assertCount(7, ExampleModel::byAuthor([1, 3]));


    // Another post type -> should not be included on the Example CPT query
    $this->factory->post->create_many(
      5,
      ['post_author' => 3]
    );

    // Still same results
    $this->assertCount(5, ExampleModel::byAuthor(3));
    $this->assertCount(7, ExampleModel::byAuthor([1, 3]));
  }

  public function test_get_related_scope()
  {
    $new_term = 'Term To Match';
    $taxonomy = 'base-example';

    $term = wp_insert_term(
      $new_term,
      $taxonomy
    );

    $this->exampleModel->title = 'Post To relate';
    $new_post_id = $this->exampleModel->save();

    $amount_of_posts_to_match = 2;
    $related_posts = $this->factory->post->create_many(
      $amount_of_posts_to_match,
      $this->merge_with_common_args()
    );

    foreach (array_merge($related_posts, [$new_post_id]) as $post_id) {
      wp_set_object_terms($post_id, $term['term_id'], $taxonomy);
    }

    $non_related_posts = $this->factory->post->create_many(
      3,
      $this->merge_with_common_args()
    );

    $related_posts = $this->exampleModel->related($taxonomy);

    $this->assertCount($amount_of_posts_to_match, $related_posts);
  }

  public function test_two_posts_are_same_post_type__check_scope_evaluation()
  {
    $post_id = $this->factory->post->create(
      $this->merge_with_common_args()
    );

    $is_same_post_type = $this->exampleModel->isSameType($post_id);

    $this->assertTrue($is_same_post_type);
  }

  public function test_two_posts_are_NOT_the_same_post_type()
  {
    $page_id = $this->factory->post->create(['post_type' => 'page']);

    $is_NOT_same_post_type = $this->exampleModel->isSameType($page_id);

    $this->assertFalse($is_NOT_same_post_type);
  }

  public function merge_with_common_args($args = [])
  {
    return array_merge(
      ['post_type' => 'cpt-example'],
      $args
    );
  }
}
