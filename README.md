# **Absensi Bot System – PHP, MySQL, Fonnte API (WA) & Telegram Bot**

Repository ini berisi source code lengkap untuk **Sistem Absensi Digital Otomatis** yang terintegrasi langsung dengan **WhatsApp** dan **Telegram**. Project ini dirancang untuk mempermudah instansi atau UMKM dalam mengelola kehadiran karyawan atau anggota secara real-time hanya melalui pesan chat, tanpa perlu mengunduh aplikasi tambahan.

Sistem ini bekerja dengan menerima pesan masuk melalui **Fonnte API** (untuk WhatsApp) dan **BotFather** (untuk Telegram). Pesan tersebut diteruskan ke server lokal melalui **Ngrok**, diproses oleh **PHP**, dan data kehadiran disimpan secara otomatis ke dalam **database MySQL**. Admin dapat memantau seluruh data kehadiran melalui dashboard berbasis web yang responsif.

## **🔥 Fitur Utama**
- **Dual Platform:** Mendukung absensi via WhatsApp dan Telegram secara simultan.
- **Integrasi Fonnte:** Pengiriman dan penerimaan pesan WhatsApp otomatis yang stabil.
- **Bot Telegram:** Respons cepat menggunakan Telegram Bot API.
- **Akses Publik Ngrok:** Memungkinkan server lokal menerima webhook dari internet.
- **Dashboard Admin:** Monitoring data kehadiran, rekapitulasi, dan manajemen user.
- **Real-time Processing:** Data langsung tersimpan di MySQL saat pesan diterima.
- **Laporan Otomatis:** Sistem mencatat waktu masuk dan keluar secara akurat.

## **🛠️ Teknologi yang Digunakan**
- **PHP** – Backend pemrosesan logika dan API.
- **MySQL** – Penyimpanan database kehadiran dan user.
- **Fonnte API** – Gateway untuk integrasi WhatsApp.
- **Telegram API** – Bot messenger untuk platform Telegram.
- **Ngrok** – Tunneling untuk akses webhook di lingkungan development lokal.
- **HTML/CSS/Bootstrap** – Interface dashboard admin.

## **🚀 Arsitektur & Cara Kerja**
1. **User Layer:** Pengguna mengirim chat absensi ke WhatsApp atau Telegram.
2. **Gateway Layer:** Fonnte atau Telegram API menangkap pesan tersebut.
3. **Tunneling:** Ngrok meneruskan data (webhook) dari internet ke server PHP lokal Anda.
4. **Logic Layer:** PHP memvalidasi identitas user dan mencatat jam absen.
5. **Data Layer:** Data kehadiran disimpan ke database MySQL.
6. **Admin Layer:** Admin memantau laporan kehadiran melalui dashboard web.

## **📺 Tutorial Lengkap**
Penjelasan detail mengenai alur kerja sistem, demonstrasi fitur, dan implementasi tanpa coding teknis dapat dilihat di:

- **YouTube Video:** https://www.youtube.com/watch?v=Qz9tmyu7Rnw
- **Website:** https://www.bisangoding.id

Tutorial mencakup:
- Demonstrasi alur kerja absensi dari sisi pengguna.
- Cara menghubungkan Fonnte dan Telegram ke sistem.
- Penggunaan Ngrok untuk membuat server lokal online.
- Cara admin memantau data di dashboard.

## **⚖️ Hak Cipta & Lisensi**
Hak Cipta (c) 2025 **Ikmal Maulana | Absensi Bot System**

Aplikasi ini bersifat **Open Source** dan dilisensikan di bawah **GNU General Public License v3.0**.

* **Atribusi:** Anda wajib tetap menyertakan atribusi pengembang asli dalam setiap salinan atau distribusi.
* **Copyleft:** Jika Anda memodifikasi atau membangun ulang software ini, Anda wajib membagikannya kembali dengan lisensi yang sama (GPL v3.0).
* **Komersial:** Penggunaan untuk komersial diperbolehkan selama mematuhi aturan open source yang berlaku.

---
_Dibuat dengan ❤️ oleh [Ikmal Maulana](https://github.com/bisangoding-id)_
