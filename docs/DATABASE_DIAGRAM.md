# Database Diagram - Aspirasi Siswa

## Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────────┐
│                          STUDENTS                                   │
├─────────────────────────────────────────────────────────────────────┤
│ PK: id_student                                                      │
│ - nis (UNIQUE)                                                      │
│ - username (UNIQUE)                                                 │
│ - password                                                          │
│ - class                                                             │
│ - created_at / updated_at                                           │
└────────────────┬────────────────────────────────────────────────────┘
                 │ 1:N
                 │ (input_by)
                 └─────────────────────────┐
                                          │
          ┌───────────────────────────────v──────────────────────────────┐
          │                    INPUT_ASPIRATIONS                         │
          ├────────────────────────────────────────────────────────────────┤
          │ PK: id_input                                                 │
          │ FK: input_by (students.id_student)                           │
          │ FK: id_category (categories.id_category)                     │
          │ FK: id_location (locations.id_location)                      │
          │ - title                                                      │
          │ - description                                               │
          │ - location                                                  │
          │ - room_number (nullable)                                    │
          │ - submission_mode (template / custom)                       │
          │ - custom_location (nullable)                                │
          │ - custom_context (nullable)                                 │
          │ - submission_status (menunggu/diterima/ditolak)             │
          │ - image (nullable)                                          │
          │ - admin_message (nullable)                                  │
          │ - rating (1-5, nullable)                                    │
          │ - feedback (text, nullable)                                 │
          │ - input_at                                                  │
          │ - created_at / updated_at                                   │
          └─────────┬──────────────┬──────────────┬──────────────┬──────┘
                    │              │              │              │
           (id_category)    (id_location)  (1:1 diterima)    (1:N votes)
                    │              │              │              │
        ┌───────────v──┐  ┌────────v─────┐  ┌──────v────────────┐  │
        │  CATEGORIES  │  │  LOCATIONS   │  │   ASPIRATIONS     │  │
        ├──────────────┤  ├──────────────┤  ├───────────────────┤  │
        │PK:id_categor-│  │PK:id_locatio-│  │PK:id_aspiration  │  │
        │ory           │  │ n            │  │FK:id_input       │  │
        │- category_   │  │- location_   │  │- progress_status │  │
        │  name        │  │  name        │  │- priority_level  │  │
        │- description │  │- location_   │  │- start_at        │  │
        │              │  │  type        │  │- end_at          │  │
        │              │  │- is_active   │  │- deadline        │  │
        │              │  │              │  │- progress_      │  │
        │              │  │              │  │  evidence_image  │  │
        │              │  │              │  │- progress_      │  │
        │              │  │              │  │  description     │  │
        │              │  │              │  │- validated_by    │  │
        │              │  │              │  │- validated_at    │  │
        │              │  │              │  │- created_at /    │  │
        │              │  │              │  │  updated_at      │  │
        └──────────────┘  └──────────────┘  └──────┬───────────┘  │
                                                    │               │
                                          (1:N feedback/comment)    │
                                                    │               │
                                             ┌──────v──────┐        │
                                             │  FEEDBACKS  │        │
                                             ├─────────────┤        │
                                             │PK:id_feedback        │
                                             │FK:id_aspirat-│        │
                                             │   ion        │        │
                                             │FK:user_id    │        │
                                             │- message     │        │
                                             │- feedback_at │        │
                                             └──────────────┘        │
                                                                    │
                                           ┌────────────────────────v───┐
                                           │         VOTES              │
                                           ├────────────────────────────┤
                                           │PK:id_vote                  │
                                           │FK:aspiration_id            │
                                           │FK:student_id               │
                                           │- created_at                │
                                           │UNIQUE(aspiration_id, studet)
                                           └────────────────────────────┘
```

## Penjelasan Relasi:

### 1. **STUDENTS → INPUT_ASPIRATIONS (1:N)**
- Satu siswa bisa membuat banyak aspirasi
- Foreign key: `input_by` reference ke `students.id_student`

### 2. **CATEGORIES → INPUT_ASPIRATIONS (1:N)**
- Satu kategori bisa terkait banyak aspirasi
- Contoh kategori: Sarana, Kebersihan, Keamanan, Fasilitas IT, Prasarana

### 3. **LOCATIONS → INPUT_ASPIRATIONS (1:N)**
- Satu lokasi bisa terkait banyak aspirasi template
- Lokasi dibedakan berdasarkan tipe (kelas, ruang, fasilitas umum)

### 4. **INPUT_ASPIRATIONS → ASPIRATIONS (1:1)**
- Hanya aspirasi yang diterima (approved) yang membuat record di ASPIRATIONS
- Aspirasi yang ditolak tidak ada di table ini

### 5. **ASPIRATIONS → FEEDBACKS (1:N)**
- Satu aspirasi bisa punya banyak feedback/update dari admin
- Feedback mencatat setiap progress update

### 6. **ASPIRATIONS → VOTES (1:N)**
- Siswa bisa vote up aspirasi yang sudah approved
- Vote digunakan untuk prioritas voting

## Mode Submission:

### Template Mode:
- Wajib pilih kategori dari list
- Wajib pilih lokasi formal dari sistem
- Ikut SLA (deadline tracking)
- Masuk global dashboard untuk voting

### Custom Mode:
- Lokasi bebas (custom_location) dari siswa
- Konteks custom (custom_context) untuk penjelasan prioritas
- Prioritas berdasarkan input_at (FIFO - First Input First Output)
- **TIDAK** masuk global dashboard meski approved (untuk menghindari konflik SLA)
- Dikelola terpisah di admin panel

## Status Flow:

```
menunggu (pending) 
    ├─→ [ADMIN APPROVE] → diterima (approved)
    │                         └─→ [ASPIRATIONS record created]
    │                             └─→ Progress tracking (Belum Dimulai → Dalam Proses → Selesai)
    │                                 └─→ Student bisa rating + feedback
    └─→ [ADMIN REJECT] → ditolak (rejected)
                            └─→ Message di admin_message
```
