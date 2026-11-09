(() => {
    'use strict';

    const storageKey = 'isetial_locale';
    const dictionary = {
        'Beranda': 'หน้าแรก', 'Profil': 'ข้อมูล', 'Kegiatan': 'กิจกรรม', 'Pengumuman': 'ประกาศ',
        'Tentang': 'เกี่ยวกับเรา', 'Pengurus': 'คณะกรรมการ', 'Masjid': 'มัสยิด', 'Pedoman': 'แนวทาง',
        'Jersey': 'เสื้อทีม', 'Kontak': 'ติดต่อ', 'Portal Saya': 'พอร์ทัลของฉัน', 'Keluar': 'ออกจากระบบ',
        'Login': 'เข้าสู่ระบบ', 'Buka navigasi': 'เปิดเมนู', 'Tutup navigasi': 'ปิดเมนู', 'Umum': 'ทั่วไป',
        'Petugas Umum': 'เจ้าหน้าที่ทั่วไป', 'Lelaki Pendek': 'ผู้ชายแขนสั้น', 'Lelaki Panjang': 'ผู้ชายแขนยาว',
        'Pemuda-Pemudi 5 Masjid': 'เยาวชนจาก 5 มัสยิด',
        'Bersatu · Bertumbuh · Bermanfaat': 'รวมพลัง · เติบโต · สร้างประโยชน์',
        'iSetial Wisdom menyatukan generasi muda masjid untuk belajar, berkarya, dan menghadirkan manfaat bagi masyarakat.': 'iSetial Wisdom รวมเยาวชนมัสยิดให้เรียนรู้ สร้างสรรค์ และทำประโยชน์เพื่อชุมชน',
        'Pesan Jersey': 'สั่งเสื้อทีม', 'Cek Status Pesanan': 'ตรวจสอบสถานะคำสั่งซื้อ',
        'Lima Masjid, Satu Gerakan': 'ห้ามัสยิด หนึ่งพลังขับเคลื่อน', 'Kunjungi portal': 'เยี่ยมชมพอร์ทัล',
        'Kegiatan Terbaru': 'กิจกรรมล่าสุด', 'Kegiatan segera diumumkan.': 'กิจกรรมใหม่จะประกาศเร็ว ๆ นี้',
        'Tentang iSetial Wisdom': 'เกี่ยวกับ iSetial Wisdom', 'Visi': 'วิสัยทัศน์', 'Misi': 'พันธกิจ',
        'Wadah kolaborasi Pemuda-Pemudi dari lima masjid yang membangun generasi berilmu, berakhlak, dan bermanfaat.': 'พื้นที่ความร่วมมือของเยาวชนจากห้ามัสยิด เพื่อสร้างคนรุ่นใหม่ที่มีความรู้ คุณธรรม และเป็นประโยชน์',
        'Menjadi gerakan pemuda masjid yang solid, bertumbuh, dan memberi dampak.': 'เป็นขบวนการเยาวชนมัสยิดที่เข้มแข็ง เติบโต และสร้างผลกระทบเชิงบวก',
        'Menguatkan ukhuwah, mengembangkan kapasitas anggota, dan menyelenggarakan kegiatan sosial-keagamaan.': 'เสริมสร้างความสัมพันธ์ พัฒนาศักยภาพสมาชิก และจัดกิจกรรมเพื่อสังคมและศาสนา',
        'Hubungi Kami': 'ติดต่อเรา', 'Untuk kolaborasi dan informasi kegiatan, hubungi pengurus iSetial Wisdom melalui kanal resmi organisasi.': 'สำหรับความร่วมมือและข้อมูลกิจกรรม โปรดติดต่อคณะกรรมการ iSetial Wisdom ผ่านช่องทางอย่างเป็นทางการ',
        'Portal 5 Masjid': 'พอร์ทัล 5 มัสยิด', 'Portal Masjid': 'พอร์ทัลมัสยิด',
        'Belum ada kegiatan.': 'ยังไม่มีกิจกรรม', 'Belum ada pengumuman.': 'ยังไม่มีประกาศ',
        'Pedoman Organisasi': 'แนวทางองค์กร', 'Pedoman belum diterbitkan.': 'ยังไม่ได้เผยแพร่แนวทาง',
        'Struktur Pengurus': 'โครงสร้างคณะกรรมการ', 'Susunan kepengurusan': 'รายชื่อคณะกรรมการ',
        'Bidang-Bidang': 'ฝ่ายต่าง ๆ', 'Data pengurus sedang diperbarui': 'กำลังปรับปรุงข้อมูลคณะกรรมการ',
        'Susunan periode aktif akan tampil di halaman ini.': 'รายชื่อคณะกรรมการวาระปัจจุบันจะแสดงในหน้านี้',
        'Login Anggota': 'เข้าสู่ระบบสมาชิก', 'Kata sandi': 'รหัสผ่าน', 'Ingat saya': 'จดจำฉัน', 'Masuk': 'เข้าสู่ระบบ',

        'Jersey Resmi iSetial Wisdom': 'เสื้อทีมอย่างเป็นทางการ iSetial Wisdom', 'Pilih jersey favoritmu': 'เลือกเสื้อทีมที่คุณชอบ',
        'Tentukan model, ukuran, dan jumlah. Simpan ke keranjang atau langsung checkout.': 'เลือกรุ่น ขนาด และจำนวน แล้วเพิ่มลงตะกร้าหรือชำระเงินได้ทันที',
        'Keranjang': 'ตะกร้า', 'Buka keranjang': 'เปิดตะกร้า', 'Pemesanan belum dibuka.': 'ยังไม่เปิดรับคำสั่งซื้อ',
        'Pratinjau jersey': 'ตัวอย่างเสื้อทีม', 'Lihat detail foto jersey': 'ดูรายละเอียดรูปเสื้อทีม',
        'Model sebelumnya': 'รุ่นก่อนหน้า', 'Model berikutnya': 'รุ่นถัดไป', 'Pilih model': 'เลือกรุ่น',
        'Lengan pendek': 'แขนสั้น', 'Lengan panjang': 'แขนยาว', 'Ukuran': 'ขนาด', 'Jumlah': 'จำนวน',
        'Stok tersedia': 'มีสินค้า', 'Stok habis': 'สินค้าหมด', 'Tambah ke Keranjang': 'เพิ่มลงตะกร้า',
        'Beli Sekarang': 'ซื้อเลย', 'Menyimpan…': 'กำลังบันทึก…', 'Gagal menyimpan jersey.': 'บันทึกเสื้อทีมไม่สำเร็จ',
        'Keranjang belanja': 'ตะกร้าสินค้า', 'Jersey pilihan Anda': 'เสื้อทีมที่คุณเลือก',
        'Ubah jumlah atau hapus jersey sebelum melanjutkan pembayaran.': 'แก้ไขจำนวนหรือลบสินค้า ก่อนดำเนินการชำระเงิน',
        '+ Tambah jersey lain': '+ เพิ่มเสื้อทีมอีก', 'Keranjang masih kosong': 'ตะกร้ายังว่าง',
        'Pilih model jersey terlebih dahulu.': 'กรุณาเลือกรุ่นเสื้อทีมก่อน', 'Pilih jersey': 'เลือกเสื้อทีม', 'Hapus': 'ลบ',
        'Ringkasan belanja': 'สรุปการสั่งซื้อ', 'Total item': 'จำนวนทั้งหมด', 'Total': 'รวม',
        'Bayar penuh sekarang →': 'ชำระเต็มจำนวนตอนนี้ →', 'Bayar sekarang →': 'ชำระเงินตอนนี้ →',

        'Checkout Jersey': 'ชำระเงินค่าเสื้อทีม', 'Selesaikan pesanan jersey': 'ดำเนินการสั่งเสื้อทีมให้เสร็จ',
        'Isi data pemesan, bayar melalui PromptPay, lalu unggah bukti.': 'กรอกข้อมูลผู้สั่งซื้อ ชำระผ่าน PromptPay แล้วอัปโหลดหลักฐาน',
        '1. Data pemesan': '1. ข้อมูลผู้สั่งซื้อ', '2. Bayar & bukti': '2. ชำระเงินและหลักฐาน',
        'Lengkapi data warga terlebih dahulu, kemudian lanjutkan ke pembayaran.': 'กรอกข้อมูลผู้สั่งซื้อก่อน แล้วจึงดำเนินการชำระเงิน',
        '1. Data warga': '1. ข้อมูลผู้สั่งซื้อ', '2. Bayar & unggah bukti': '2. ชำระเงินและอัปโหลดหลักฐาน',
        'Data warga': 'ข้อมูลผู้สั่งซื้อ', 'Semua kolom wajib': 'ต้องกรอกทุกช่อง', 'Wajib diisi': 'จำเป็นต้องกรอก', 'Nama lengkap': 'ชื่อ-นามสกุล',
        'Nama pemesan': 'ชื่อผู้สั่งซื้อ', 'Contoh: 08xxxxxxxx': 'ตัวอย่าง: 08xxxxxxxx',
        'Kampung, nomor rumah, dan keterangan lain': 'หมู่บ้าน เลขที่บ้าน และรายละเอียดเพิ่มเติม',
        'Nomor HP/WhatsApp': 'หมายเลขโทรศัพท์/WhatsApp', 'Alamat lengkap': 'ที่อยู่โดยละเอียด',
        'Detail jersey': 'รายละเอียดเสื้อทีม', 'Ringkasan pesanan': 'สรุปคำสั่งซื้อ',
        'Lanjut ke Pembayaran →': 'ไปชำระเงิน →', 'Lanjut Bayar →': 'ไปชำระเงิน →', 'Kembali ke keranjang': 'กลับไปที่ตะกร้า',
        '← Ubah data pemesan': '← แก้ไขข้อมูลผู้สั่งซื้อ',
        'Bayar melalui PromptPay': 'ชำระผ่าน PromptPay',
        'Pindai QR. Nomor tujuan dan nominal sudah terisi otomatis.': 'สแกน QR ระบบกรอกหมายเลขผู้รับและยอดเงินให้อัตโนมัติแล้ว',
        'Pindai QR; nomor tujuan dan nominal lengkap sudah terisi otomatis.': 'สแกน QR ระบบกรอกหมายเลขผู้รับและยอดเงินให้อัตโนมัติแล้ว',
        'QR PromptPay dinamis': 'QR PromptPay ตามยอดชำระ',
        'Total jersey': 'ยอดค่าเสื้อทีม', 'Kode unik': 'รหัสเฉพาะ', 'Bayar tepat sebesar': 'ชำระให้ตรงจำนวน',
        'Bayar tepat': 'ยอดที่ต้องชำระ',
        'Jangan membulatkan nominal. Dua angka terakhir adalah kode pesanan Anda.': 'ห้ามปัดเศษ สองหลักสุดท้ายคือรหัสคำสั่งซื้อของคุณ',
        'Jangan membulatkan nominal. Dua angka terakhir adalah kode pesanan.': 'ห้ามปัดเศษ สองหลักสุดท้ายคือรหัสคำสั่งซื้อ',
        '📷 Unggah Bukti & Selesaikan Pesanan': '📷 อัปโหลดหลักฐานและเสร็จสิ้นคำสั่งซื้อ',
        '📷 Unggah Bukti': '📷 อัปโหลดหลักฐาน', 'Pesanan dikirim otomatis setelah bukti dipilih.': 'ระบบจะส่งคำสั่งซื้ออัตโนมัติเมื่อเลือกหลักฐานแล้ว',
        'Setelah foto dipilih, pesanan langsung dikirim.': 'เมื่อเลือกรูปแล้ว ระบบจะส่งคำสั่งซื้อทันที',
        'Total pembayaran': 'ยอดชำระทั้งหมด',
        'Setelah membayar, unggah bukti. Pesanan akan langsung dikirim.': 'หลังชำระเงิน ให้อัปโหลดหลักฐาน แล้วระบบจะส่งคำสั่งซื้อทันที',
        'Setelah membayar, unggah bukti melalui tombol di samping. Pesanan akan langsung selesai.': 'หลังชำระเงิน ให้อัปโหลดหลักฐานด้วยปุ่มด้านข้าง แล้วคำสั่งซื้อจะเสร็จสมบูรณ์ทันที',
        '← Kembali ke data warga': '← กลับไปยังข้อมูลผู้สั่งซื้อ', '← Kembali ke data pemesan': '← กลับไปยังข้อมูลผู้สั่งซื้อ', 'Mengirim pesanan…': 'กำลังส่งคำสั่งซื้อ…',
        'Bukti pembayaran wajib diunggah.': 'กรุณาอัปโหลดหลักฐานการชำระเงิน', 'The proof field is required.': 'กรุณาอัปโหลดหลักฐานการชำระเงิน',

        '← Pemesanan jersey': '← การสั่งเสื้อทีม', 'Status pesanan': 'สถานะคำสั่งซื้อ', 'Salin nomor': 'คัดลอกเลขที่',
        'Tersalin ✓': 'คัดลอกแล้ว ✓', 'Total dibayar': 'ยอดชำระ', 'Pembayaran': 'การชำระเงิน', 'Produksi': 'การผลิต',
        'Menunggu verifikasi': 'รอตรวจสอบ', 'Pembayaran diterima': 'รับชำระเงินแล้ว',
        'Bukti pembayaran ditolak': 'หลักฐานการชำระเงินถูกปฏิเสธ', 'Belum ada pembayaran': 'ยังไม่มีการชำระเงิน',
        'Bukti pembayaran penuh sudah terkirim dan sedang diperiksa admin.': 'ส่งหลักฐานการชำระเต็มจำนวนแล้ว และผู้ดูแลกำลังตรวจสอบ',
        'Pembayaran penuh sudah diverifikasi. Tidak ada tagihan tambahan.': 'ตรวจสอบการชำระเต็มจำนวนแล้ว ไม่มีค่าใช้จ่ายเพิ่มเติม',
        'Hubungi petugas untuk memperbaiki bukti pembayaran.': 'โปรดติดต่อเจ้าหน้าที่เพื่อแก้ไขหลักฐานการชำระเงิน',
        'Hubungi petugas yang membuat pesanan ini.': 'โปรดติดต่อเจ้าหน้าที่ที่สร้างคำสั่งซื้อนี้',
        'Pesanan diterima': 'รับคำสั่งซื้อแล้ว', 'Sedang diproduksi': 'กำลังผลิต', 'Siap diambil': 'พร้อมรับ',
        'Sudah diserahkan': 'ส่งมอบแล้ว', 'Dibatalkan': 'ยกเลิกแล้ว', 'Diterima': 'รับแล้ว', 'Diproduksi': 'กำลังผลิต', 'Selesai': 'เสร็จสิ้น',
        'Pesanan sudah masuk dan menunggu giliran produksi.': 'ได้รับคำสั่งซื้อแล้วและกำลังรอคิวผลิต',
        'Jersey sedang disiapkan oleh tim produksi.': 'ทีมผลิตกำลังจัดเตรียมเสื้อทีม',
        'Jersey sudah siap untuk diambil atau diserahkan.': 'เสื้อทีมพร้อมรับหรือส่งมอบแล้ว',
        'Pesanan telah selesai dan diserahkan.': 'คำสั่งซื้อเสร็จสิ้นและส่งมอบแล้ว', 'Pesanan ini telah dibatalkan.': 'คำสั่งซื้อนี้ถูกยกเลิกแล้ว',
        'Perjalanan pesanan': 'ความคืบหน้าคำสั่งซื้อ', 'Pesanan': 'คำสั่งซื้อ', 'Rincian jersey': 'รายละเอียดเสื้อทีม',
        'Ringkasan pembayaran': 'สรุปการชำระเงิน', 'Data pemesan': 'ข้อมูลผู้สั่งซื้อ',
        'Dilayani oleh petugas': 'ดูแลโดยเจ้าหน้าที่', 'Butuh bantuan?': 'ต้องการความช่วยเหลือ?',
        'Sebutkan nomor pesanan': 'แจ้งเลขที่คำสั่งซื้อ', 'kepada petugas atau admin.': 'แก่เจ้าหน้าที่หรือผู้ดูแลระบบ',
        'Cek pesanan jersey': 'ตรวจสอบคำสั่งซื้อเสื้อทีม', 'Masukkan nomor pesanan': 'กรอกเลขที่คำสั่งซื้อ', 'Cari pesanan': 'ค้นหาคำสั่งซื้อ',

        'Kelola Jersey': 'จัดการเสื้อทีม', 'Administrasi penjualan': 'การจัดการการขาย',
        'Periksa pembayaran, pantau produksi, dan atur stok dari satu tempat.': 'ตรวจสอบการชำระเงิน ติดตามการผลิต และจัดการสต็อกได้ในที่เดียว',
        '← Dashboard': '← แดชบอร์ด', 'Semua pesanan': 'คำสั่งซื้อทั้งหมด', 'Perlu diverifikasi': 'รอตรวจสอบ',
        'Antrean + proses': 'อยู่ในคิว + กำลังดำเนินการ', 'Antrean': 'อยู่ในคิว', 'Diproses': 'กำลังดำเนินการ',
        'Diserahkan': 'ส่งมอบแล้ว', 'Belum Bayar': 'ยังไม่ชำระ', 'Cicilan': 'ผ่อนชำระ', 'Lunas': 'ชำระครบแล้ว',
        'Ringkasan dana dan model jersey': 'สรุปยอดเงินและรุ่นเสื้อทีม',
        'Total dana diterima': 'ยอดเงินที่ได้รับทั้งหมด', 'Pembayaran terverifikasi': 'การชำระเงินที่ตรวจสอบแล้ว',
        'Lelaki panjang': 'ผู้ชายแขนยาว', 'Lelaki pendek': 'ผู้ชายแขนสั้น', 'Muslimah': 'มุสลิมะฮ์',
        'jersey dipesan': 'เสื้อที่สั่ง', 'Petugas': 'เจ้าหน้าที่', 'Produk & stok': 'สินค้าและสต็อก',
        'Cari pesanan': 'ค้นหาคำสั่งซื้อ', 'Cari nomor, nama, atau HP…': 'ค้นหาเลขที่ ชื่อ หรือเบอร์โทร…',
        'Status produksi': 'สถานะการผลิต', 'Semua produksi': 'สถานะการผลิตทั้งหมด',
        'Status pembayaran': 'สถานะการชำระเงิน', 'Semua pembayaran': 'การชำระเงินทั้งหมด',
        'Terapkan': 'ใช้ตัวกรอง', 'Hapus filter': 'ล้างตัวกรอง', 'Pemesan': 'ผู้สั่งซื้อ', 'Item': 'รายการ',
        'Tanggal': 'วันที่', 'Aksi': 'การดำเนินการ', 'Simpan': 'บันทึก', 'Detail': 'รายละเอียด', 'Tutup': 'ปิด',
        'Rincian pesanan': 'รายละเอียดคำสั่งซื้อ', 'Alamat:': 'ที่อยู่:', 'Menunggu': 'รอตรวจสอบ',
        'Ditolak': 'ปฏิเสธแล้ว', 'Lihat bukti ↗': 'ดูหลักฐาน ↗', 'Catatan': 'หมายเหตุ',
        'Catatan (opsional)': 'หมายเหตุ (ไม่บังคับ)', 'Tolak': 'ปฏิเสธ', 'Terima': 'ยืนยัน',
        'Tolak bukti pembayaran ini?': 'ปฏิเสธหลักฐานการชำระเงินนี้หรือไม่?', 'Terima pembayaran ini?': 'ยืนยันการชำระเงินนี้หรือไม่?',
        'Belum ada pembayaran.': 'ยังไม่มีการชำระเงิน', 'Catatan internal': 'หมายเหตุภายใน',
        'Contoh: menunggu konfirmasi': 'ตัวอย่าง: รอการยืนยัน', 'Simpan perubahan': 'บันทึกการเปลี่ยนแปลง',
        'Simpan status': 'บันทึกสถานะ', 'Hapus pesanan': 'ลบคำสั่งซื้อ',
        'Pesanan tidak ditemukan': 'ไม่พบคำสั่งซื้อ', 'Coba ubah pencarian atau filter.': 'ลองเปลี่ยนคำค้นหาหรือตัวกรอง',
        'Pendek': 'แขนสั้น', 'Panjang': 'แขนยาว',

        'Tambah petugas': 'เพิ่มเจ้าหน้าที่', 'Maksimal 10 orang. Setiap petugas mendapat link pribadi.': 'สูงสุด 10 คน เจ้าหน้าที่แต่ละคนจะได้รับลิงก์ส่วนตัว',
        'Nama': 'ชื่อ', 'Nomor HP': 'หมายเลขโทรศัพท์', 'Batas 10 petugas tercapai': 'ครบจำนวนเจ้าหน้าที่ 10 คนแล้ว',
        'Buat link petugas': 'สร้างลิงก์เจ้าหน้าที่', 'Tanpa nomor HP': 'ไม่มีหมายเลขโทรศัพท์',
        'Aktif': 'ใช้งาน', 'Nonaktif': 'ปิดใช้งาน', 'Salin': 'คัดลอก', 'Simpan petugas': 'บันทึกเจ้าหน้าที่',
        'Belum ada petugas.': 'ยังไม่มีเจ้าหน้าที่',
        'Dashboard Petugas Jersey': 'แดชบอร์ดเจ้าหน้าที่เสื้อทีม', 'Dashboard petugas jersey': 'แดชบอร์ดเจ้าหน้าที่เสื้อทีม',
        'Hanya menampilkan pesanan yang masuk melalui link Anda.': 'แสดงเฉพาะคำสั่งซื้อที่มาจากลิงก์ของคุณ',
        'Buka halaman pemesanan': 'เปิดหน้าสั่งซื้อ', 'Dana diterima': 'ยอดเงินที่ได้รับ',
        'Bukti ditolak': 'หลักฐานถูกปฏิเสธ',
        'Belum ada pesanan melalui link Anda atau filter tidak cocok.': 'ยังไม่มีคำสั่งซื้อผ่านลิงก์ของคุณ หรือตัวกรองไม่ตรงกัน',
        'Link pemesanan untuk warga': 'ลิงก์สั่งซื้อสำหรับลูกค้า', 'Link dashboard pribadi petugas': 'ลิงก์แดชบอร์ดส่วนตัวของเจ้าหน้าที่',

        'Tambah produk': 'เพิ่มสินค้า', 'Buat jenis jersey beserta ukuran awalnya.': 'สร้างประเภทเสื้อทีมพร้อมขนาดเริ่มต้น',
        'Nama produk': 'ชื่อสินค้า', 'Jersey Dewasa': 'เสื้อทีมผู้ใหญ่', 'Harga dasar (THB)': 'ราคาพื้นฐาน (THB)',
        'Ukuran awal': 'ขนาดเริ่มต้น', 'Pisahkan dengan koma.': 'คั่นด้วยเครื่องหมายจุลภาค', 'Deskripsi': 'รายละเอียด',
        'Tampilkan ke pembeli': 'แสดงให้ผู้ซื้อเห็น', 'Simpan produk': 'บันทึกสินค้า',
        'Ukuran & stok': 'ขนาดและสต็อก', 'Tak terbatas': 'ไม่จำกัด', 'Ukuran baru': 'ขนาดใหม่',
        'Tambahan harga': 'ราคาเพิ่ม', '+ Ukuran': '+ เพิ่มขนาด'
    };

    const patterns = [
        [/^Petugas (.+)$/u, 'เจ้าหน้าที่ $1'], [/^Periode (.+)$/u, 'วาระ $1'],
        [/^© (\d{4}) iSetial Wisdom · Pemuda-Pemudi 5 Masjid$/u, '© $1 iSetial Wisdom · เยาวชนจาก 5 มัสยิด'],
        [/^(.+) · Lelaki Pendek · Pendek · (.+)$/u, '$1 · ผู้ชายแขนสั้น · แขนสั้น · $2'],
        [/^(.+) · Lelaki Panjang · Panjang · (.+)$/u, '$1 · ผู้ชายแขนยาว · แขนยาว · $2'],
        [/^(.+) · Muslimah · Panjang · (.+)$/u, '$1 · มุสลิมะฮ์ · แขนยาว · $2'],
        [/^(.+) · Lelaki Pendek · Lengan pendek · Ukuran (.+)$/u, '$1 · ผู้ชายแขนสั้น · แขนสั้น · ขนาด $2'],
        [/^(.+) · Lelaki Panjang · Lengan panjang · Ukuran (.+)$/u, '$1 · ผู้ชายแขนยาว · แขนยาว · ขนาด $2'],
        [/^(.+) · Muslimah · Lengan panjang · Ukuran (.+)$/u, '$1 · มุสลิมะฮ์ · แขนยาว · ขนาด $2'],
        [/^(\d+) jersey$/u, '$1 ตัว'], [/^Total (\d+) jersey$/u, 'รวม $1 ตัว'], [/^(\d+) buah$/u, '$1 ชิ้น'],
        [/^Ukuran (.+)$/u, 'ขนาด $1'], [/^Sebutkan nomor pesanan (.+) kepada petugas atau admin\.$/u, 'แจ้งเลขที่คำสั่งซื้อ $1 แก่เจ้าหน้าที่หรือผู้ดูแลระบบ'],
        [/^(.+) · Lengan pendek · Ukuran (.+)$/u, '$1 · แขนสั้น · ขนาด $2'],
        [/^(.+) · Lengan panjang · Ukuran (.+)$/u, '$1 · แขนยาว · ขนาด $2'],
        [/^(.+) · Ukuran (.+) · (\d+) buah$/u, '$1 · ขนาด $2 · $3 ชิ้น'],
        [/^Buka keranjang, (\d+) item$/u, 'เปิดตะกร้า, $1 รายการ'],
        [/^Petugas \((\d+)\/10\)$/u, 'เจ้าหน้าที่ ($1/10)'],
        [/^Menampilkan (\d+) pesanan$/u, 'แสดง $1 คำสั่งซื้อ'],
        [/^(\d+) bukti baru$/u, 'หลักฐานใหม่ $1 รายการ'],
        [/^Petugas: (.+)$/u, 'เจ้าหน้าที่: $1'],
        [/^Rincian pesanan \((\d+) jersey\)$/u, 'รายละเอียดคำสั่งซื้อ ($1 ตัว)'],
        [/^(.+) · (\d+) pesanan$/u, '$1 · $2 คำสั่งซื้อ'],
        [/^Status produksi (.+)$/u, 'สถานะการผลิต $1'],
        [/^Hapus pesanan (.+)\? Data akan diarsipkan dan tidak tampil lagi\.$/u, 'ลบคำสั่งซื้อ $1 หรือไม่? ข้อมูลจะถูกเก็บถาวรและไม่แสดงอีก'],
        [/^(\d{1,2}) (Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des) (\d{4})(.*)$/u, (all, day, month, year, rest) => `${day} ${{Jan:'ม.ค.',Feb:'ก.พ.',Mar:'มี.ค.',Apr:'เม.ย.',Mei:'พ.ค.',Jun:'มิ.ย.',Jul:'ก.ค.',Agu:'ส.ค.',Sep:'ก.ย.',Okt:'ต.ค.',Nov:'พ.ย.',Des:'ธ.ค.'}[month]} ${Number(year) + 543}${rest}`]
    ];
    const originalText = new WeakMap();
    const originalAttributes = new WeakMap();
    const originalTitle = document.title;
    let locale = localStorage.getItem(storageKey) === 'th' ? 'th' : 'id';
    let applying = false;

    const translate = value => {
        const trimmed = value.trim();
        if (!trimmed) return value;
        let result = dictionary[trimmed];
        if (!result) {
            for (const [pattern, replacement] of patterns) {
                if (pattern.test(trimmed)) { result = trimmed.replace(pattern, replacement); break; }
            }
        }
        return result ? value.replace(trimmed, result) : value;
    };

    const blocked = node => node.parentElement?.closest('script, style, noscript, [data-no-translate]');

    function applyText(node, refresh = false) {
        if (blocked(node)) return;
        if (refresh && originalText.has(node)) {
            const expected = locale === 'th' ? translate(originalText.get(node)) : originalText.get(node);
            if (node.nodeValue === expected) return;
        }
        if (refresh || !originalText.has(node)) originalText.set(node, node.nodeValue);
        const value = locale === 'th' ? translate(originalText.get(node)) : originalText.get(node);
        if (node.nodeValue !== value) node.nodeValue = value;
    }

    function applyElement(element, refresh = false) {
        if (!(element instanceof Element) || element.closest('[data-no-translate]')) return;
        const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) applyText(walker.currentNode, refresh);
        const targets = [element, ...element.querySelectorAll('[placeholder], [aria-label], [title], [alt]')];
        targets.forEach(target => {
            const saved = originalAttributes.get(target) || {};
            ['placeholder', 'aria-label', 'title', 'alt'].forEach(attribute => {
                if (!target.hasAttribute(attribute)) return;
                if (refresh || saved[attribute] === undefined) saved[attribute] = target.getAttribute(attribute);
                const value = locale === 'th' ? translate(saved[attribute]) : saved[attribute];
                if (target.getAttribute(attribute) !== value) target.setAttribute(attribute, value);
            });
            originalAttributes.set(target, saved);
        });
    }

    function updateButtons() {
        document.querySelectorAll('[data-locale]').forEach(button => {
            const active = button.dataset.locale === locale;
            button.classList.toggle('bg-amber-400', active);
            button.classList.toggle('text-emerald-950', active);
            button.classList.toggle('text-emerald-100', !active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }

    function applyPage() {
        applying = true;
        document.documentElement.lang = locale === 'th' ? 'th' : 'id';
        document.title = locale === 'th' ? translate(originalTitle) : originalTitle;
        applyElement(document.body);
        updateButtons();
        applying = false;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-locale]').forEach(button => button.addEventListener('click', () => {
            locale = button.dataset.locale === 'th' ? 'th' : 'id';
            localStorage.setItem(storageKey, locale);
            applyPage();
        }));
        applyPage();
        new MutationObserver(mutations => {
            if (applying) return;
            applying = true;
            mutations.forEach(mutation => {
                if (mutation.type === 'characterData') applyText(mutation.target, true);
                mutation.addedNodes.forEach(node => node.nodeType === Node.TEXT_NODE ? applyText(node) : applyElement(node));
            });
            applying = false;
        }).observe(document.body, {childList: true, characterData: true, subtree: true});
    });

    window.isetialTranslate = text => locale === 'th' ? translate(text) : text;
    const nativeConfirm = window.confirm.bind(window);
    window.confirm = message => nativeConfirm(window.isetialTranslate(String(message)));
})();
