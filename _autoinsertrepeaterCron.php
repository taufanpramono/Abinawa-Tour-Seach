<?php 

function generate_travel_dates($post_id, $is_cron = false) {
    // Pastikan hanya berjalan untuk post type 'travel'
    if (get_post_type($post_id) !== 'travel') {
        return;
    }

    // Cegah autosave atau revisi post agar tidak duplikasi saat disimpan secara manual
    if (!$is_cron) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if (wp_is_post_autosave($post_id)) return;
    }

    // Ambil tahun dan bulan saat ini dari server
    $year = date('Y');
    $month = date('m');

    // Tentukan jumlah hari dalam bulan ini
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Ambil data repeater yang sudah ada
    $existing_dates = get_field('tanggal_keberangkatan', $post_id) ?: [];

    // Simpan tanggal yang sudah ada dalam array untuk pengecekan
    $unique_dates = array_column($existing_dates, 'tanggal_tersedia');

    // **CEK APAKAH JUMLAH HARI BERUBAH (CONTOH: DARI 31 KE 30)**
    if (count($existing_dates) !== $days_in_month) {
        // Reset ulang data
        $updated_dates = [];

        // Generate ulang berdasarkan jumlah hari dalam bulan yang sedang berjalan
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%02d/%02d/%04d', $month, $day, $year); // Format MM/DD/YYYY
            
            // Cek apakah tanggal ini sudah ada sebelumnya untuk mempertahankan status kendaraan
            $status = 'Ready'; // Default status
            foreach ($existing_dates as $entry) {
                if ($entry['tanggal_tersedia'] === $date) {
                    $status = $entry['status_kendaraan']; // Pakai status lama jika ada
                    break;
                }
            }

            // Tambahkan ke array baru
            $updated_dates[] = [
                'tanggal_tersedia' => $date, 
                'status_kendaraan' => $status
            ];
        }

        // Update field dengan daftar tanggal yang telah disesuaikan
        update_field('tanggal_keberangkatan', $updated_dates, $post_id);
    }
}

// Pastikan hanya dijalankan ketika post disimpan di backend
add_action('acf/save_post', 'generate_travel_dates', 20);



// Buat cron job jika belum ada
if (!wp_next_scheduled('update_travel_dates_daily')) {
    wp_schedule_event(strtotime('tomorrow 03:00:00'), 'daily', 'update_travel_dates_daily');
}

// Fungsi yang dijalankan oleh cron
add_action('update_travel_dates_daily', function() {
    $args = [
        'post_type'      => 'travel',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];

    $travel_posts = get_posts($args);

    foreach ($travel_posts as $post_id) {
        generate_travel_dates($post_id, true);
    }
});


// add_action('init', function() {
//     if (isset($_GET['run_cron_travel'])) {
//         do_action('update_travel_dates_daily');
//         echo 'Cron travel dates berhasil dijalankan!';
//         exit;
//     }
// });





?>