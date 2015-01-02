<?php

// HasOne <---> BelongsTo
// HasMany <---> BelongsTo
// MorphTo <---> MorphToMany

// BelongsToMany <---> BelongsToMany


$form->multiselect('author.books'); // hasMany
$form->select('author.contract'); // belongs to one
$form->multiselect('author.account'); // hasOne / belongsToOne

// subforms

$form->form('friends', function ($subform) {
   $subform->text('name');
   $subform->text('surname');
   $subform->form('phones', function ($subform) {
        $subform->text('number');
   });
});
