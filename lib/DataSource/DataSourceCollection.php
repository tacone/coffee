<?php


namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * You neighbourly dot syntax data source.
 *
 * This class has been built to abstract away the horrible mess that
 * Eloquent is, internally.
 *
 * Usage:
 *  <pre>
 *      $datasource = new DataSource(new Article());
 *      $name = $datasource['author.name'];
 *      $datasource['author.name'] = 'new name';
 *      $datasource->save();
 *  </pre>
 *
 * You can get and set values using dot syntax. The class handles all
 * the complexities of finding fields, of instantiating a new model if the
 * relation is empty, and of saving all the bunch in the right order.
 *
 * No change to your models is needed.
 */
class DataSourceCollection extends DataSource
{
}
