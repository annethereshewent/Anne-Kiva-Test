<?php
require('vendor/autoload.php');
use \PHPUnit\Framework\TestCase;

class KivaTest extends TestCase {
	//this will be the mock object that the unit test will compare.
	public $loans = [
		[
			'name'        => 'Shifaa',
			'status'      => 'fundraising'
			'loan_amount' => 1025

		], 
		[
			'name'        => 'Alice',
			'status'      => 'fundraising'
			'loan_amount' => 400
		],
		[
			'name'        => "Dung's Group",
			'status'      => 'fundraising'
			'loan_amount' => 2650
		],
		[
			'name'        => 'Kaneez Bb',
			'status'      => 'fundraising'
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