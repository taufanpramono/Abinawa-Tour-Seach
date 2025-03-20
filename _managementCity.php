<?php 

function dataCity() {
	$data = travelDataSource();
	$emptyArr = [];
	if($data && is_array($data)) {
		foreach ($data as $row) {
			$emptyArr[] = [
				'kota_asal'   => $row['kota_awal'],
				'kota_tujuan' => $row['kota_tujuan'],
			];
		}
	}
// 	echo '<pre>';
// 	print_r($emptyArr);
// 	echo '</pre>';
	return $emptyArr;
}
add_shortcode('check_city','dataCity');






 ?>