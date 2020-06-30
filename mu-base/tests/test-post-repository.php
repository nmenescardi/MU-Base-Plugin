<?php

class PostRepositoryTest extends WP_UnitTestCase
{

	/** @test */
	public function get_all_scope()
	{

		// Example Model
		$example_model = new \MUBase\Core\Models\Posts\Example(new WP_Query());

		// All
		$all = $example_model->all();


		$this->assertTrue(true);
	}
}
