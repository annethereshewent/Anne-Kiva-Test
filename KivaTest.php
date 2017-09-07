<?php
require('vendor/autoload.php');
use \PHPUnit\Framework\TestCase;

class KivaTest extends TestCase {
	//this will be the mock object that the unit test will compare.
	public $loans = [
		[
			'name'        => 'Shifaa',
			'loan_amount' => 1025
		], 
		[
			'name' 	  	  => 'Alice',
			'loan_amount' => 400
		],
		[
			'name'        => "Dung's Group",
			'loan_amount' => 2650
		],
		[
			'name' 		  => 'Kaneez Bb',
			'loan_amount' => 475
		]
	];

	public function testCalculateTotal() {
		//the total should add up to 4550, assert that it does
		$total = 0;
		foreach ($this->loans as $loan) {
			$total += $loan['loan_amount'];
		}

		$this->assertEquals($total, 4550);
	}
}








?>