<?php

use MUBase\Core\Models\Posts\Example as ExampleModel;

class CptMetaRepositoryTest extends WP_UnitTestCase
{

  public function setup()
  {
    parent::setUp();

    $this->author = $this->factory->user->create_and_get(array('role' => 'editor'));

    $this->exampleModel = new ExampleModel();
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

  public function test_deleting_meta_data_using_crud_methods()
  {
    $this->exampleModel->title = 'New Post';
    $post_id = $this->exampleModel->save();

    $metaKey = 'some_meta_key';
    $metaValue = 'Value to Remove';

    $metaID = $this->exampleModel->saveMeta($metaKey, $metaValue);
    $this->assertInternalType('integer', $metaID);
    $this->assertTrue($metaID > 0);

    // Assert Deleting goes well
    $this->assertTrue($this->exampleModel->deleteMeta($metaKey));
    $this->assertEmpty(get_post_meta($post_id, $metaKey, true));

    // Deleting return false when the key wasn't found or it was an issue
    $this->assertFalse($this->exampleModel->deleteMeta('Some_Dummy_Meta'));
  }

  public function test_getting_meta_data_using_crud_methods()
  {
    $this->exampleModel->title = 'New Post';
    $post_id = $this->exampleModel->save();

    $metaKey = 'some_meta_key';
    $metaValue = 'Value to retrieve and compare';

    $metaID = $this->exampleModel->saveMeta($metaKey, $metaValue);

    // Test getting same value
    $this->assertEquals(
      $this->exampleModel->getMeta($metaKey, true),
      $metaValue
    );

    // same value as array
    $this->assertEquals(
      $this->exampleModel->getMeta($metaKey),
      array($metaValue)
    );

    // Test getting empty after deleting
    $this->assertTrue($this->exampleModel->deleteMeta($metaKey));
    $this->assertEmpty($this->exampleModel->getMeta($metaKey));


    // Test getting the default value back when it's empty
    $defaultValue = 'Value to receive';
    $this->assertEquals(
      $this->exampleModel->getMeta($metaKey, true, $defaultValue),
      $defaultValue
    );
  }

  public function test_saving_meta_data_as_properties()
  {
    $some_value = 'Some Value';

    $this->exampleModel->title = 'New Post';
    $this->exampleModel->meta_example_1 = $some_value;
    $this->exampleModel->none_existing_meta = $some_value;
    $post_id = $this->exampleModel->save();

    $this->assertEquals(
      $this->exampleModel->getMeta('meta_example_1', true),
      $some_value
    );

    $this->assertEmpty($this->exampleModel->getMeta('meta_example_2', true));
    $this->assertEmpty($this->exampleModel->getMeta('none_existing_meta', true));
  }

  public function test_getting_meta_data_as_properties()
  {
    $this->exampleModel->title = 'New Post';
    $post_id = $this->exampleModel->save();

    $some_value = 'Some Value';
    add_post_meta($post_id, 'meta_example_1', $some_value);

    $this->assertEquals(
      $this->exampleModel->meta_example_1, // Accessing as property
      $some_value
    );

    // Accessing existing meta but empty
    $this->assertEmpty($this->exampleModel->meta_example_2);

    // Accessing non existing meta
    $this->assertEmpty($this->exampleModel->nonExistingMeta);
  }

  public function merge_with_common_args($args = [])
  {
    return array_merge(
      ['post_type' => 'cpt-example'],
      $args
    );
  }
}
