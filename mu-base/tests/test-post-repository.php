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
		$new_term['title'] = 'Term To Match';
		$new_term['slug'] = sanitize_title('Term To Match');
		$taxonomy = 'base-example';

		$term = wp_insert_term(
			$new_term['title'],
			$taxonomy
		);

		$example_model = new \MUBase\Core\Models\Posts\Example();

		$related_posts = $this->factory->post->create_many(
			2,
			$this->merge_with_common_args([
				'post_category' => array($term['term_taxonomy_id']),
				'tax_input' => array(
					'prominence' => $new_term['slug']
				)
			])
		);

		$not_related_posts = $this->factory->post->create_many(
			3,
			$this->merge_with_common_args()
		);

		$latest = $example_model->related($taxonomy);
	}


	public function merge_with_common_args($args = [])
	{
		return array_merge(
			['post_type' => 'cpt-example'],
			$args
		);
	}
}
