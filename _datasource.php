<?php 

function travelDataSource() {
    $args = array(
        'post_type'      => 'travel',
        'posts_per_page' => -1, 
		'order'         => 'ASC',
    );

    $query = new WP_Query($args);
    $dataSource = []; // Array kosong untuk menampung semua data

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
			
			$fasilitas_data = get_field('data_dasilitas');
            $fasilitas = [];

            if (!empty($fasilitas_data) && is_array($fasilitas_data)) {
                foreach ($fasilitas_data as $row) {
                    $fasilitas[] = $row['fasilitas_travel']; // Pastikan key sesuai dengan ACF
                }
            }
			
			$tanggal   = get_field('tanggal_keberangkatan');
			$tanggalAr = [];
			
			if(!empty($tanggal) && is_array($tanggal)) {
				foreach($tanggal as $tang) {
					$tanggalAr[] = [
						"tanggal" => $tang['tanggal_tersedia'],
						"status"  => $tang['status_kendaraan'],
					];
				}
			}
			
			
			$kendaraan = get_field('pilih_kendaraan'); // Post Object dari ACF
			$arrkend   = [];

			if ($kendaraan) {
				
				 if (!is_array($kendaraan)) {
        			$kendaraan = [$kendaraan];
    			}
    			
				foreach ($kendaraan as $ken) {
        			$gambar = get_field('gambar_kendaraan', $ken->ID);
        			$gam    = [];

        			if ($gambar && is_array($gambar)) {
            				foreach ($gambar as $gm) {
                				if (!empty($gm['url'])) {
                    			$gam[] = esc_url($gm['url']); // Pastikan URL aman
                			}
            			}
        			}

        			$arrkend[] = [
            			"nama_kendaraan"  => get_the_title($ken->ID),
						"jumlah_seat"     => get_field('jumlah_seat', $ken->ID),
            			"gambar"          => $gam,
        			];
    			}
			}
			
			// Ambil kategori dari taxonomy "kategori-travel"
			$kategori_terms = get_the_terms(get_the_ID(), 'kategori-travel');
			$kategoriNames  = [];

			if (!empty($kategori_terms) && !is_wp_error($kategori_terms)) {
				foreach ($kategori_terms as $term) {
					$kategoriNames[] = $term->name; // Bisa pakai $term->slug jika perlu slug
				}
			}
			
			// Ambil kota awal dari post object
            $kotaAwal = get_field('kota_awal_auto'); // post object kota awal
            $kota_awal = ($kotaAwal && is_object($kotaAwal)) ? get_the_title($kotaAwal->ID) : 'Tidak tersedia';
            
            // Ambil kota tujuan dari post object
            $kotaTujuan = get_field('kota_tujuan_auto'); // post object kota tujuan
            $kota_tujuan = ($kotaTujuan && is_object($kotaTujuan)) ? get_the_title($kotaTujuan->ID) : 'Tidak tersedia';
			
			//waktu Penjemputan 
			$waktuJemput = get_field('waktu_penjemputan');
			if($waktuJemput) {
				$waktuJem = get_field('waktu_penjemputan');
			} else {
				$waktuJem = 'All Time';
			}
			
			//waktu berangkat
			$waktuBerangkat = get_field('waktu_pemberangkatan');
			if($waktuBerangkat) {
				$waktuBer = get_field('waktu_pemberangkatan');
			} else {
				$waktuBer = 'All Time';
			}
			
            
            // Tambahkan data ke array
            $dataSource[] = [
                "ID"        			=> get_the_ID(),
                "kota_awal" 			=> $kota_awal,
				"kota_tujuan"			=> $kota_tujuan,
				"waktu_penjemputan" 	=> $waktuJem,
				"waktu_pemberangkatan"  => $waktuBer,
				"fasilitas"             => $fasilitas, 
				"harga"					=> get_field('harga_travel'),
				"jumlah_seat"			=> get_field('jumlah_seat'),
				"tanggal"				=> $tanggalAr,
				"kendaraan"             => $arrkend,
				"kategori"				=> $kategoriNames,
				
            ];
        }
        wp_reset_postdata();
    }
// 	echo '<pre>';
// 	print_r($dataSource);
// 	echo '</pre>';
    return $dataSource; // Pastikan return ada di luar if
}

?>