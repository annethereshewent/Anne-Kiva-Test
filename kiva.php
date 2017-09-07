<?php
//first we need to do a curl call to the API and parse the json

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL 			=> 'http://api.kivaws.org/v1/loans/search.json?status=fundraising&expiresSoon=true',
	CURLOPT_RETURNTRANSFER  => 1,
]);


$result = json_decode(curl_exec($curl));



$loan_total = 0;

?>
<head>
	<style>
		body {
			background: #cc99ff;
		}
		h1 {
			font-family: Cambria;
			color: #800000;
		}
	</style>
</head>
<body>
<h1>Loans Expiring in the Next 24 hours</h1>
<table>
	<tr>
		<th>Name</th>
		<th>Loan Amount</th>
		<th>Funded Amount</th>
		<th>Planned Expiration Date</th>
	</tr>
<?php foreach ($result->loans as $loan) {  ?>
	<tr>
		<td><?= $loan->name ?></td>
		<td><?= $loan->loan_amount ?></td>
		<td><?= $loan->funded_amount ?></td>
		<td><?= date('M j Y h:i:s a', strtotime($loan->planned_expiration_date)) ?></td>
	</tr>
	<?php $loan_total += $loan->loan_amount; ?>
<?php } ?>
</table>



</body>
