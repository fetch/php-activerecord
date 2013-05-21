<?php 

class IdentityMapTest extends DatabaseTest
{
	public function test_persistense_of_identity_between_instances()
	{
		$author1 = Author::find(1);
		$author2 = Author::find(1);
		
		$this->assert_equals($author1->name, $author2->name);
		
		$author1->name = 'A New Title';
		
		$this->assert_equals($author1->name, $author2->name);
	}
	
	public function test_persistence_with_models_that_do_not_select_pk()
	{
		$author1 = Author::first(array('select'=>'name','conditions'=>array('name'=>'tito')));
		$author2 = Author::first(array('select'=>'name','conditions'=>array('name'=>'tito')));
		
		$this->assert_equals($author1->name, $author2->name);
		
		$author1->name = 'A New Title';
		
		$this->assert_equals($author1->name, $author2->name);
	}
}