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

		$this->assertCount(1, $post_byID);


		$ridiculous_large_id = 9999999999;
		$post_byID = ExampleModel::find($ridiculous_large_id);

		$this->assertCount(0, $post_byID);
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

	public function test_saving_meta_data_using_crud_methods()
	{
		$this->exampleModel->title = 'New Post';
		$post_id = $this->exampleModel->save();

		$metaKey = 'some_meta_key';
		$metaValue = 'The meta value';

		$this->assertInternalType(
			'integer',
			$metaID = $this->exampleModel->saveMeta($metaKey, $metaValue)
		);

		$valueToCheck = get_post_meta(
			$post_id,
			$metaKey,
			true
		);
		$this->assertEquals($metaValue, $valueToCheck);

		// Check getting proper data by querying the post meta by ID
		$postMetaObj = get_post_meta_by_id($metaID);
		$this->assertEquals($post_id, $postMetaObj->post_id);
		$this->assertEquals($metaKey, $postMetaObj->meta_key);
		$this->assertEquals($metaValue, $postMetaObj->meta_value);
	}

	public function test_updating_meta_data_using_crud_methods()
	{
		$this->exampleModel->title = 'New Post';
		$post_id = $this->exampleModel->save();

		$metaKey = 'some_meta_key';
		$firstMetaValue = 'First Value';

		$metaIdWhenInserting = $this->exampleModel->saveMeta($metaKey, $firstMetaValue);

		$secondMetaValue = 'Second Value';
		$metaIdWhenUpdating = $this->exampleModel->saveMeta($metaKey, $secondMetaValue);

		$this->assertEquals($metaIdWhenInserting, $metaIdWhenUpdating);

		$valueToCheck = get_post_meta($post_id, $metaKey, true);
		$this->assertEquals($secondMetaValue, $valueToCheck);
	}


	public function merge_with_common_args($args = [])
	{
		return array_merge(
			['post_type' => 'cpt-example'],
			$args
		);
	}
}
