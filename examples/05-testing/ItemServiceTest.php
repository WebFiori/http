<?php

require_once '../../vendor/autoload.php';
require_once 'ItemService.php';

use WebFiori\Http\Test\ServiceTestCase;

class ItemServiceTest extends ServiceTestCase {

    public function testListItems() {
        $this->get(new ItemService())
            ->assertOk()
            ->assertJson()
            ->assertJsonHas('data')
            ->assertBodyContains('items');
    }

    public function testGetSingleItem() {
        $this->get(new ItemService(), ['id' => 1])
            ->assertOk()
            ->assertJson()
            ->assertBodyContains('Widget');
    }

    public function testCreateItem() {
        $this->post(new ItemService(), ['name' => 'Doohickey', 'price' => 9.99])
            ->assertOk()
            ->assertJson()
            ->assertBodyContains('Doohickey');
    }

    public function testCreateItemMissingParam() {
        $this->post(new ItemService(), ['name' => 'Incomplete'])
            ->assertError()
            ->assertJson()
            ->assertBodyContains('price');
    }
}
