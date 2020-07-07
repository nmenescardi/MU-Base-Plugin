<?php

class PostRepositoryTest extends WP_UnitTestCase
{

	public function setup()
	{
		parent::setUp();
	}

	/** @test */
	public function get_all_scope()
	{

		$example_model = new \MUBase\Core\Models\Posts\Example();

		$posts = $this->factory->post->create_many(
			25,
			$this->merge_with_common_args()
		);

		$all = $example_model->all();

		$this->assertCount(25, $all);
	}

	/** @test */
	public function get_byID_scope()
	{

		$example_model = new \MUBase\Core\Models\Posts\Example();

		$new_post_ids = $this->factory->post->create_many(
			5,
			$this->merge_with_common_args()
		);

		$some_id = $new_post_ids[array_rand($new_post_ids)]; // Grab any ID

		$post_byID = $example_model->byID($some_id);

		$this->assertCount(1, $post_byID);


		$ridiculous_large_id = 9999999999;
		$post_byID = $example_model->byID($ridiculous_large_id);

		$this->assertCount(0, $post_byID);
	}

	/** @test */
	public function get_latest_scope_with_pagination()
	{
		$example_model = new \MUBase\Core\Models\Posts\Example();

		$posts = $this->factory->post->create_many(
			25,
			$this->merge_with_common_args()
		);

		$latest = $example_model->latest(20);

		$this->assertCount(20, $latest);
	}

	/** @test */
	public function get_by_author_scope()
	{
		$example_model = new \MUBase\Core\Models\Posts\Example();

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
		$this->assertCount(2, $example_model->byAuthor(1));

		// Author User ID 2
		$this->assertCount(3, $example_model->byAuthor(2));

		// Author User ID 3
		$this->assertCount(5, $example_model->byAuthor(3));

		// Posts of Authors 1 and 3
		$this->assertCount(7, $example_model->byAuthor([1, 3]));


		// Another post type -> should not be included on the Example CPT query
		$this->factory->post->create_many(
			5,
			['post_author' => 3]
		);

		// Still same results
		$this->assertCount(5, $example_model->byAuthor(3));
		$this->assertCount(7, $example_model->byAuthor([1, 3]));
	}


	/** @test */
	public function get_related_scope()
	{
		$new_term = 'Term To Match';
		$taxonomy = 'base-example';

		$term = wp_insert_term(
			$new_term,
			$taxonomy
		);

		$example_model = new \MUBase\Core\Models\Posts\Example();
		$example_model->title = 'Post To relate';
		$new_post_id = $example_model->save();

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

		$related_posts = $example_model->related($taxonomy);

		$this->assertCount($amount_of_posts_to_match, $related_posts);
	}

	/** @test */
	public function test_getting_proper_id_after_inserting_a_new_post()
	{
		$example_model = new \MUBase\Core\Models\Posts\Example();
		$example_model->title = 'New Post';
		$post_id = $example_model->save();

		$this->assertTrue(is_numeric($post_id));
		$this->assertTrue($post_id > 0);
	}


	public function merge_with_common_args($args = [])
	{
		return array_merge(
			['post_type' => 'cpt-example'],
			$args
		);
	}
}
