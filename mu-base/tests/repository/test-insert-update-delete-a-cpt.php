<?php

use MUBase\Core\Models\Posts\Example as ExampleModel;

class PostRepositoryTest extends WP_UnitTestCase
{

	public function setup()
	{
		parent::setUp();

		$this->author = $this->factory->user->create_and_get(array('role' => 'editor'));

		$this->exampleModel = new ExampleModel();
	}

	public function test_getting_proper_id_after_inserting_a_new_post()
	{
		$this->exampleModel->title = 'New Post';
		$post_id = $this->exampleModel->save();

		$this->assertTrue(is_numeric($post_id));
		$this->assertTrue($post_id > 0);
	}

	public function test_matching_properties_after_inserting_a_new_post()
	{
		$this->exampleModel->title = rand_str();
		$this->exampleModel->content = rand_str();
		$post_id = $this->exampleModel->save();

		$post_to_match = get_post($post_id);

		$this->assertEquals($this->exampleModel->title, $post_to_match->post_title);
		$this->assertEquals($this->exampleModel->content, $post_to_match->post_content);
		$this->assertEquals($this->exampleModel->ID, $post_to_match->ID);
		$this->assertEquals($this->exampleModel->status, $post_to_match->post_status);
		$this->assertEquals($this->exampleModel->author, $post_to_match->post_author);
		$this->assertEquals($this->exampleModel->date, $post_to_match->post_date);
	}

	public function test_post_status_when_inserting_a_new_post()
	{
		$statuses = [
			'publish', 'draft', 'private', 'inherit', 'trash', 'pending', 'trash'
		];

		foreach ($statuses as $status) {

			$exampleModel = new ExampleModel();
			$exampleModel->title = rand_str();
			$exampleModel->status = $status;
			$post_id = $exampleModel->save();

			$post_to_match = get_post($post_id);
			$this->assertEquals($exampleModel->status, $post_to_match->post_status);
			$this->assertEquals($status, get_post_status($post_id));
		}
	}

	public function test_post_author_when_inserting_a_new_post()
	{
		$this->exampleModel->title = rand_str();
		$this->exampleModel->author = $this->author->ID;
		$post_id = $this->exampleModel->save();

		$post_to_match = get_post($post_id);
		$this->assertEquals($this->exampleModel->author, $post_to_match->post_author);
	}

	public function test_custom_past_date_when_inserting_a_new_post()
	{
		$past_date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 day'));

		$this->exampleModel->title = rand_str();
		$this->exampleModel->date = $past_date;
		$post_id = $this->exampleModel->save();

		$post_to_match = get_post($post_id);
		$this->assertEquals($this->exampleModel->date, $post_to_match->post_date);
	}

	public function test_custom_properties_when_editing_a_post()
	{
		$this->exampleModel->title = rand_str();
		$post_id_inserting = $this->exampleModel->save(); // Insert post

		$this->exampleModel->title = $new_title = 'New title';
		$this->exampleModel->content = $new_content = 'New content';
		$this->exampleModel->date = $new_date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 day'));
		$this->exampleModel->author = $new_author = $this->author->ID;
		$this->exampleModel->status = $new_status = 'draft';

		$post_id_updating = $this->exampleModel->save(); // Update post
		$this->assertEquals($post_id_inserting, $post_id_updating);

		$post_to_match = get_post($post_id_updating);
		$this->assertEquals($new_title, $post_to_match->post_title);
		$this->assertEquals($new_content, $post_to_match->post_content);
		$this->assertEquals($new_date, $post_to_match->post_date);
		$this->assertEquals($new_author, $post_to_match->post_author);
		$this->assertEquals($new_status, $post_to_match->post_status);
	}

	public function test_get_instance_of_WP_Post_class()
	{
		$this->exampleModel->title = 'New title';
		$this->exampleModel->content = 'New content';
		$this->exampleModel->date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 day'));
		$this->exampleModel->author = $this->author->ID;
		$this->exampleModel->status = 'draft';

		$post_id_inserting = $this->exampleModel->save(); // Insert post

		$wp_post = $this->exampleModel->toPost();

		$this->assertInstanceOf('WP_Post', $wp_post);

		$post_to_match = get_post($post_id_inserting);
		$this->assertEquals($wp_post->post_title, 	$post_to_match->post_title);
		$this->assertEquals($wp_post->post_content, $post_to_match->post_content);
		$this->assertEquals($wp_post->post_date, 		$post_to_match->post_date);
		$this->assertEquals($wp_post->post_author, 	$post_to_match->post_author);
		$this->assertEquals($wp_post->post_status, 	$post_to_match->post_status);
	}

	public function test_inserting_a_new_post_using_an_array_as_arg()
	{
		$new_title = 'New title';
		$new_content = 'New content';
		$new_date = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 day'));
		$new_author = $this->author->ID;
		$new_status = 'draft';

		$post_id = $this->exampleModel->save([
			'post_title' => $new_title,
			'post_content' => $new_content,
			'post_date' => $new_date,
			'post_author' => $new_author,
			'post_status' => $new_status,
		]);

		$this->assertTrue(is_numeric($post_id));
		$this->assertTrue($post_id > 0);

		$post_to_match = get_post($post_id);
		$this->assertEquals($new_title, $post_to_match->post_title);
		$this->assertEquals($new_content, $post_to_match->post_content);
		$this->assertEquals($new_date, $post_to_match->post_date);
		$this->assertEquals($new_author, $post_to_match->post_author);
		$this->assertEquals($new_status, $post_to_match->post_status);
	}

	public function test_deleting_a_new_post()
	{
		$this->exampleModel->title = 'New Post';
		$this->exampleModel->save();
		$this->assertCount(1, ExampleModel::all());

		// Remove with no Arguments
		$this->exampleModel->delete();
		$this->assertCount(0, ExampleModel::all());
	}

	public function test_deleting_a_new_post_passing_post_id_as_arg()
	{
		$post_id = $this->factory->post->create(
			$this->merge_with_common_args()
		);

		$this->assertCount(1, ExampleModel::all());

		$this->exampleModel->delete($post_id);
		$this->assertCount(0, ExampleModel::all());
	}

	public function test_deleting_a_new_post_passing_wp_post_object_as_arg()
	{
		$post_id = $this->factory->post->create(
			$this->merge_with_common_args()
		);

		$all_posts = ExampleModel::all();

		$this->assertCount(1, $all_posts);

		$this->exampleModel->delete($all_posts[0]);
		$this->assertCount(0, ExampleModel::all());
	}

	public function merge_with_common_args($args = [])
	{
		return array_merge(
			['post_type' => 'cpt-example'],
			$args
		);
	}
}
