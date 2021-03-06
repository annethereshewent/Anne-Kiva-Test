<?php
require('vendor/autoload.php');
use \PHPUnit\Framework\TestCase;

class KivaTest extends TestCase {
	

	//assume the current date is always September 7 2017 for the test's sake
	public $tomorrow_date = 'September 7 2017 11:59:00 PM';

	//this will be the mock object that the unit test will compare.
	public $loans = [
		[
			'name'                    => 'Shifaa',
			'status'                  => 'fundraising',
			'loan_amount'             => 1025,
			'funded_amount'			  => 400,
			'planned_expiration_date' => 'September 7 2017 01:00:00 AM'

		], 
		[
			'name'                    => 'Alice',
			'status'                  => 'fundraising',
			'loan_amount'             => 400,
			'funded_amount'           => 100,
			'planned_expiration_date' => 'September 7 2017 04:05:00 PM'
		],
		[
			'name'                    => "Dung's Group",
			'status'                  => 'fundraising',
			'loan_amount'             => 2650,
			'funded_amount'           => 350,
			'planned_expiration_date' => 'September 7 2017 06:09:05 AM'
		],
		[
			'name'                    => 'Kaneez Bb',
			'status'                  => 'fundraising',
			'loan_amount'             => 475,
			'funded_amount'           => 200,
			'planned_expiration_date' => 'September 7 2017 11:48:00 PM'
		],
		[
			'name'                    => 'Veronicaa',
			'status'                  => 'funded',
			'loan_amount'             => 600,
			'funded_amount'           => 600,
			'planned_expiration_date' => 'September 8 2017 05:00:00PM'
		]
	];

	//these are the amounts remaining for the respective loans, this will be used in testAmuntRemaining
	public $amount_remaining = [
		625, //amount remaining for first loan
		300, //second loan
		2300, //and so on...
		275
	];

	public function testCalculateTotal() {
		//the total should add up to 4550, assert that it does
		$total = 0;
		foreach ($this->loans as $loan) {
			//only add the ones that have a status of fundraising and an expiration date less than $tomorrow_date
			if ($loan['status'] == 'fundraising' && strtotime($loan['planned_expiration_date']) <= strtotime($this->tomorrow_date)) {
				$total += $loan['loan_amount'];
			}
		}

		$this->assertEquals($total, 4550);
	}

	public function testDate() {

		$filtered_loans = $this->getFilteredLoans();

		foreach ($filtered_loans as $loan) {
			$this->assertLessThan(strtotime($this->tomorrow_date), strtotime($loan['planned_expiration_date']));
		}
	}

	public function testAmountRemaining() {
		$filtered_loans = $this->getFilteredLoans();

		//since the objects are fixed, just make sure that the calculations are correct manually

		foreach ($filtered_loans as $i => $loan) {
			$this->assertEquals($loan['loan_amount'] - $loan['funded_amount'], $this->amount_remaining[$i]);
		}

	}

	public function getFilteredLoans() {
		$filtered_loans = [];

		foreach ($this->loans as $loan) {
			if ($loan['status'] == 'fundraising' && strtotime($loan['planned_expiration_date']) <= strtotime($this->tomorrow_date)) {
				$filtered_loans[] = $loan;
			}
		}

		return $filtered_loans;
	}
}








?>