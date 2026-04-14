<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('home redirects to products', function () {
    $this->get(route('home'))->assertRedirect('/products');
});

test('products page returns a successful response', function () {
    $this->get('/products')->assertOk();
});
