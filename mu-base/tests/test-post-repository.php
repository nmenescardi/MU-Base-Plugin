<?php

class PostRepositoryTest extends WP_UnitTestCase
{

	public function setup()
	{
		parent::setUp();

		$this->postID = $this->factory->post->create([
			'post_type' => 'cpt-example'
		]);
	}

	/** @test */
	public function get_all_scope()
	{

		// Example Model
		$example_model = new \MUBase\Core\Models\Posts\Example(new WP_Query());

		// All
		$all = $example_model->all();

		$this->assertCount(1, $all);
	}
}
