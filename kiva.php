<?php
//first we need to do a curl call to the API and parse the json

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL 			=> 'http://api.kivaws.org/v1/loans/search.json?status=fundraising&expiringSoon=true',
	CURLOPT_RETURNTRANSFER  => 1,
]);


$result = json_decode(curl_exec($curl));



$loan_total = 0;
setlocale(LC_MONETARY, 'en_US.UTF-8');

?>
<head>
	<style>
		body {
			background: #cc99ff;
		}
		h1 {
			font-family: Cambria;
			color: #800000;
			text-align: center;
		}
		.loan-table {
			margin-left:100px;
			border: 1px solid black;
			border-collapse: collapse;
		}
		th, td {
			border: 1px solid black;
		}
		th {
			background: gold;
		}
		td {
			padding-left: 20px;
			padding-right: 20px;
			color: #4d0000;
		}
		td a {
			text-decoration: none;
			color: #4d0000;
		}
		.modal {
			display: none;
			z-index: 1;
			position: fixed;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background-color: rgb(0,0,0); /* Fallback color */
    		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
		}
		.modal-content {
			margin-left: 100px;
			margin-top: 60px;
			background: #cc99ff;
			border-radius: 2px;
			width: 500px;
			height: 500px;
			padding: 0 0 0 20px;
			border: 1px solid black;
		}
		label {
			font-weight: bold;
		}
		.close-button {
			color: red;
			text-align:right;
			font-size: 20px;
			cursor: hand;
		}
	</style>
</head>
<body>
<h1>Loans Retrieved From Kiva Database</h1>
<table class="loan-table" colspacing="0">
	<tr>
		<th>Name</th>
		<th>Planned Expiration Date</th>
		<th>Loan Amount</th>
	</tr>
<?php foreach ($result->loans as $loan) {  ?>
	<tr>
		<td><a href="#" onclick="showLoan(<?= $loan->id ?>)"><?= $loan->name ?></a></td>
		<td><?= date('M j Y h:i:s a', strtotime($loan->planned_expiration_date)) ?></td>
		<td><?= money_format('%.2n', $loan->loan_amount) ?></td>
	</tr>
	<?php $loan_total += $loan->loan_amount; ?>
<?php } ?>
<tr>
	<td colspan="2"><b>Total:</b></td>
	<td><?= money_format( '%.2n', $loan_total) ?></td>

</table>


<div class="modal">
	<div class="modal-content">
		<p class="close-button">x</p>
		<h1>Loan Info for <span id="name"></span></h1>
		<p><label>Country:</label> <span id="country"></span></p>
		<p><label>Activity:</label> <span id="activity"></span></p>
		<p><label>Loan Amount:</label> <span id="loan_amount"></span></p>
		<p><label>Planned Expiration Date:</label> <span id="expiration_date"></span></p>
		<p><label>Funded Amount:</label> <span id="funded_amount"></span></p>
		<p><label>Amount Remaining:</label> <span id="amount_remaining"></span></p>
	</div>
</div>

<script src="jquery-3.2.1.min.js"></script>
<script>
	var loans;
	$(function() {
		//this way we can easily iterate through the loans if need be
		loans = <?= json_encode($result->loans) ?>

		$('.modal').click(function (event) {
		   if(!$(event.target).closest('.modal-content').length && !$(event.target).is('.modal-content')) {
		     $(".modal").hide();
		   }     
		});

		$('.close-button').click(function() {
			$(".modal").hide();
		})
	});

	function showLoan(loan_id) {
		loan = getLoan(loan_id);

		if (loan) {
			$("#name").text(loan.name);
			$("#country").text(loan.location.country);
			$("#activity").text(loan.activity);
			$("#loan_amount").text("$" + loan.loan_amount.toFixed(2));
			$("#expiration_date").text(loan.planned_expiration_date);
			$("#funded_amount").text("$" + loan.funded_amount.toFixed(2));

			$("#amount_remaining").text("$" + (loan.loan_amount - loan.funded_amount).toFixed(2	));

			$(".modal").show();
		}
		else {
			alert("Loan not found");
		}
	}

	function getLoan(loan_id) {
		for (var i = 0; i < loans.length; i++) {
			if (loans[i].id == loan_id) {
				return loans[i];
			}
		}

		return false;
	}
</script>
</body>
