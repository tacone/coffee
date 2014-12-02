<?php

// HasOne <---> BelongsTo
// HasMany <---> BelongsTo
// MorphTo <---> MorphToMany

// BelongsToMany <---> BelongsToMany


$form->multiselect('author.books'); // hasMany
$form->select('author.contract'); // belongs to one
$form->multiselect('author.account'); // hasOne / belongsToOne