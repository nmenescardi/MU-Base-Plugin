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

  public function getMeta(string $key, bool $single = null, $default = false)
  {
    $single = $single ?? $this->isSingleValueMeta($key);

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

  public function getMetaAsArray(string $key, $default = false)
  {
    return $this->getMeta($key, false, $default);
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

  protected function getMetaArgs(): array
  {
    $metaArgs = [];

    foreach (static::boundCPT()::meta() as $metaKey => $args) {

      if (isset($this->$metaKey))
        $metaArgs[$metaKey] = $this->$metaKey;
    }

    return $metaArgs;
  }

  protected function getSingleMetaArgs($keyToSearch)
  {
    foreach (static::boundCPT()::meta() as $metaKey => $args) {

      if ($keyToSearch === $metaKey)
        return $args;
    }

    return false;
  }

  protected function isSingleValueMeta($key)
  {
    if ($args = $this->getSingleMetaArgs($key))
      return $args['single'];

    return true;
  }
}
