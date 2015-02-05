<?php

/**
 * I need...
 */


// to hide grid buttons
$grid->buttons(false);

// to hide row buttons
$grid->prototype->actions(false);

// to print a collection field
$grid->text('categories')->lists('name')->join(', ');

// to disable pagination
$grid->paginate(false);
