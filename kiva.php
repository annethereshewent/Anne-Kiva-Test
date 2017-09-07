<?php
$loan_total = 0;
//setlocale(LC_MONETARY, 'en_US.UTF-8');
date_default_timezone_set('America/Los_Angeles');

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL 			=> 'http://api.kivaws.org/v1/loans/search.json?status=fundraising&expiring_soon=true&per_page=50&sort_by=expiration&page=1',
	CURLOPT_RETURNTRANSFER  => 1,
]);


$result = json_decode(curl_exec($curl));

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
			margin-left:300px;
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
			margin-left: 300px;
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
		.loading {
			display: none;
			margin-left: 250px;
			margin-top: 20px;

		}
		h3 {
			text-align: center;
			font-family: Cambria;
			font-style: italic;
			font-size: 14px;
		}
	</style>
</head>
<body>
<h1>Loans Retrieved From Kiva Database With at least 24 hours Expiration</h1>
<h3>(Click on a loan's name to see more details on that loan.)</h3>
<table class="loan-table" colspacing="0">
	<tr>
		<th>Name</th>
		<th>Planned Expiration Date</th>
		<th>Loan Amount</th>
	</tr>
	<?php foreach ($result->loans as $loan) {  ?>
		<tr>
			<td><a href="#" onclick="showLoan(<?= $loan->id ?>)"><?= $loan->name ?></a></td>
			<td><?= date('F j Y h:i:s A', strtotime($loan->planned_expiration_date)) ?></td>
			<td>$<?= money_format('%.2n', $loan->loan_amount) ?></td>
		</tr>
		<?php $loan_total += $loan->loan_amount; ?>
	<?php } ?>
	<tr class="total-row">
		<td colspan="2"><b>Total:</b></td>
		<td id="loan-total">$<?= money_format( '%.2n', $loan_total) ?></td>
	</tr>
</table>

<img src="loading.gif" class="loading" width="20" height="20">

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
<script type="text/javascript" src="http://www.datejs.com/build/date.js"></script>
<script>
	var loans;
	var current_page=1;
	var loading = false;

	$(function() {
		//this way we can easily iterate through the loans if need be
		loans = <?= json_encode($result->loans) ?>

		$('.modal').click(function (event) {
		   	if (!$(event.target).closest('.modal-content').length && !$(event.target).is('.modal-content')) {
   				$(".modal").hide();
		   	}     
		});

		$('.close-button').click(function() {
			$(".modal").hide();
		})
		$(window).scroll(function() {
			if ($(window).scrollTop() + $(window).height() == $(document).height()) {
				//need to do an ajax call to retrieve the next set of documents
				if (!loading) {
					loading = true;
					$('.loading').show();
					$.get(
						'http://api.kivaws.org/v1/loans/search.json?status=fundraising&expiring_soon=true&per_page=50&sort_by=expiration&page=' + (current_page+1),
						function(data) {
							console.log(data);

							var total = parseInt($("#loan-total").text().substring(1));
							console.log(total);

							var new_loans = data.loans;


							//this will get the next day so we can compare the results and filter them out
							tomorrow_date = new Date();
							tomorrow_date.setDate(tomorrow_date.getDate() + 1);

							console.log(tomorrow_date);


							//don't do anything if the first result already has a time greater than tomorrow 
							if (new Date(new_loans[0].planned_expiration_date).getTime() > tomorrow_date.getTime()) {
								$('.loading').hide();
								return;
							}


							//remove the total row, it's invalid 
							$(".total-row").remove();

							for (var i = 0; i < new_loans.length; i++) {
								if (new Date(new_loans[i].planned_expiration_date).getTime() <= tomorrow_date.getTime()) {
									//if the expiration date is within one day, then append it to the table
									$(".loan-table").append("<tr><td><a href='#' onclick='showLoan(" + new_loans[i].id + ")'>" + new_loans[i].name + "</a></td><td>" + new Date(new_loans[i].planned_expiration_date).setTimezone('PDT').toString("MMMM d yyyy hh:mm:ss tt") + "</td><td>$" + new_loans[i].loan_amount.toFixed(2) + "</td>");
									total += parseInt(new_loans[i].loan_amount); 
								}
								else {
									//remove this element from new loans since its date is later than 24 hours
									new_loans.splice(i,1);
								}
							}

							//append the new_loans to the loans array so that we can iterate through them
							loans.push.apply(loans, new_loans);

							$(".loan-table").append('<tr class="total-row"><td colspan="2"><b>Total:</b> </td><td id="loan-total">$' + total.toFixed(2)  + '</td></tr>')


							$('.loading').hide();
							loading = false;
							current_page++;
						},
						'json'
					);
				}
			} 
		})

	});

	function showLoan(loan_id) {
		loan = getLoan(loan_id);

		if (loan) {
			$("#name").text(loan.name);
			$("#country").text(loan.location.country);
			$("#activity").text(loan.activity);
			$("#loan_amount").text("$" + loan.loan_amount.toFixed(2));
			$("#expiration_date").text(new Date(loan.planned_expiration_date).setTimezone('PDT').toString('MMMM d yyyy hh:mm:ss tt'));
			$("#funded_amount").text("$" + loan.funded_amount.toFixed(2));

			$("#amount_remaining").text("$" + (loan.loan_amount - loan.funded_amount).toFixed(2));

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
