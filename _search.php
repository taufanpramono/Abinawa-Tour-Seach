<?php 

function searchData() {
    $data = travelDataSource(); // Ambil semua data
	$dataCity = dataCity(); //ambil data kota semua kota
    $filteredData = [];
	$jseat = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]; 

    // Ambil input form
    $tanggal = isset($_GET['tanggal']) ? date("d/m/Y", strtotime($_GET['tanggal'])) : '';
    $jumlah_seat = isset($_GET['jumlah_seat']) ? (int) $_GET['jumlah_seat'] : 0;
    $kota_asal = isset($_GET['kota_asal']) ? strtolower(trim($_GET['kota_asal'])) : '';
    $kota_tujuan = isset($_GET['kota_tujuan']) ? strtolower(trim($_GET['kota_tujuan'])) : '';
	
	//random id
	$idRand = rand(1000, 2000);
	
	
	$routeMap = [];
	foreach ($dataCity as $row) {
    	$asal = $row['kota_asal'];
    	$tujuan = $row['kota_tujuan'];

    	if (!isset($routeMap[$asal])) {
        	$routeMap[$asal] = [];
    	}
    
    	if (!in_array($tujuan, $routeMap[$asal])) {
        	$routeMap[$asal][] = $tujuan;
    	}
	}
// 	echo '<pre>';
// 	print_r($routeMap);
// 	echo '</pre>';

    // Form Pencarian
    ?>

	<!-- FORM PENCARIAN -->
    <form method="GET" id="form_pencarian">
		<div class="row">
			<div class="form-column">
        		<label>Tanggal:</label>
        		<input type="date" id="tanggal" name="tanggal" value="<?php echo isset($_GET['tanggal']) ? $_GET['tanggal'] : ''; ?>"><br>
			</div>
		
			<div class="form-column">
        		<label>Jumlah Penumpang</label>
				<select name="jumlah_seat" id="jumlah_seat">
					<option value="">Pilih Jumlah Penumpang</option>
					<?php foreach($jseat as $sit) : ?>
					<option value="<?= $sit ?>" <?= (isset($_GET['jumlah_seat']) && $_GET['jumlah_seat'] == $sit) ? 'selected' : ''; ?>><?= $sit.' Penumpang' ?></option>
					<?php endforeach ?>
				</select>
        	</div>
		</div>
	   
		<div class="row">
        	<div class="form-column">
				<label>Kota Awal :</label>
				<select name="kota_asal" id="kota_asal">
    				<option value="">Pilih Kota Awal</option>
    				<?php foreach (array_keys($routeMap) as $city) : ?>
        				<option value="<?= $city ?>" <?= (isset($_GET['kota_asal']) && $_GET['kota_asal'] == $city) ? 'selected' : ''; ?>>
            				<?= $city ?>
        				</option>
   				 	<?php endforeach; ?>
				</select>
			</div>

			<div class="form-column">
				<label>Kota Tujuan:</label>
				<select name="kota_tujuan" id="kota_tujuan" disabled>
    				<option value="">Pilih Kota Tujuan</option>
				</select>
			</div>
		</div>
       <button type="submit">Cari</button>
    </form>




	<!-- JAVA SCRIPT DISINI -->
	<script>
	// javascript menangani select dropdown kota
	document.addEventListener("DOMContentLoaded", function() {
    const kotaAsal = document.getElementById("kota_asal");
    const kotaTujuan = document.getElementById("kota_tujuan");
    const tanggal = document.getElementById("tanggal");
    const jumlahSeat = document.getElementById("jumlah_seat");
    const submitButton = document.querySelector('button[type="submit"]');

    // Data kota dari PHP
    const routeMap = <?= json_encode($routeMap, JSON_HEX_TAG); ?>;

    // Fungsi untuk mengisi dropdown kota tujuan
    function updateKotaTujuan(selectedKotaAsal) {
        kotaTujuan.innerHTML = '<option value="">Pilih Kota Tujuan</option>';
        kotaTujuan.disabled = true;

        if (selectedKotaAsal && routeMap[selectedKotaAsal]) {
            routeMap[selectedKotaAsal].forEach(function(city) {
                const option = document.createElement("option");
                option.value = city;
                option.textContent = city;
                kotaTujuan.appendChild(option);
            });

            kotaTujuan.disabled = false;
        }
    }

    // Event saat kota asal berubah
    kotaAsal.addEventListener("change", function() {
        updateKotaTujuan(this.value);
        checkForm();
    });

    // Cek jika semua field telah diisi
    function checkForm() {
        if (tanggal.value && jumlahSeat.value && kotaAsal.value && kotaTujuan.value) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    // Event listener untuk setiap input agar cek form setiap perubahan terjadi
    [tanggal, jumlahSeat, kotaAsal, kotaTujuan].forEach(element => {
        element.addEventListener("change", checkForm);
    });

    // Jika ada data dari GET, set kota tujuan dengan opsi yang sesuai
    const selectedKotaAsal = kotaAsal.value;
    const selectedKotaTujuan = "<?= $_GET['kota_tujuan'] ?? '' ?>";

    if (selectedKotaAsal) {
        updateKotaTujuan(selectedKotaAsal);

        // Pilih kota tujuan yang sesuai jika ada dalam GET
        if (selectedKotaTujuan) {
            Array.from(kotaTujuan.options).forEach(option => {
                if (option.value === selectedKotaTujuan) {
                    option.selected = true;
                }
            });
        }
    }

    checkForm();
});


	document.addEventListener("DOMContentLoaded", function() {
        // Ambil elemen input tanggal
        let inputTanggal = document.getElementById("tanggal");

        // Dapatkan tanggal hari ini dalam format YYYY-MM-DD
        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // Tambahkan nol di depan jika perlu
        let dd = String(today.getDate()).padStart(2, '0');

        let minDate = `${yyyy}-${mm}-${dd}`;

        // Set atribut "min" pada input tanggal
        inputTanggal.setAttribute("min", minDate);
    });
	
		
		document.addEventListener("DOMContentLoaded", function () {
    const accordions = document.querySelectorAll(".custom-accordion");

    accordions.forEach(accordion => {
        const header = accordion.querySelector(".custom-accordion-header");
        const content = accordion.querySelector(".custom-accordion-content");
        const icon = accordion.querySelector(".custom-icon");

        header.addEventListener("click", function () {
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                content.style.paddingTop = "0";
                content.style.paddingBottom = "0";
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                content.style.paddingTop = "10px";
                content.style.paddingBottom = "10px";
            }
            icon.classList.toggle("custom-rotate");
        });
    });
});
		
		
	</script>

    <?php

    // Validasi agar hanya menjalankan pencarian jika ada parameter GET
    if (!empty($_GET)) {
		 // Validasi input: pastikan semua field telah diisi
    	$input_fields = [$tanggal, $jumlah_seat, $kota_asal, $kota_tujuan];
    	$filled_fields = array_filter($input_fields, function($val) {
        	return !empty($val);
    	});

    	// Jika kurang dari 4 field yang diisi, tampilkan pesan error
   		 if (count($filled_fields) < 4) {
        	echo '<div class="nothing-found">';
            echo '<p>Anda Belum Mengisi Semua Kolom Pencarian !</p>';
			echo '</div>';
        	return;
    	}
		
		
		 // Mulai proses pencarian jika semua field telah terisi
        foreach ($data as $item) {
            // Cek kecocokan tanggal
            $match_tanggal = false;
            $tanggal_ditemukan = [];
            if (!empty($tanggal)) {
                foreach ($item['tanggal'] as $t) {
                    if ($t['tanggal'] == $tanggal && strtolower($t['status']) == 'ready') {
                        $match_tanggal = true;
                        $tanggal_ditemukan[] = $t; // Simpan hanya tanggal yang cocok
                        break;
                    }
                }
            } else {
                $match_tanggal = true; // Jika tidak ada filter tanggal, biarkan lolos
                $tanggal_ditemukan = $item['tanggal']; // Pakai semua tanggal yang ada
            }

            // Cek kecocokan jumlah seat
            $match_seat = $jumlah_seat > 0 ? ($item['jumlah_seat'] >= $jumlah_seat) : true;
            $seat_tersedia = $match_seat ? $jumlah_seat : 0;

            // Cek kota asal & tujuan
            $match_kota_asal = !empty($kota_asal) ? (strpos(strtolower($item['kota_awal']), $kota_asal) !== false) : true;
            $match_kota_tujuan = !empty($kota_tujuan) ? (strpos(strtolower($item['kota_tujuan']), $kota_tujuan) !== false) : true;
			
			

            // Jika semua kriteria terpenuhi, tambahkan ke hasil
            if ($match_tanggal && $match_seat && $match_kota_asal && $match_kota_tujuan) {
                $filteredData[] = [
                    "ID"                   => $item['ID'],
                    "kota_awal"            => $item['kota_awal'],
                    "kota_tujuan"          => $item['kota_tujuan'],
                    "waktu_penjemputan"    => $item['waktu_penjemputan'],
                    "waktu_pemberangkatan" => $item['waktu_pemberangkatan'],
                    "harga"                => $item['harga'],
                    "jumlah_seat"          => $item['jumlah_seat'],
                    "fasilitas"            => $item['fasilitas'],
                    "tanggal"              => $tanggal_ditemukan, // Hanya tanggal yang sesuai
					"kendaraan"			   => $item['kendaraan'],
					"kategori"			   => $item['kategori'],
					
                ];
            }
        }

        // Tampilkan hasil pencarian
        if (!empty($filteredData)) {
            foreach ($filteredData as $item) {
				?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
				<div class="column-item">
					<div class="column-atas">
						
						<!-- KOLOM KIRI -->
						<div class="column-one">
							<strong><i class="fa-regular fa-calendar"></i></strong>
                			<?php 
								
								foreach ($item['tanggal'] as $tgl) {
                    				if (!empty($tanggal) && $tgl['tanggal'] == $tanggal) {
                        				echo '<span class="ini-calendar">'.$tgl['tanggal'].'</span>';
										$thetgl = $tgl['tanggal'];
                    				} elseif (empty($tanggal)) {
                        				echo '<span class="ini-calendar">'.$tgl['tanggal'].'</span>';
										$thetgl = $tgl['tanggal'];
                    				}
                				}
				
							?>
							<br>
							<span class="waktu-jemput"><?= $item['waktu_penjemputan'] ?></span>	
							<div class="asal-tujuan">
                				<span class="kota-awal"><i class="fa-regular fa-circle-down"></i> <?= $item['kota_awal']  ?></span>
                				<span class="kota-tujuan"><i class="fa-regular fa-circle-up"></i> <?= $item['kota_tujuan'] ?></span>
								<span class="kategori-travel"><i class="fa-regular fa-circle"></i> <?=  implode(', ', $item['kategori']) ?></span>
								<span class="seat"><i class="fa-solid fa-couch"></i> <?=  $item['jumlah_seat']  ?> Seat Tersedia </span> 
							</div>
						</div>
					
						<!-- KOLOM KANAN -->
						<div class="column-two">
							<span class="special"><i class="fa-regular fa-thumbs-up"></i> Harga Spesial </span>
							<span class="harga">Rp. <?=  number_format($item['harga'], 0, ',', '.') ?></span>
							<div class="fasilitas-item">
								<span class="fasilitas"><i class="fa-regular fa-paper-plane"></i> Fasilitas : </span>
								<ul>
								<?php foreach($item['fasilitas'] as $fasi) : ?>
									<li><?= $fasi ?></li>
								<?php endforeach ?>
								</ul>
							</div>
						</div>
					</div>
          
					
					
				<!-- KOLOM BAWAH -->
				<div class="custom-accordion">
    				<div class="custom-accordion-header">
        				<span>Informasi Kendaraan</span>
        				<i class="fa-solid fa-chevron-down custom-icon"></i>
    				</div>
    				<div class="custom-accordion-content">
        				<div class="column-bawah">
    						<?php foreach ($item['kendaraan'] as $ken) : ?>
        						<div class="kendaraan-info">
            						<strong>Tipe :</strong> <?= $ken['nama_kendaraan'] ?> <br>
            						<strong>Kapasitas :</strong> <?= $ken['jumlah_seat'] ?> Penumpang<br>
        						</div>
        						<div class="kendaraan-gambar">
            						<?php foreach ($ken['gambar'] as $pic) : ?>
                						<img src="<?= $pic ?>">
            						<?php endforeach ?>
        						</div>
    						<?php endforeach ?>
						</div>
    				</div>
					<?php
						$hargaTot = $item['harga'] * $jumlah_seat;
						$hargaTotal = 'Rp. '.number_format($hargaTot, 0, ',', '.');
					?>
					<a href="#" class="waLink button-checkout" 
   						data-id="<?= $idRand ?>" 
   						data-nama="..." 
   						data-tlp="..." 
   						data-tlp-cadangan="..." 
   						data-tanggal="<?= $thetgl ?>" 
   						data-kota-awal="<?= $item['kota_awal'] ?>" 
   						data-kota-tujuan="<?= $item['kota_tujuan'] ?>" 
   						data-waktu="<?= $item['waktu_penjemputan'] ?>" 
   						data-jumlah-seat="<?= $jumlah_seat ?>" 
   						data-harga="<?= $hargaTotal ?>">
   						Pesan
					</a>
					
					<script>
    					// Ambil semua tombol dengan class 'waLink'
    					document.querySelectorAll(".waLink").forEach(button => {
       					 button.addEventListener("click", function(event) {
            					event.preventDefault(); // Mencegah link langsung terbuka

            					// Ambil data dari atribut tombol
            					const phoneNumber = "6285122332377"; // Nomor tujuan
            					const idRand = this.dataset.id;
           					 	const nama = this.dataset.nama;
            					const noTlp = this.dataset.tlp;
            					const noTlpCadangan = this.dataset.tlpCadangan;
            					const tanggal = this.dataset.tanggal;
            					const kotaAwal = this.dataset.kotaAwal;
            					const kotaTujuan = this.dataset.kotaTujuan;
            					const waktuPenjemputan = this.dataset.waktu;
            					const jumlahSeat = this.dataset.jumlahSeat;
            					const hargaTotal = this.dataset.harga;

            					// Format pesan WhatsApp
            					const message = `Hi. Terima Kasih telah memilih Abinawa Travel\n\nPemesanan anda sudah diproses:\n\nOrder ID: #${idRand}\nNama Anda: ${nama}\nNo Tlp Anda: ${noTlp}\nNo Tlp Cadangan: ${noTlpCadangan}\nTanggal Keberangkatan: ${tanggal}\n\n*${kotaAwal} - ${kotaTujuan} / ${waktuPenjemputan}*\nAlamat Jemput: ....\nJam Keberangkatan: ....\nMaskapai / Kapal: ....\nKode Penerbangan / Keberangkatan: ....\n${jumlahSeat} Px / ${hargaTotal}\n\nPastikan no. telepon terdaftar selalu aktif, CS Abinawa Travel akan menghubungi anda maksimal 15 menit untuk konfirmasi melalui nomor 6285122332377`;

            					// Encode pesan agar bisa terbaca dengan benar di URL
            					const encodedMessage = encodeURIComponent(message);

            					// Buat URL WhatsApp
            					const waUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;

            					// Arahkan ke WhatsApp
            					window.location.href = waUrl;
        					});
    					});
					</script>
				</div>	
			</div>
			<?php 
            }
        } else {
			echo '<div class="nothing-found">';
            echo '<p>Perjalanan Tidak Di Temukan !</p>';
			echo '</div>';
        }
    }
}
add_shortcode('data', 'searchData');

 ?>