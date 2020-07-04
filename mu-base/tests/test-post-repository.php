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



	public function merge_with_common_args($args = [])
	{
		return array_merge(
			['post_type' => 'cpt-example'],
			$args
		);
	}
}
