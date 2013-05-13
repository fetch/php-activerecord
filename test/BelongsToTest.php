<?php

class Writer extends ActiveRecord\Model{
	
	static $table_name = 'authors';
}

class BelongsToTest extends DatabaseTest
{
	public function test_gh291_should_not_require_foreign_key_with_class_name_set(){
		Book::$belongs_to = array(
			array('author', 'class_name' => 'Writer')
		);
		$book = Book::find('first');
		$this->assert_not_null($book->author);
	}
}
?>
