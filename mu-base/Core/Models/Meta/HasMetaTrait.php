<?php

namespace MUBase\Core\Models\Meta;

trait HasMetaTrait
{

  // To be overriden by different models. 
  // Should be 'post', 'comment', 'term', 'user', or any other object type with an associated meta table.
  protected $meta_type = 'post';

  /**
   * Insert or Update metadata
   *
   * @param string $key
   * @param [type] $value
   * @return int|bool
   */
  public function saveMeta(string $key, $value)
  {
    return \update_metadata(
      $this->meta_type,
      $this->ID,
      $key,
      $value
    );
  }

  public function getMeta(string $key, bool $single = false, $default = false)
  {
    $metaValue = \get_metadata(
      $this->meta_type,
      $this->ID,
      $key,
      $single
    );

    return
      !$metaValue && $default
      ? $default
      : $metaValue;
  }

  public function deleteMeta(string $key, $value = '', bool $deleteAll = false)
  {
    return \delete_metadata(
      $this->meta_type,
      $this->ID,
      $key,
      $value,
      $deleteAll
    );
  }
}
