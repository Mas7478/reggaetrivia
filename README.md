# 🎵 Reggae Trivia API

REST API backend untuk aplikasi **Reggae Trivia**, sebuah game kuis Android berbasis Flutter yang menguji pengetahuan pemain tentang lagu-lagu reggae menggunakan **YouTube Music API** sebagai sumber data eksternal.

Backend ini dibangun menggunakan **PHP Native**, **MySQL**, dan di-hosting menggunakan **Wasmer**.

---

## 🚀 Features

- 🎮 Generate trivia questions dari YouTube Music API
- 👤 CRUD Player
- 🏆 Leaderboard dengan sistem poin akumulatif
- 📈 Skor bertambah saat jawaban benar
- 📉 Skor berkurang saat jawaban salah
- 🕒 Menyimpan waktu permainan
- 🎵 Menyimpan history lagu yang pernah muncul
- 🚫 Mencegah data leaderboard ganda
- 🚫 Mencegah history lagu ganda
- 🗑 Menghapus leaderboard & history ketika player dihapus
- 🌍 REST API JSON

---

## 🛠 Tech Stack

- PHP Native
- MySQL
- REST API
- Wasmer
- Flutter (Client)
- YouTube Music API

---

## 📂 Project Structure

```
reggaetrivia/
│
├── api/
│   ├── config.php
│   ├── game.php
│   ├── history.php
│   ├── leaderboard.php
│   └── player.php
│
└── index.php
```

---

## 📡 API Endpoints

### Game

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/game.php?keyword=reggae` | Generate trivia question |

---

### Player

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/player.php` | Get all players |
| GET | `/api/player.php?id=1` | Get player by ID |
| POST | `/api/player.php` | Create player |
| PUT | `/api/player.php` | Update player |
| DELETE | `/api/player.php?id=1` | Delete player |

---

### Leaderboard

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/leaderboard.php` | Get leaderboard |
| POST | `/api/leaderboard.php` | Save player score |

---

### Song History

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/history.php?player_id=1` | Get song history |
| POST | `/api/history.php` | Save song history |

---

## 🗄 Database

### players

```
id
nama
```

### leaderboard

```
id
player_id
skor
total_soal
benar
waktu_main
```

### song_history

```
id
player_id
youtube_id
judul
artis
thumbnail
shown_at
```

---

## 🎮 Game Rules

- Benar : +10 poin
- Salah : -5 poin
- Skor minimum : 0 poin
- Leaderboard menggunakan sistem poin akumulatif.
- Setiap player hanya memiliki satu data leaderboard.
- History lagu tidak akan tersimpan dua kali.

---

## 🌐 External API

Project ini menggunakan:

**YouTube Music API**

https://github.com/2004durgesh/yt-music-api

---

## 📄 License

This project is for educational purposes.
