<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Models\Vote;
use App\Models\Comment;
use App\Models\CommentTop;
use App\Models\Endorsement;
use App\Models\LabRoom;
use App\Models\LabSource;
use App\Models\LabDiscussion;
use App\Models\PolicyBrief;
use App\Models\PolicyEndorsement;
use App\Models\HoaxClaim;
use App\Models\HoaxVerdict;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create main demo user (login: demo@civic.id / password)
        $ahmad = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'demo@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Ilmu Politik',
            'universitas' => 'UI',
            'bio' => 'Mahasiswa semester 7, fokus pada literasi digital dan kebijakan publik.',
            'role' => 'agent',
            'is_profile_complete' => true,
        ]);

        // Create additional users
        $putri = User::create([
            'name' => 'Putri Amalina',
            'email' => 'putri@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Dosen Komunikasi',
            'universitas' => 'UGM',
            'bio' => 'Dosen dan peneliti bidang komunikasi digital.',
            'role' => 'mentor',
            'is_profile_complete' => true,
        ]);

        $rizky = User::create([
            'name' => 'Rizky Pratama',
            'email' => 'rizky@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Mahasiswa Hukum',
            'universitas' => 'UI',
            'bio' => 'Aktif dalam riset kebijakan kesehatan.',
            'role' => 'agent',
            'is_profile_complete' => true,
        ]);

        $sari = User::create([
            'name' => 'Sari Dewi',
            'email' => 'sari@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Ilmu Komputer',
            'universitas' => 'UGM',
            'role' => 'mahasiswa',
            'is_profile_complete' => true,
        ]);

        // Create approved posts (publicly visible)
        $post1 = Post::create([
            'user_id' => $putri->id,
            'category' => 'fact-check',
            'body' => 'Analisis mahasiswa tentang "Kenaikan UKT 50%" sudah saya review. Terima kasih atas partisipasi aktif rekan-rekan mahasiswa. Informasi kenaikan UKT 50% tidak benar. Keputusan rektorat hanya penyesuaian biaya praktikum 5% untuk Fakultas Kedokteran.',
            'citations' => [
                ['text' => 'Surat Keputusan Rektor No. 123/2026 tentang Penyesuaian Biaya Praktikum', 'url' => 'https://ui.ac.id/sk-rektor/123-2026'],
                ['text' => 'Laporan Komisi X DPR RI tentang Kebijakan UKT 2026', 'url' => 'https://dpr.go.id/laporan/komisi-x/ukt-2026'],
            ],
            'status' => 'approved',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subHours(5),
        ]);

        $post2 = Post::create([
            'user_id' => $rizky->id,
            'category' => 'artikel',
            'body' => 'Bedah Pasal RUU Kesehatan vs Narasi yang Viral di WA. Setelah riset kolaboratif di L.A.B Room, kami menemukan beberapa poin penting dalam Analisis Pasal 154 Ayat 3 RUU Kesehatan.',
            'citations' => [
                ['text' => 'RUU Kesehatan Pasal 154 Ayat 3 - DPR RI', 'url' => 'https://dpr.go.id/ruu-kesehatan/pasal-154'],
            ],
            'status' => 'approved',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subHours(3),
        ]);

        $post3 = Post::create([
            'user_id' => $sari->id,
            'category' => 'fact-check',
            'body' => 'Benarkah vaksin COVID-19 menyebabkan efek samping jangka panjang? Setelah membaca beberapa jurnal ilmiah, berikut temuan kami tentang klaim yang beredar di media sosial.',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subHours(2),
        ]);

        $post4 = Post::create([
            'user_id' => $ahmad->id,
            'category' => 'artikel',
            'body' => 'Debat Mahasiswa Nasional 2026 akan diselenggarakan bulan depan. Siapa yang siap berpartisipasi? Mari kita diskusikan persiapannya.',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subHours(1),
        ]);

        // Create a pending post (waiting for review)
        $postPending = Post::create([
            'user_id' => $sari->id,
            'category' => 'fact-check',
            'body' => 'Apakah benar pemerintah akan menghapus subsidi BBM? Ini perlu dicek faktanya karena banyak beredar di media sosial.',
            'status' => 'pending',
        ]);

        // Create a rejected post (with warning)
        $postRejected = Post::create([
            'user_id' => $sari->id,
            'category' => 'artikel',
            'body' => 'Postingan ini berisi opini tanpa sumber yang jelas tentang kebijakan pendidikan.',
            'status' => 'rejected',
            'rejection_reason' => 'Postingan tidak menyertakan sumber atau referensi yang valid. Silakan tambahkan referensi dan kirim ulang.',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subHours(6),
        ]);

        // Add votes to fact-check posts
        Vote::create(['user_id' => $ahmad->id, 'post_id' => $post1->id, 'vote' => 'hoaks']);
        Vote::create(['user_id' => $rizky->id, 'post_id' => $post1->id, 'vote' => 'hoaks']);
        Vote::create(['user_id' => $sari->id, 'post_id' => $post1->id, 'vote' => 'hoaks']);

        Vote::create(['user_id' => $ahmad->id, 'post_id' => $post3->id, 'vote' => 'hoaks']);
        Vote::create(['user_id' => $putri->id, 'post_id' => $post3->id, 'vote' => 'fakta']);
        Vote::create(['user_id' => $rizky->id, 'post_id' => $post3->id, 'vote' => 'fakta']);

        // Add endorsements to artikel posts
        Endorsement::create(['user_id' => $ahmad->id, 'post_id' => $post2->id]);
        Endorsement::create(['user_id' => $sari->id, 'post_id' => $post2->id]);
        Endorsement::create(['user_id' => $putri->id, 'post_id' => $post4->id]);
        Endorsement::create(['user_id' => $rizky->id, 'post_id' => $post4->id]);
        Endorsement::create(['user_id' => $sari->id, 'post_id' => $post4->id]);

        // Add comments with top votes
        $c1 = Comment::create([
            'user_id' => $ahmad->id,
            'post_id' => $post1->id,
            'body' => 'Terima kasih atas analisis yang detail, Bu Putri. Data SK Rektor yang dilampirkan sangat membantu klarifikasi.',
            'created_at' => now()->subHours(4),
        ]);
        $c2 = Comment::create([
            'user_id' => $sari->id,
            'post_id' => $post1->id,
            'body' => 'Saya sudah cek ke bagian keuangan kampus, memang benar hanya penyesuaian praktikum 5% untuk FK saja.',
            'created_at' => now()->subHours(3),
        ]);
        $c3 = Comment::create([
            'user_id' => $rizky->id,
            'post_id' => $post1->id,
            'body' => 'Apakah ada rencana penyesuaian untuk fakultas lain juga?',
            'created_at' => now()->subHours(2),
        ]);

        // Replies to c1
        Comment::create([
            'user_id' => $putri->id,
            'post_id' => $post1->id,
            'parent_id' => $c1->id,
            'body' => 'Sama-sama, Ahmad. Penting untuk selalu cross-check dengan dokumen resmi.',
            'created_at' => now()->subHours(3)->subMinutes(30),
        ]);

        // Top votes on comments (c2 gets most tops, will be first)
        CommentTop::create(['user_id' => $ahmad->id, 'comment_id' => $c2->id]);
        CommentTop::create(['user_id' => $putri->id, 'comment_id' => $c2->id]);
        CommentTop::create(['user_id' => $rizky->id, 'comment_id' => $c2->id]);
        CommentTop::create(['user_id' => $ahmad->id, 'comment_id' => $c1->id]);

        // Comments on post2 (artikel)
        $c4 = Comment::create([
            'user_id' => $putri->id,
            'post_id' => $post2->id,
            'body' => 'Analisis yang sangat komprehensif. Bagian tentang Pasal 154 Ayat 3 sangat relevan dengan situasi saat ini.',
            'created_at' => now()->subHours(2),
        ]);
        Comment::create([
            'user_id' => $ahmad->id,
            'post_id' => $post2->id,
            'body' => 'Setuju dengan analisis ini. Perlu sosialisasi lebih luas ke masyarakat.',
            'created_at' => now()->subHours(1),
        ]);
        CommentTop::create(['user_id' => $sari->id, 'comment_id' => $c4->id]);
        CommentTop::create(['user_id' => $rizky->id, 'comment_id' => $c4->id]);

        // Add a sample report
        Report::create([
            'user_id' => $putri->id,
            'post_id' => $post3->id,
            'reason' => 'hoaks',
            'description' => 'Informasi ini perlu verifikasi lebih lanjut dari sumber yang kredibel.',
            'status' => 'pending',
        ]);

        // Add notifications for the rejected post author (sari)
        Notification::create([
            'user_id' => $sari->id,
            'post_id' => $postRejected->id,
            'type' => 'post_rejected',
            'title' => 'Postingan Ditolak',
            'message' => 'Postingan Anda telah ditolak: Postingan tidak menyertakan sumber atau referensi yang valid.',
            'is_read' => false,
        ]);

        // Notification for approved post
        Notification::create([
            'user_id' => $sari->id,
            'post_id' => $post3->id,
            'type' => 'post_approved',
            'title' => 'Postingan Disetujui',
            'message' => 'Postingan fact-check Anda tentang vaksin COVID-19 telah disetujui dan dipublikasikan.',
            'is_read' => true,
        ]);

        // ===== L.A.B ROOMS =====

        // Room 1: Completed room with all phases done
        $room1 = LabRoom::create([
            'user_id' => $ahmad->id,
            'title' => 'Analisis Misinformasi Pemilu 2026',
            'description' => 'Menganalisis pola penyebaran hoaks terkait pemilu dan dampaknya terhadap persepsi publik.',
            'category' => 'politik',
            'phase' => 'output',
            'status' => 'completed',
            'target' => 'nasional',
            'max_participants' => 6,
        ]);
        $room1->participants()->attach([$ahmad->id, $sari->id, $rizky->id]);

        // Sources for room 1
        LabSource::create([
            'lab_room_id' => $room1->id,
            'user_id' => $ahmad->id,
            'url' => 'https://kominfo.go.id/hoaks-pemilu-2026',
            'title' => 'Laporan Hoaks Pemilu 2026 - Kominfo',
            'summary' => 'Data resmi Kominfo tentang sebaran hoaks selama masa kampanye.',
            'is_verified' => true,
        ]);
        LabSource::create([
            'lab_room_id' => $room1->id,
            'user_id' => $sari->id,
            'url' => 'https://mafindo.or.id/fact-check/pemilu',
            'title' => 'Analisis Fact-Check Mafindo',
            'summary' => 'Rangkuman verifikasi fakta terkait klaim-klaim viral selama pemilu.',
            'is_verified' => true,
        ]);

        // Discussions for room 1
        $disc1 = LabDiscussion::create([
            'lab_room_id' => $room1->id,
            'user_id' => $rizky->id,
            'claim' => 'Sebagian besar hoaks pemilu berasal dari akun anonim di platform X dan WhatsApp.',
            'evidence' => 'Berdasarkan data Kominfo, 72% konten hoaks pemilu teridentifikasi dari akun tanpa identitas jelas.',
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room1->id,
            'user_id' => $sari->id,
            'claim' => 'Setuju, dan perlu ditambahkan bahwa bot juga berperan besar dalam amplifikasi.',
            'parent_id' => $disc1->id,
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room1->id,
            'user_id' => $ahmad->id,
            'claim' => 'Regulasi platform digital perlu diperketat untuk mengurangi akun anonim.',
            'evidence' => 'UU ITE revisi pasal 27 memberikan dasar hukum untuk identifikasi akun, namun implementasi masih lemah.',
        ]);

        // Room 2: Active room in analisis phase
        $room2 = LabRoom::create([
            'user_id' => $sari->id,
            'title' => 'Dampak AI terhadap Ketenagakerjaan',
            'description' => 'Diskusi tentang bagaimana AI mengubah lanskap pekerjaan di Indonesia dan rekomendasi kebijakan.',
            'category' => 'teknologi',
            'phase' => 'analisis',
            'status' => 'in_progress',
            'target' => 'nasional',
        ]);
        $room2->participants()->attach([$sari->id, $ahmad->id, $putri->id]);

        LabSource::create([
            'lab_room_id' => $room2->id,
            'user_id' => $sari->id,
            'url' => 'https://weforum.org/future-of-jobs-2026',
            'title' => 'World Economic Forum: Future of Jobs Report 2026',
            'summary' => 'Laporan global tentang perubahan lanskap pekerjaan akibat AI dan otomasi.',
            'is_verified' => true,
        ]);
        LabSource::create([
            'lab_room_id' => $room2->id,
            'user_id' => $putri->id,
            'url' => 'https://bps.go.id/tenaga-kerja-digital',
            'title' => 'BPS: Statistik Tenaga Kerja Sektor Digital',
            'summary' => 'Data statistik ketenagakerjaan sektor digital Indonesia.',
        ]);

        LabDiscussion::create([
            'lab_room_id' => $room2->id,
            'user_id' => $putri->id,
            'claim' => 'AI tidak menghilangkan pekerjaan secara masif, melainkan mengubah skill yang dibutuhkan.',
            'evidence' => 'Studi McKinsey menunjukkan 60% pekerjaan akan berubah, bukan hilang. Perlu program re-skilling.',
        ]);

        // Room 3: Open room waiting for participants
        $room3 = LabRoom::create([
            'user_id' => $rizky->id,
            'title' => 'Regulasi Perlindungan Data Mahasiswa',
            'description' => 'Menyusun rekomendasi kebijakan perlindungan data pribadi mahasiswa di lingkungan kampus.',
            'category' => 'hukum',
            'phase' => 'literasi',
            'status' => 'open',
            'target' => 'kampus',
            'is_private' => false,
        ]);
        $room3->participants()->attach([$rizky->id]);

        // ===== POLICY BRIEFS =====

        // Brief 1: Approved (from completed room 1)
        $brief1 = PolicyBrief::create([
            'user_id' => $ahmad->id,
            'lab_room_id' => $room1->id,
            'title' => 'Strategi Penanganan Misinformasi Pemilu 2026',
            'summary' => 'Naskah kebijakan ini merangkum temuan kolaboratif tentang pola misinformasi pemilu dan merekomendasikan strategi multi-stakeholder untuk penanganannya.',
            'problem' => 'Misinformasi selama masa pemilu 2026 meningkat 300% dibanding periode non-pemilu. Sebanyak 72% konten hoaks berasal dari akun anonim, dan 45% masyarakat terpapar minimal satu hoaks per hari. Hal ini mengancam integritas demokrasi dan kepercayaan publik terhadap proses pemilu.',
            'analysis' => 'Berdasarkan data Kominfo dan Mafindo, terdapat tiga pola utama misinformasi pemilu: (1) Manipulasi data survei dengan menyebarkan hasil survei palsu, (2) Disinformasi tentang kandidat menggunakan deepfake dan narasi palsu, (3) Hoaks tentang proses pemungutan suara untuk menurunkan partisipasi. Platform X dan WhatsApp menjadi media utama penyebaran.',
            'recommendation' => '1. Percepat implementasi identifikasi akun di platform digital sesuai UU ITE revisi. 2. Bentuk satgas fact-checking kampus yang melibatkan mahasiswa terlatih. 3. Integrasikan literasi digital dalam kurikulum wajib perguruan tinggi. 4. Kembangkan dashboard real-time monitoring hoaks pemilu.',
            'template_type' => 'standar',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subDays(2),
        ]);

        // Endorsements for brief 1
        PolicyEndorsement::create(['policy_brief_id' => $brief1->id, 'user_id' => $sari->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief1->id, 'user_id' => $putri->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief1->id, 'user_id' => $rizky->id]);

        // Brief 2: Draft for Room 2
        $brief2 = PolicyBrief::create([
            'user_id' => $sari->id,
            'lab_room_id' => $room2->id,
            'title' => 'Kebijakan Re-skilling Tenaga Kerja di Era AI',
            'summary' => 'Rekomendasi kebijakan untuk program re-skilling digital bagi tenaga kerja Indonesia menghadapi dampak AI.',
            'problem' => 'Indonesia menghadapi tantangan besar dalam adaptasi tenaga kerja terhadap AI. Diperkirakan 23 juta pekerja perlu re-skilling dalam 5 tahun ke depan.',
            'analysis' => 'Data BPS menunjukkan hanya 12% pekerja Indonesia memiliki keterampilan digital memadai. Program pelatihan yang ada belum terkoordinasi dan tidak menjangkau daerah.',
            'recommendation' => "1. Buat platform pelatihan digital nasional gratis.\n2. Insentif pajak untuk perusahaan yang menjalankan program re-skilling.\n3. Integrasi kompetensi AI dasar dalam kurikulum SMK.",
            'template_type' => 'data-driven',
            'status' => 'draft',
        ]);

        // Brief 3: Draft
        PolicyBrief::create([
            'user_id' => $rizky->id,
            'title' => 'Perlindungan Data Pribadi Mahasiswa',
            'summary' => 'Draft awal rekomendasi kebijakan perlindungan data mahasiswa...',
            'problem' => 'Banyak kampus mengumpulkan data mahasiswa tanpa consent yang jelas.',
            'analysis' => 'Analisis sedang disusun...',
            'recommendation' => 'Rekomendasi akan ditambahkan setelah diskusi LAB Room.',
            'template_type' => 'standar',
            'status' => 'draft',
        ]);

        // ===== ADDITIONAL USERS FOR COMPLETED ROOM =====

        $dina = User::create([
            'name' => 'Dina Maharani',
            'email' => 'dina@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Kesehatan Masyarakat',
            'universitas' => 'Unair',
            'bio' => 'Peneliti muda bidang kesehatan mental dan kebijakan kampus.',
            'role' => 'mahasiswa',
            'is_profile_complete' => true,
        ]);

        $bayu = User::create([
            'name' => 'Bayu Setiawan',
            'email' => 'bayu@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Psikologi',
            'universitas' => 'Unpad',
            'bio' => 'Aktivis kesehatan mental kampus, co-founder UKM Peduli Jiwa.',
            'role' => 'mahasiswa',
            'is_profile_complete' => true,
        ]);

        $mega = User::create([
            'name' => 'Mega Kurniawati',
            'email' => 'mega@civic.id',
            'password' => Hash::make('password'),
            'jurusan' => 'Dosen Psikologi Klinis',
            'universitas' => 'UGM',
            'bio' => 'Dosen dan konselor, fokus pada wellbeing mahasiswa.',
            'role' => 'mentor',
            'is_profile_complete' => true,
        ]);

        // ===== COMPLETED L.A.B ROOM: Kesehatan Mental Mahasiswa =====

        $room4 = LabRoom::create([
            'user_id' => $dina->id,
            'title' => 'Krisis Kesehatan Mental Mahasiswa Pasca-Pandemi',
            'description' => 'Riset kolaboratif tentang dampak pandemi terhadap kesehatan mental mahasiswa Indonesia dan rekomendasi kebijakan kampus yang komprehensif.',
            'category' => 'sosial',
            'phase' => 'output',
            'status' => 'completed',
            'target' => 'Policy Brief',
            'max_participants' => 8,
            'created_at' => now()->subDays(21),
            'updated_at' => now()->subDays(3),
        ]);

        // 6 collaborators: dina (host), bayu, mega, putri, sari, rizky
        $room4->participants()->attach([
            $dina->id => ['joined_at' => now()->subDays(21)],
            $bayu->id => ['joined_at' => now()->subDays(20)],
            $mega->id => ['joined_at' => now()->subDays(19)],
            $putri->id => ['joined_at' => now()->subDays(18)],
            $sari->id => ['joined_at' => now()->subDays(17)],
            $rizky->id => ['joined_at' => now()->subDays(16)],
        ]);

        // Sources for room 4
        LabSource::create([
            'lab_room_id' => $room4->id,
            'user_id' => $dina->id,
            'url' => 'https://kemkes.go.id/survei-kesehatan-mental-mahasiswa-2025',
            'title' => 'Survei Nasional Kesehatan Mental Mahasiswa 2025 - Kemenkes',
            'summary' => 'Data prevalensi gangguan kecemasan dan depresi pada mahasiswa Indonesia pasca-pandemi. 38% mahasiswa mengalami gejala kecemasan sedang-berat.',
            'is_verified' => true,
            'created_at' => now()->subDays(18),
        ]);
        LabSource::create([
            'lab_room_id' => $room4->id,
            'user_id' => $mega->id,
            'url' => 'https://who.int/publications/mental-health-young-people-2025',
            'title' => 'WHO: Mental Health Among Young People - Global Report 2025',
            'summary' => 'Laporan WHO tentang tren global kesehatan mental usia muda, termasuk rekomendasi kebijakan institusi pendidikan.',
            'is_verified' => true,
            'created_at' => now()->subDays(17),
        ]);
        LabSource::create([
            'lab_room_id' => $room4->id,
            'user_id' => $bayu->id,
            'url' => 'https://into-the-light.id/riset-bunuh-diri-mahasiswa',
            'title' => 'Into The Light: Laporan Kasus Bunuh Diri di Lingkungan Kampus',
            'summary' => 'Dokumentasi dan analisis kasus bunuh diri mahasiswa 2020-2025, mengidentifikasi faktor risiko utama.',
            'is_verified' => true,
            'created_at' => now()->subDays(16),
        ]);
        LabSource::create([
            'lab_room_id' => $room4->id,
            'user_id' => $sari->id,
            'url' => 'https://dikti.kemdikbud.go.id/kebijakan-konseling-kampus',
            'title' => 'Dikti: Pedoman Layanan Konseling Perguruan Tinggi',
            'summary' => 'Pedoman resmi Dikti tentang standar minimum layanan konseling dan kesehatan mental di kampus.',
            'is_verified' => true,
            'created_at' => now()->subDays(15),
        ]);
        LabSource::create([
            'lab_room_id' => $room4->id,
            'user_id' => $putri->id,
            'url' => 'https://jurnal.ugm.ac.id/psikologi/stigma-mental-health',
            'title' => 'Stigma Kesehatan Mental di Kalangan Mahasiswa Indonesia',
            'summary' => 'Penelitian kualitatif tentang stigma yang menghalangi mahasiswa mencari bantuan profesional. 67% merasa malu untuk konseling.',
            'is_verified' => true,
            'created_at' => now()->subDays(14),
        ]);

        // Discussions for room 4
        $disc4a = LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $dina->id,
            'claim' => 'Data Kemenkes menunjukkan 38% mahasiswa mengalami kecemasan sedang-berat, namun hanya 8% yang mengakses layanan konseling kampus. Ada gap besar antara kebutuhan dan utilisasi layanan.',
            'evidence' => 'Survei Nasional Kesehatan Mental Mahasiswa 2025 mencatat dari 15.000 responden di 50 PTN/PTS, rasio konselor:mahasiswa rata-rata 1:5.000, jauh dari standar ideal 1:1.000.',
            'created_at' => now()->subDays(15),
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $mega->id,
            'claim' => 'Stigma jadi penghalang utama. Dari pengalaman saya sebagai konselor, banyak mahasiswa baru datang saat sudah masuk fase krisis. Perlu pendekatan preventif.',
            'evidence' => 'Riset UGM (2024) menunjukkan 67% mahasiswa merasa "malu" dan 42% takut dianggap "lemah" jika datang ke pusat konseling. Peer-counseling dan screening rutin bisa jadi solusi.',
            'parent_id' => $disc4a->id,
            'created_at' => now()->subDays(14),
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $bayu->id,
            'claim' => 'Tekanan akademik dan finansial adalah dua faktor risiko tertinggi. Sistem SKS dan deadline bersamaan memperburuk kondisi mahasiswa yang sudah rentan.',
            'evidence' => 'Data Into The Light mencatat 78% kasus burnout akademik terjadi di semester 3-5. Beban SKS rata-rata 22 sks/semester melebihi rekomendasi WHO untuk usia 18-24.',
            'created_at' => now()->subDays(13),
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $putri->id,
            'claim' => 'Dari perspektif komunikasi, kampus perlu mengubah narasi tentang kesehatan mental. Bahasa yang digunakan dalam sosialisasi masih terlalu klinis dan menakutkan.',
            'evidence' => 'Analisis konten media sosial kampus menunjukkan engagement untuk konten "self-care tips" 5x lebih tinggi dibanding pengumuman jadwal konseling formal.',
            'created_at' => now()->subDays(12),
        ]);
        $disc4e = LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $sari->id,
            'claim' => 'Solusi teknologi bisa membantu. Banyak mahasiswa lebih nyaman curhat via chatbot atau platform anonim daripada tatap muka dengan konselor.',
            'evidence' => 'Platform Riliv dan Sejiwa mencatat 340% peningkatan pengguna mahasiswa sejak 2023. Integrasi platform digital dengan sistem konseling kampus bisa meningkatkan jangkauan.',
            'created_at' => now()->subDays(11),
        ]);
        LabDiscussion::create([
            'lab_room_id' => $room4->id,
            'user_id' => $rizky->id,
            'claim' => 'Aspek hukumnya penting: UU Kesehatan No. 17/2023 Pasal 89 sudah mewajibkan fasilitas kesehatan mental di institusi pendidikan, tapi belum ada peraturan turunan yang mengatur implementasi di kampus.',
            'evidence' => 'Review regulasi menunjukkan hanya 12 dari 200+ PTN yang sudah memiliki Peraturan Rektor khusus tentang layanan kesehatan mental. Perlu dorongan kebijakan top-down.',
            'parent_id' => $disc4e->id,
            'created_at' => now()->subDays(10),
        ]);

        // ===== POLICY BRIEF OUTPUT FROM ROOM 4 =====

        $brief4 = PolicyBrief::create([
            'user_id' => $dina->id,
            'lab_room_id' => $room4->id,
            'title' => 'Kebijakan Komprehensif Kesehatan Mental Mahasiswa: Dari Stigma Menuju Sistem Dukungan Berkelanjutan',
            'summary' => 'Naskah kebijakan ini merupakan hasil riset kolaboratif 6 mahasiswa dan dosen lintas universitas di L.A.B Room. Berdasarkan data Kemenkes, WHO, dan studi lapangan, kami merekomendasikan kerangka kebijakan 4 pilar untuk mengatasi krisis kesehatan mental di lingkungan kampus Indonesia pasca-pandemi.',
            'problem' => "Krisis kesehatan mental mahasiswa Indonesia telah mencapai titik kritis pasca-pandemi. Data Survei Nasional Kesehatan Mental Mahasiswa 2025 dari Kemenkes mencatat 38% dari 15.000 responden di 50 PTN/PTS mengalami gejala kecemasan sedang-berat, sementara 24% menunjukkan gejala depresi. Angka ini meningkat 15% dibanding survei serupa tahun 2022.\n\nNamun, hanya 8% mahasiswa yang mengakses layanan konseling kampus. Gap utilisasi ini disebabkan oleh tiga faktor utama: (1) stigma sosial — 67% mahasiswa merasa malu untuk mencari bantuan profesional, (2) keterbatasan infrastruktur — rasio konselor:mahasiswa rata-rata 1:5.000, jauh dari standar ideal 1:1.000, dan (3) kurangnya kesadaran — 54% mahasiswa tidak mengetahui layanan konseling yang tersedia di kampusnya.\n\nLaporan Into The Light mendokumentasikan peningkatan kasus bunuh diri di lingkungan kampus sebesar 28% dalam 3 tahun terakhir, dengan tekanan akademik (78%) dan masalah finansial (62%) sebagai faktor risiko utama.",
            'analysis' => "Analisis multi-sumber mengidentifikasi empat dimensi permasalahan:\n\n**1. Dimensi Struktural:** Dari 200+ PTN di Indonesia, hanya 12 yang memiliki Peraturan Rektor khusus tentang layanan kesehatan mental. UU Kesehatan No. 17/2023 Pasal 89 mewajibkan fasilitas kesehatan mental di institusi pendidikan, namun belum ada peraturan turunan yang mengatur implementasi spesifik di kampus.\n\n**2. Dimensi Kultural:** Stigma tetap menjadi penghalang utama. Riset kualitatif UGM (2024) menemukan 42% mahasiswa takut dianggap \"lemah\" jika berkonsultasi ke psikolog. Narasi kampus tentang kesehatan mental masih terlalu klinis — analisis media sosial menunjukkan engagement konten \"self-care tips\" 5x lebih tinggi dari pengumuman konseling formal.\n\n**3. Dimensi Akademik:** Beban SKS rata-rata 22 sks/semester melebihi rekomendasi beban kognitif optimal. Sistem evaluasi yang berfokus pada IPK menciptakan tekanan kompetitif tidak sehat. 78% kasus burnout akademik terjadi di semester 3-5.\n\n**4. Dimensi Digital:** Platform digital (Riliv, Sejiwa) mencatat peningkatan 340% pengguna mahasiswa sejak 2023, menunjukkan preferensi generasi muda untuk akses layanan via teknologi. Namun integrasi platform digital dengan sistem konseling kampus masih sangat minim.",
            'recommendation' => "Kami merekomendasikan Kerangka Kebijakan 4 Pilar Kesehatan Mental Kampus:\n\n**Pilar 1 — Regulasi & Standarisasi:**\n- Kemendikbudristek menerbitkan Permendikbud tentang Standar Minimum Layanan Kesehatan Mental di Perguruan Tinggi\n- Target rasio konselor:mahasiswa minimal 1:2.000 dalam 3 tahun\n- Wajibkan alokasi minimal 2% dana kemahasiswaan untuk program kesehatan mental\n\n**Pilar 2 — Destigmatisasi & Edukasi:**\n- Integrasikan modul kesehatan mental dalam Mata Kuliah Wajib Kurikulum (MKWK) semester 1\n- Latih dosen wali sebagai \"mental health first aider\" untuk deteksi dini\n- Kampanye berbasis peer-to-peer menggunakan bahasa non-klinis di media sosial kampus\n\n**Pilar 3 — Infrastruktur Layanan Berjenjang:**\n- Tingkat 1: Screening rutin via platform digital terintegrasi (setiap awal semester)\n- Tingkat 2: Peer-counseling oleh mahasiswa terlatih untuk kasus ringan\n- Tingkat 3: Konseling profesional tatap muka untuk kasus sedang-berat\n- Tingkat 4: Rujukan ke fasilitas kesehatan untuk kasus krisis\n\n**Pilar 4 — Reformasi Akademik:**\n- Batasi beban maksimal 20 SKS/semester dengan opsi pengurangan tanpa sanksi akademik\n- Terapkan kebijakan \"mental health day\" — 3 hari izin per semester tanpa surat dokter\n- Fleksibilitas deadline tugas dengan sistem \"extension request\" tanpa penalti nilai",
            'template_type' => 'data-driven',
            'status' => 'approved',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subDays(3),
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(3),
        ]);

        // Endorsements for brief 4 — all 6 collaborators + ahmad as reviewer
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $bayu->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $mega->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $putri->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $sari->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $rizky->id]);
        PolicyEndorsement::create(['policy_brief_id' => $brief4->id, 'user_id' => $ahmad->id]);

        // ══════════════════════════════════════════
        //  Hoax Buster — Dummy Claims & Verdicts
        // ══════════════════════════════════════════

        // Claim 1: RESOLVED — hoax (konsensus >75%, ≥10 approved verdicts)
        $hclaim1 = HoaxClaim::create([
            'user_id' => $sari->id,
            'title' => 'Konsumsi air rebusan nanas dapat membunuh sel kanker stadium 4 tanpa kemoterapi.',
            'description' => 'Pesan berantai WhatsApp yang mengklaim metode alternatif pengobatan kanker. Perlu tinjauan ahli onkologi.',
            'source_url' => 'https://wa.me/status/example',
            'source_platform' => 'whatsapp',
            'category' => 'kesehatan',
            'status' => 'resolved',
            'final_verdict' => 'hoax',
            'resolved_at' => now()->subDays(2),
            'created_at' => now()->subDays(10),
        ]);
        // 7 approved verdicts: all hoax
        foreach ([$ahmad, $putri, $rizky, $sari, $dina, $bayu, $mega] as $i => $user) {
            HoaxVerdict::create([
                'hoax_claim_id' => $hclaim1->id,
                'user_id' => $user->id,
                'verdict' => 'hoax',
                'reasoning' => 'Tidak ada bukti ilmiah yang mendukung klaim ini. WHO dan jurnal onkologi tidak mengkonfirmasi.',
                'evidence_url' => 'https://www.who.int/cancer',
                'status' => 'approved',
                'reviewed_by' => $ahmad->id,
                'reviewed_at' => now()->subDays(3),
                'created_at' => now()->subDays(8 - $i),
            ]);
        }

        // Claim 2: OPEN — technology (few verdicts)
        $hclaim2 = HoaxClaim::create([
            'user_id' => $putri->id,
            'title' => 'Universitas Indonesia menciptakan perangkat energi gratis dari limbah sungai Jakarta.',
            'description' => 'Klaim viral di Twitter/X bahwa mahasiswa UI menemukan solusi energi tanpa batas. Perlu verifikasi terkait hukum termodinamika.',
            'source_url' => 'https://twitter.com/ViralNewsID/example',
            'source_platform' => 'twitter',
            'category' => 'teknologi',
            'status' => 'open',
            'created_at' => now()->subDays(3),
        ]);
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim2->id,
            'user_id' => $ahmad->id,
            'verdict' => 'hoax',
            'reasoning' => 'Melanggar hukum termodinamika. Tidak ada publikasi jurnal dari UI yang mendukung klaim ini.',
            'evidence_url' => 'https://doi.org/10.1038/s41560-021-00898-3',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subDays(2),
            'created_at' => now()->subDays(2),
        ]);
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim2->id,
            'user_id' => $sari->id,
            'verdict' => 'hoax',
            'reasoning' => 'Saya cek di repositori UI dan tidak ada riset yang sesuai dengan klaim ini.',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subDays(1),
            'created_at' => now()->subDays(2),
        ]);
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim2->id,
            'user_id' => $dina->id,
            'verdict' => 'misleading',
            'reasoning' => 'Mungkin ada riset tentang energi terbarukan, tapi klaim "gratis" dan "tanpa batas" jelas berlebihan.',
            'status' => 'approved',
            'reviewed_by' => $rizky->id,
            'reviewed_at' => now()->subDays(1),
            'created_at' => now()->subDays(1),
        ]);
        // One pending verdict
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim2->id,
            'user_id' => $bayu->id,
            'verdict' => 'hoax',
            'reasoning' => 'Tidak mungkin secara fisika. Klaim energy gratis = perpetual motion machine.',
            'status' => 'pending',
            'created_at' => now()->subHours(6),
        ]);

        // Claim 3: OPEN — politics (mixed verdicts)
        $hclaim3 = HoaxClaim::create([
            'user_id' => $rizky->id,
            'title' => 'RUU baru mengusulkan pengawasan AI wajib di seluruh universitas negeri pada tahun 2025.',
            'description' => 'Artikel blog yang membahas bocoran draf RUU Pendidikan Tinggi. Sumber tidak jelas namun mendapat traksi tinggi di kalangan akademisi.',
            'source_url' => 'https://blog.example.com/ruu-ai-universitas',
            'source_platform' => 'website',
            'category' => 'politik',
            'status' => 'open',
            'created_at' => now()->subDays(5),
        ]);
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim3->id,
            'user_id' => $putri->id,
            'verdict' => 'misleading',
            'reasoning' => 'Ada pembahasan tentang AI di pendidikan, tapi bukan "pengawasan wajib". Draf RUU hanya menyebut kajian awal.',
            'status' => 'approved',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subDays(3),
            'created_at' => now()->subDays(4),
        ]);
        HoaxVerdict::create([
            'hoax_claim_id' => $hclaim3->id,
            'user_id' => $sari->id,
            'verdict' => 'misleading',
            'reasoning' => 'Saya cek website DPR, tidak ada RUU dengan substansi yang disebutkan klaim ini.',
            'evidence_url' => 'https://dpr.go.id/ruu',
            'status' => 'approved',
            'reviewed_by' => $ahmad->id,
            'reviewed_at' => now()->subDays(2),
            'created_at' => now()->subDays(3),
        ]);

        // Claim 4: PENDING — waiting for agent review
        $hclaim4 = HoaxClaim::create([
            'user_id' => $bayu->id,
            'title' => 'Kementerian Pendidikan mengonfirmasi penghapusan kurikulum sejarah untuk jurusan STEM.',
            'description' => 'Berita yang beredar di Facebook tentang perubahan kurikulum nasional.',
            'source_url' => 'https://facebook.com/example/post/123',
            'source_platform' => 'facebook',
            'category' => 'politik',
            'status' => 'pending',
            'created_at' => now()->subHours(12),
        ]);

        // Claim 5: OPEN — social (no verdicts yet)
        $hclaim5 = HoaxClaim::create([
            'user_id' => $mega->id,
            'title' => 'Grafik menunjukkan peningkatan 400% angka putus sekolah terkait kelelahan pembelajaran daring.',
            'description' => 'Infografis viral di Instagram yang mengklaim data dari Kemendikbud tanpa sumber resmi.',
            'source_platform' => 'instagram',
            'category' => 'sosial',
            'status' => 'open',
            'created_at' => now()->subDays(1),
        ]);
    }
}
